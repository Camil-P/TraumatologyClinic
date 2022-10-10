<?php

header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
// header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Origin: *');

require_once("../config/Database.php");
require_once("../models/Appointment.php");
require_once("../models/Response.php");
require_once("../requestModels/CreateAppointment.php");
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

if ($authorizedUser['role'] === 'Patient') {

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
    $row = $query->fetch(PDO::FETCH_ASSOC);

    $patientId = $row['Id'];
    $doctorId = $row['DoctorId'];
} else {
    $doctorId = $authorizedUser['id'];
}

// Handle Get all Appointments by doctorId/patientId Request 

if (!array_key_exists('appointmentId', $_GET) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $query = $readDB->prepare('SELECT
                                    Id,
                                    ServiceName,
                                    Date,
                                    StartingHour,
                                    PatientId,
                                    DoctorId,
                                    Note
                                FROM appointment
                                WHERE 
                                    DoctorId = :doctorId
                                    OR PatientId = :patientId;');

        $query->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
        $query->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount === 0) {
            $response = new Response(false, 404);
            $response->addMessage("Appointments were not found.");
            $response->send();
            exit();
        }

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $appointment = new Appointment($row['Id'], $row["ServiceName"], $row['Date'], $row['StartingHour'], $row['PatientId'], $row['DoctorId'], $row['Note']);
            $appointmentArray[] = $appointment->asArray();
        }

        $response = new Response(true, 200);
        $response->toCache(true);
        $response->setData($appointmentArray);
        $response->send();
        exit();
    } catch (AppointmentException $ex) {
        $response = new Response(false, 500);
        $response->addMessage($ex->getMessage());
        $response->send();
        exit();
    } catch (PDOException $ex) {
        $response = new Response(false, 500);
        $response->addMessage("Unable to retreive appointments from DB with given params.");
        $response->send();

        error_log("Connection error: " . $ex->getMessage(), 0);
        exit();
    }
}

