<?php

header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

include_once('../config/Database.php');
include_once('../models/Response.php');
include_once('../models/User.php');
include_once('../requestModels/CreateSession.php');

try {
  $writeDB = DB::connectWriteDB();
  // $readDB = DB::connectReadDB();
} catch (PDOException $ex) {
  $response = new Response(false, 500);
  $response->addMessage("Database conn error.");
  $response->send();

  error_log("Connection error: " . $ex->getMessage(), 0);
  exit();
}

if (array_key_exists("id", $_GET)) {
  $sessionId = $_GET['id'];

  if ($sessionId === '' || !is_numeric($sessionId)) {
    $response = new Response(false, 400);
    $response->addMessage("sessionId in not valid.");
    $response->send();
    exit();
  }

  $accessToken = $_SERVER['HTTP_AUTHORIZATION'];

  if (!isset($accessToken) || strlen($accessToken) < 1) {
    $response = new Response(false, 400);
    $response->addMessage("AccessToken is not valid.");
    $response->send();
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response = new Response(true, 200);
    $response->send();
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
      $query = $writeDB->prepare("DELETE FROM session
                                        WHERE Id = $sessionId AND AccessToken = '$accessToken'");
      $query->execute();

      $rowCount = $query->rowCount();
      if ($rowCount === 0) {
        $response = new Response(false, 500);
        $response->addMessage("Failed to log out of this session with provided access token.");
        $response->send();
        exit();
      }

      $returnData = array();
      $returnData['sessionId'] = $sessionId;

      $response = new Response(true, 200);
      $response->addMessage("Successfully logged out.");
      $response->setData($returnData);
      $response->send();
      exit();
    } catch (PDOException $ex) {
      $response = new Response(false, 500);
      $response->addMessage("Failed to logout of this session. Please try again. Reason: " . $ex->getMessage());
      $response->send();
      exit();
    }
  }

  // elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH'){

  //   if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
  //     $response = new Response(false, 400);
  //     $response->addMessage("Content type header not set to JSON.");
  //     $response->send();
  //     exit();
  //   }

  //   if (!$jsonData = json_decode(file_get_contents('php://input'))) {
  //     $response = new Response(false, 400);
  //     $response->addMessage("Request body is not valid JSON.");
  //     $response->send();
  //     exit();
  //   }

  //   $refreshToken = $jsonData->refresh_token;

  //   if (!isset($refreshToken) || strlen($refreshToken) < 1){
  //     $response = new Response(false, 400);
  //     $response->addMessage("refresh_token is not set or valid.");
  //     $response->send();
  //     exit();
  //   }

  //   try {

  //     $query = $writeDB->prepare("SELECT 
  //                                   session.Id as sessionId,
  //                                   session.UserId as userId,
  //                                   AccessToken,
  //                                   AccessTokenExpiry,
  //                                   RefreshToken,
  //                                   RefreshTokenExpiry,
  //                                   Role
  //                                 FROM session, user
  //                                 WHERE 
  //                                   user.Id = userId AND
  //                                   session.Id = :sessionId AND
  //                                   session.AccessToken = :accessToken,
  //                                   session.RefreshToken = :refreshToken");
  //     $query->bindParam(':sessionId', $sessionId, PDO::PARAM_INT);
  //     $query->bindParam(':accessToken', $accessToken, PDO::PARAM_STR);
  //     $query->bindParam(':refreshToken', $refreshToken, PDO::PARAM_INT);
  //     $query->execute();

  //     $rowCount = $query->rowCount();
  //     if ($rowCount === 0) {
  //       $response = new Response(false, 500);
  //       $response->addMessage("AccessToken or Refresh Token is incorrect for the session id.");
  //       $response->send();
  //       exit();
  //     }

  //     $row = $query->fetch(PDO::FETCH_ASSOC);

  //     echo json_encode($row);
  //     exit();

  // } catch (PDOException $ex) {
  //   $response = new Response(false, 500);
  //   $response->addMessage("There was a problem refreshing the accessToken. Please try again.");
  //   $response->send();

  //   error_log("DB error: " . $ex->getMessage(), 0);
  //   exit();
  // }

  // }
} elseif (empty($_GET)) {
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response = new Response(true, 200);
    $response->send();
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = new Response(false, 405);
    $response->addMessage("Method not allowed.");
    $response->send();
    exit();
  }

  sleep(1);

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

    $createSession = new CreateSession($jsonData);

    $query = $writeDB->prepare("SELECT *
                                    FROM user
                                    WHERE Email = '{$createSession->getEmail()}'");
    $query->execute();

    $rowCount = $query->rowCount();

    if ($rowCount === 0) {
      $response = new Response(false, 401);
      $response->addMessage("Username or password is incorrect");
      $response->send();
      exit();
    }

    $row = $query->fetch(PDO::FETCH_ASSOC);

    $user = new User(
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

    if ($user->isDisabled()) {
      $response = new Response(false, 401);
      $response->addMessage("User's account is disabled.");
      $response->send();
      exit();
    }

    if (!password_verify($createSession->getPassword(), $user->getPassword())) {

      $query = $writeDB->prepare("UPDATE user 
                                        SET LoginAttempts = LoginAttempts + 1
                                        WHERE id = {$user->getId()}");
      $query->execute();

      $response = new Response(false, 401);
      $response->addMessage("Username or password is incorrect");
      $response->send();
      exit();
    }

    $accessToken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

    $accessTokenExpirySeconds = 432000;
  } catch (SessionException $ex) {
    $response = new Response(false, 400);
    $response->addMessage($ex->getMessage());
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

  try {

    $writeDB->beginTransaction();

    $query = $writeDB->prepare("UPDATE user 
                                  SET LoginAttempts = 0
                                  WHERE Id = {$user->getId()}");
    $query->execute();

    $query = $writeDB->prepare("INSERT INTO session (
                                  UserId,
                                  AccessToken,
                                  AccessTokenExpiry,
                                  Role)
                                  values (
                                  '{$user->getId()}',
                                  '$accessToken',
                                  date_add(NOW(), INTERVAL :accessTokenExpirySeconds SECOND),
                                  '{$user->getRole()}');");
    $query->bindParam(':accessTokenExpirySeconds', $accessTokenExpirySeconds, PDO::PARAM_INT);
    $query->execute();

    $lastSessionId = $writeDB->lastInsertId();
    $writeDB->commit();

    $returnData = array();
    $returnData['sessionId'] = intval($lastSessionId);
    $returnData['accessToken'] = $accessToken;
    $returnData['accessTokenExpiresIn'] = $accessTokenExpirySeconds;
    $returnData['role'] = $user->getRole();

    $response = new Response(true, 201);
    $response->addMessage("Session successfully created.");
    $response->setData($returnData);
    $response->send();
    exit();
  } catch (PDOException $ex) {
    try {
      $writeDB->rollBack();
      $response = new Response(false, 500);
      $response->addMessage("There was a problem with creating a session in DB: \n" . $ex->getMessage());
      $response->send();

      error_log("DB error: " . $ex->getMessage(), 0);
      exit();
    } catch (PDOException $ex2) {
      $response = new Response(false, 500);
      $response->addMessage("There was a problem with creating a session in DB: \n" . $ex2->getMessage());
      $response->send();

      error_log("DB error: " . $ex->getMessage(), 0);
      exit();
    }
  }
} else {
  $response = new Response(false, 405);
  $response->addMessage("Method not allowed.");
  $response->send();
  exit();
};
