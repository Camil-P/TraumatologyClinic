<?php

require_once("../config/Database.php");
require_once("../models/Response.php");
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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response = new Response(true, 200);
    $response->send();
    exit();
}

// AUTH

$authorizedUser = authorize($writeDB);

if (empty($_GET) && $_SERVER['REQUEST_METHOD'] === 'POST') {

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

        if ($jsonData->content === "" || !isset($jsonData->content) || empty($jsonData->content)) {
            $response = new Response(false, 400);
            $response->addMessage("Content of the message is not valid.");
            $response->send();
            exit();
        }

        $jsonData = (array)$jsonData;
        $jsonData['sender'] = $authorizedUser['id'];
        $jsonData = (object)$jsonData;

        $query = $writeDB->prepare("INSERT INTO message (
                                            Sender,
                                            Receiver,
                                            Content
                                        ) 
                                        values (
                                            :sender,
                                            :receiver,
                                            :content);");
        $query->bindParam(':sender', $jsonData->sender, PDO::PARAM_INT);
        $query->bindParam(':receiver', $jsonData->receiver, PDO::PARAM_INT);
        $query->bindParam(':content', $jsonData->content, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount === 0) {
            $response = new Response(false, 500);
            $response->addMessage("Message was not created. Please try again");
            $response->send();
            exit();
        }

        $lastMessageCreated = $writeDB->lastInsertId();

        $jsonData = (array)$jsonData;
        $jsonData['id'] = $lastMessageCreated;

        $response = new Response(true, 201);
        $response->addMessage("Successfully created Message");
        $response->setData($jsonData);
        $response->send();
        exit();
    } catch (PDOException $ex) {
        $response = new Response(false, 500);
        $response->addMessage("There was a problem with creating a message in DB: " . $ex->getMessage());
        $response->send();

        error_log("DB error: " . $ex->getMessage(), 0);
        exit();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // if ($authorizedUser['role'] === 'Patient') {
    //     $doctorId = $_GET['id'];
    //     $query = $writeDB->prepare("SELECT * 
    //                                 FROM message 
    //                                 WHERE DoctorId = $doctorId;");
    // } else {
    //     $patientId = $_GET['id'];
    //     $query = $writeDB->prepare("SELECT * 
    //                                 FROM message 
    //                                 WHERE PatientId = $patientId;");
    // }

    $query = $writeDB->prepare("SELECT * 
                                FROM message 
                                WHERE 
                                    Sender = {$authorizedUser['id']} OR
                                    Receiver = {$authorizedUser['id']};");

    $query->execute();

    $rowCount = $query->rowCount();
    if ($rowCount === 0) {
        $response = new Response(false, 500);
        $response->addMessage("Unable to get messages for the provided id.");
        $response->send();
        exit();
    }

    $responseData = array();
    $responseData['senderId'] = $authorizedUser['id'];

    $messageArray = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $message = array();
        $message['id'] = $row['Id'];
        $message['content'] = $row['Content'];
        $message['sender'] = $row['Sender'];
        $message['receiver'] = $row['Receiver'];
        $messageArray[] = $message;
    }
    $responseData['messages'] = $messageArray;

    $response = new Response(true, 200);
    $response->addMessage("Successfully fetched messages");
    $response->setData($responseData);
    $response->send();
    exit();
} else {
    $response = new Response(false, 404);
    $response->addMessage("Request unknown.");
    $response->send();
    exit();
}