// Handle Methods that require appointmentId param
elseif (array_key_exists('appointmentId', $_GET) && ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'DELETE')) {

    $appointmentId = $_GET['appointmentId'];
    if ($appointmentId == '' || !is_numeric($appointmentId)) {
        $response = new Response(false, 400);
        $response->addMessage('Param appointmentId is not valid');
        $response->send();
        exit();
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case "GET":
            try {
                $query = $readDB->prepare('SELECT
                                                Id,
                                                ServiceName,
                                                Date,
                                                StartingHour,
                                                PatientId,
                                                DoctorId,
                                                Note
                                            FROM appointment
                                            WHERE 
                                                Id = :appointmentId AND
                                                (PatientId = :patientId OR DoctorId = :doctorId);');

                $query->bindParam(':appointmentId', $appointmentId, PDO::PARAM_INT);
                $query->bindParam(':patientId', $patientId, PDO::PARAM_INT);
                $query->bindParam(':doctorId', $doctorId, PDO::PARAM_INT);
                $query->execute();

                $rowCount = $query->rowCount();
                if ($rowCount === 0) {
                    $response = new Response(false, 404);
                    $response->addMessage("Appointment was not found.");
                    $response->send();
                    exit();
                }

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $appointment = new Appointment($row['Id'], $row["ServiceName"], $row['Date'], $row['StartingHour'], $row['PatientId'], $row['DoctorId'], $row['Note']);
                    $appointmentArray = $appointment->asArray();
                }

                $response = new Response(true, 200);
                $response->toCache(true);
                $response->setData($appointmentArray);
                $response->send();
                exit();
            } catch (AppointmentException $ex) {
                $response = new Response(false, 500);
                $response->addMessage($ex->getMessage());
                $response->send();
                exit();
            } catch (PDOException $ex) {
                $response = new Response(false, 500);
                $response->addMessage("Unable to retreive appointments from DB with given id.");
                $response->send();

                error_log("Connection error: " . $ex->getMessage(), 0);
                exit();
            }
            break;

        case "DELETE":
            try {
                $query = $readDB->prepare('DELETE FROM `appointment` 
                                           WHERE Id = :appointmentId;');

                $query->bindParam(':appointmentId', $appointmentId, PDO::PARAM_INT);
                $query->execute();

                $rowCount = $query->rowCount();
                if ($rowCount === 0) {
                    $response = new Response(false, 404);
                    $response->addMessage("Appointment was not found.");
                    $response->send();
                    exit();
                }

                $response = new Response(true, 200);
                $response->addMessage("Appointment deleted successfully!");
                $response->send();
                exit();
            } catch (AppointmentException $ex) {
                $response = new Response(false, 500);
                $response->addMessage($ex->getMessage());
                $response->send();
                exit();
            } catch (PDOException $ex) {
                $response = new Response(false, 500);
                $response->addMessage("Database conn error.");
                $response->send();

                error_log("Connection error: " . $ex->getMessage(), 0);
                exit();
            }
            break;

        default:
            $response = new Response(false, 405);
            $response->addMessage("Request method is not allowed");
            $response->send();
            exit();
    }
}

// Create appointment
elseif (empty($_GET)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            $response = new Response(false, 400);
            $response->addMessage("Content type header is not set to JSON");
            $response->send();
            exit();
        }

        try {

            $rawPOSTData = file_get_contents('php://input');
            $jsonData = json_decode($rawPOSTData);
            if (!$jsonData) {
                $response = new Response(false, 400);
                $response->addMessage("Request body is not valid");
                $response->send();
                exit();
            }

            if ($authorizedUser['role'] === 'Patient') {
                $jsonData = (array)$jsonData;
                $jsonData['patientId'] = $patientId;
                $jsonData['doctorId'] = $doctorId;
                $jsonData = (object)$jsonData;
            } else {
                $jsonData = (array)$jsonData;
                $jsonData['doctorId'] = $doctorId;
                $jsonData = (object)$jsonData;
                $patientId = $jsonData->patientId;
            }

            $createAppointment = new CreateAppointment($writeDB, $jsonData);

            $query = $writeDB->prepare("INSERT INTO appointment 
                                            (ServiceName, 
                                            Date, 
                                            StartingHour, 
                                            PatientId, 
                                            DoctorId,
                                            Note) 
                                        values (
                                           '{$createAppointment->getServiceName()}',
                                           '{$createAppointment->getDate()}',
                                            {$createAppointment->getStartingHour()},
                                            {$createAppointment->getPatientId()},
                                            {$createAppointment->getDoctorId()},
                                            '{$createAppointment->getNote()}');");
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response = new Response(false, 500);
                $response->addMessage("Appointment was not created.");
                $response->send();
                exit();
            }

            $lastAppointmentCreated = $writeDB->lastInsertId();

            $query = $writeDB->prepare("SELECT *
                                        FROM appointment
                                        WHERE Id = :appointmentId;");
            $query->bindParam(':appointmentId', $lastAppointmentCreated, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response = new Response(false, 500);
                $response->addMessage("Failed to retrieve appointment after creation.");
                $response->send();
                exit();
            }

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $appointment = new Appointment($row['Id'], $row["ServiceName"], $row['Date'], $row['StartingHour'], $row['PatientId'], $row['DoctorId'], $row['Note']);
                $appointmentArray = $appointment->asArray();
            }

            $response = new Response(true, 201);
            $response->toCache(true);
            $response->addMessage("Successfully created Appointment");
            $response->setData($appointmentArray);
            $response->send();
            exit();
        } catch (AppointmentException $ex) {
            $response = new Response(false, 400);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit();
        } catch (PDOException $ex) {
            $response = new Response(false, 500);
            $response->addMessage("There was a problem with creating a appointment in DB: " . $ex->getMessage());
            $response->send();

            error_log("DB error: " . $ex->getMessage(), 0);
            exit();
        }
    }
} else {
    $response = new Response(false, 404);
    $response->addMessage("Request unknown.");
    $response->send();
    exit();
}
