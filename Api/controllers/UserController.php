<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include_once('../config/Database.php');
include_once('../models/Response.php');
include_once('../models/User.php');
include_once('../requestModels/CreateUser.php');

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

// HANDLE OPTIONS REQUEST METHOD FOR POST

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response = new Response(true, 200);
    $response->send();
    exit();
}

// ONLY POST IS ALLOWED

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
    $createUser = new CreateUser($writeDB, $jsonData, '');

    $query = $writeDB->prepare("INSERT INTO user 
                                    (name, 
                                    surname,
                                    gender,
                                    birthPlace,
                                    birthDate,
                                    jmbg,
                                    phoneNumber,
                                    email,
                                    password,
                                    role) 
                                values (
                                    '{$createUser->getName()}',
                                    '{$createUser->getSurname()}',
                                    '{$createUser->getGender()}',
                                    '{$createUser->getBirthPlace()}',
                                    '{$createUser->getBirthDate()}',
                                    '{$createUser->getJmbg()}',
                                    '{$createUser->getPhoneNumber()}',
                                    '{$createUser->getEmail()}',
                                    '{$createUser->getPassword()}',
                                    '{$createUser->getRole()}');");
    $query->execute();
    
    $rowCount = $query->rowCount();
    if ($rowCount === 0){
        $response = new Response(false, 500);
        $response->addMessage("User was not created.");
        $response->send();
        exit();
    }

    $lastUserId = $writeDB->lastInsertId();

    if ($createUser->getRole() === 'Patient'){
        $query = $writeDB->prepare("INSERT INTO patient 
                                        (UserId) 
                                    values ($lastUserId);");
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount === 0){
            $query = $writeDB->prepare("DELETE FROM user 
                                        WHERE $lastUserId;");
            $query->execute();
    
            $response = new Response(false, 500);
            $response->addMessage("Patient was not created.");
            $response->send();
            exit();
        }
    }

    $responseData = $createUser->asArray();
    $responseData['id'] = $lastUserId;
    
    $response = new Response(true, 201);
    $response->addMessage('User successfully created.');
    $response->setData($responseData);
    $response->send();
    exit();
    
} catch (UserException $ex) {
    $response = new Response(false, 400);
    $response->addMessage($ex->getMessage());
    $response->send();
    exit();
} catch (PDOException $ex) {
    $response = new Response(false, 500);
    $response->addMessage("There was a problem with creating a user in DB: \n" . $ex->getMessage());
    $response->send();

    error_log("DB error: " . $ex->getMessage(), 0);
    exit();
}
