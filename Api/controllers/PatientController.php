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
                    $doctor = new Doctor($row['Name'], $row["Surname"], $row['Gender'], $row['BirthPlace'], $row['PhoneNumber'], $row['Email'], $assigned);
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
        }
    }
}
