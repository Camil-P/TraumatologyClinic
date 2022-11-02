<?php

// header('Access-Control-Allow-Headers: *');
// header('Access-Control-Max-Age: 86400');
// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Allow-Methods: *');


include_once('../config/Database.php');
include_once('../models/Response.php');
include_once('../models/Doctor.php');
include_once('../models/User.php');
include_once('../requestModels/CreateUser.php');
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

$authorizedUser = authorize($writeDB);

if ($authorizedUser['role'] !== "Doctor") {
    $response = new Response(false, 401);
    $response->addMessage("You are not authorized");
    $response->send();
}

// HANDLE OPTIONS REQUEST METHOD FOR POST

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response = new Response(true, 200);
    $response->send();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (array_key_exists('fetch', $_GET)) {
        $fetch = $_GET['fetch'];

        if ($fetch === 'patients') {
            $query = $writeDB->prepare("SELECT u.*, p.Id as PatientId 
                                        FROM user u 
                                        INNER JOIN patient p 
                                            ON u.Id = p.UserId;");
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response = new Response(false, 404);
                $response->addMessage("No patients were found.");
                $response->send();
                exit();
            }


            $response = new Response(true, 200);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $patient = new User(
                    $row['Id'],
                    $row['Name'],
                    $row['Surname'],
                    $row['Gender'],
                    $row['BirthPlace'],
                    $row['BirthDate'],
                    $row['JMBG'],
                    $row['PhoneNumber'],
                    $row['Email'],
                    $row['Role'],
                    $row['Password'],
                    $row['Disabled'],
                    $row['LoginAttempts']
                );
                $patientAsArray = $patient->asArray();
                $patientAsArray['patientId'] = $row['PatientId'];
                $rowCount = $query->rowCount();

                $patientsArray[] = $patientAsArray;
            }

            $response->toCache(true);
            $response->setData($patientsArray);
            $response->send();
            exit();
        }
    }
}
