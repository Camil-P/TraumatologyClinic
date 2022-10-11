<?php
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Origin: *');

require_once("../config/Database.php");
require_once("../models/Response.php");
require_once("../models/Doctor.php");
require_once("../config/Auth.php");

try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
} catch (PDOException $ex) {
    $response = new Response(false, 500);
    $response->addMessage("Database conn error.");
    $response->send();

    error_log("Connection error: " . $ex->getMessage(), 0);
    exit();
}

// AUTH

$authorizedUser = authorize($writeDB);

if ($authorizedUser['role'] !== "Patient") {
    $response = new Response(false, 401);
    $response->addMessage("You are not authorized to see this content.");
    $response->send();
    exit();
}

if (array_key_exists('fetch', $_GET)) {
    $fetch = $_GET['fetch'];

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if ($fetch === "doctors") {

            try {

                $query = $readDB->prepare('SELECT
                                                Id,
                                                UserId,
                                                DoctorId
                                            FROM patient
                                            WHERE
                                                UserId = :userId;');
                $query->bindParam(':userId', $authorizedUser['id'], PDO::PARAM_INT);
                $query->execute();

                $rowCount = $query->rowCount();
                if ($rowCount === 0) {
                    $response = new Response(false, 401);
                    $response->addMessage("Patient does not exist.");
                    $response->send();
                    exit();
                }
                $patientRow = $query->fetch(PDO::FETCH_ASSOC);

                $query = $writeDB->prepare("SELECT
                                                Id,
                                                Name,
                                                Surname,
                                                Gender,
                                                BirthPlace,
                                                PhoneNumber,
                                                Email
                                            FROM user
                                            WHERE Role = 'Doctor'");
                $query->execute();

                $rowCount = $query->rowCount();
                if ($rowCount === 0) {
                    $response = new Response(false, 404);
                    $response->addMessage("No doctors were found.");
                    $response->send();
                    exit();
                }


                $response = new Response(true, 200);
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $assigned = $row['Id'] === $patientRow['DoctorId'];
                    $doctor = new Doctor($row['Id'], $row['Name'], $row["Surname"], $row['Gender'], $row['BirthPlace'], $row['PhoneNumber'], $row['Email'], $assigned);
                    $doctorArray[] = $doctor->asArray();
                }

                $response->toCache(true);
                $response->setData($doctorArray);
                $response->send();
                exit();
            } catch (DoctorException $ex) {
                $response = new Response(false, 400);
                $response->addMessage($ex->getMessage());
                $response->send();
                exit();
            } catch (PDOException $ex) {
                $response = new Response(false, 500);
                $response->addMessage("There was a problem with fetch doctors from DB: \n" . $ex->getMessage());
                $response->send();

                error_log("DB error: " . $ex->getMessage(), 0);
                exit();
            }
        } else if ($fetch === "profile") {
            try {

                $query = $writeDB->prepare("SELECT
                                                Id,
                                                Name,
                                                Surname,
                                                Gender,
                                                BirthPlace,
                                                BirthDate,
                                                PhoneNumber,
                                                Email
                                            FROM user
                                            WHERE Id = :userId");
                $query->bindParam(':userId', $authorizedUser['id'], PDO::PARAM_INT);
                $query->execute();

                $rowCount = $query->rowCount();
                if ($rowCount === 0) {
                    $response = new Response(false, 404);
                    $response->addMessage("No user was found.");
                    $response->send();
                    exit();
                }
                $row = $query->fetch(PDO::FETCH_ASSOC);


                $responseData = array();
                $responseData['name'] = $row['Name'];
                $responseData['surname'] = $row['Surname'];
                $responseData['gender'] = $row['Gender'];
                $responseData['birthPlace'] = $row['BirthPlace'];
                $responseData['birthDate'] = $row['BirthDate'];
                $responseData['phoneNumber'] = $row['PhoneNumber'];
                $responseData['email'] = $row['Email'];

                $response = new Response(true, 200);
                $response->setData($responseData);
                $response->send();
                exit();
            } catch (DoctorException $ex) {
                $response = new Response(false, 400);
                $response->addMessage($ex->getMessage());
                $response->send();
                exit();
            } catch (PDOException $ex) {
                $response = new Response(false, 500);
                $response->addMessage("There was a problem with fetch doctors from DB: \n" . $ex->getMessage());
                $response->send();

                error_log("DB error: " . $ex->getMessage(), 0);
                exit();
            }
        }
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response = new Response(false, 405);
        $response->addMessage("Method not allowed.");
        $response->send();
        exit();
    }

    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        $response = new Response(false, 400);
        $response->addMessage("Content type header not set to JSON.");
        $response->send();
        exit();
    }
    
    if (!$jsonData = json_decode(file_get_contents('php://input'))) {
        $response = new Response(false, 400);
        $response->addMessage("Request body is not valid JSON.");
        $response->send();
        exit();
    }

    try {

        $requestedDoctorsId = $jsonData->requestedDoctorsId;

        $query = $readDB->prepare('SELECT
                                        Id,
                                        UserId,
                                        DoctorId
                                    FROM patient
                                    WHERE
                                        UserId = :userId;');
        $query->bindParam(':userId', $authorizedUser['id'], PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount === 0) {
            $response = new Response(false, 401);
            $response->addMessage("Patient does not exist.");
            $response->send();
            exit();
        }
        $patientRow = $query->fetch(PDO::FETCH_ASSOC);

        $query = $readDB->prepare('SELECT *
                                    FROM assigndoctorrequest
                                    WHERE
                                        PatientId = :userId;');
        $query->bindParam(':userId', $authorizedUser['id'], PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount !== 0) {
            $response = new Response(false, 400);
            $response->addMessage("User already created one request for doctor change!");
            $response->send();
            exit();
        }
        $patientRow['DoctorId'] = $patientRow['DoctorId'] ? $patientRow['DoctorId'] : 0;
        $query = $writeDB->prepare("INSERT INTO assigndoctorrequest
                                        (PatientId,
                                        RequestDoctorId,
                                        PreviouseDoctorId)
                                    values 
                                        ({$patientRow['UserId']},
                                         {$requestedDoctorsId},
                                         {$patientRow['DoctorId']});");
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount === 0) {
            $response = new Response(false, 500);
            $response->addMessage("Unable to create a doctor change request.");
            $response->send();
            exit();
        }

        $response = new Response(true, 201);
        $response->addMessage("Successfully created doctor change request.");
        $response->send();
        exit();
    } catch (DoctorException $ex) {
        $response = new Response(false, 400);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit();
    } catch (PDOException $ex) {
        $response = new Response(false, 500);
        $response->addMessage("There was a problem with fetch doctors from DB: \n" . $ex->getMessage());
        $response->send();

        error_log("DB error: " . $ex->getMessage(), 0);
        exit();
    }
}
