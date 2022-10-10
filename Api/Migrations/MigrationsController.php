<?php

require_once('../config/Database.php');
require_once('../models/Response.php');

require_once('CM00001.php');
require_once('CM00002.php');
require_once('CM00003.php');
require_once('CM00004.php');
require_once('CM00005.php');
require_once('CM00006.php');
require_once('CM00007.php');
require_once('CM00008.php');
require_once('CM00009.php');
require_once('CM00010.php');
require_once('CM00011.php');
require_once('CM00012.php');

if ($_GET['username'] === 'clinic' && $_GET['password'] === 'clinic') {

    try {
        $writeDB = DB::connectWriteDB();
    } catch (PDOException $ex) {
        $response = new Response(false, 500);
        $response->addMessage("Database conn error.");
        $response->send();
    
        error_log("Connection error: " . $ex->getMessage(), 0);
        exit();
    }

    $response = new Response(true, 200);
    try {

        // $writeDB->beginTransaction();

        // Execute the above required migrations
        $stmt = $writeDB->prepare($CM00001);
        $response->addMessage($descriptionCM00001);
        $stmt->execute();

        // $stmt = $writeDB->prepare($CM00002);
        // $response->addMessage($descriptionCM00002);
        // $stmt->execute();

        $stmt = $writeDB->prepare($CM00003);
        $response->addMessage($descriptionCM00003);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00004);
        $response->addMessage($descriptionCM00004);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00005);
        $response->addMessage($descriptionCM00005);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00006);
        $response->addMessage($descriptionCM00006);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00007);
        $response->addMessage($descriptionCM00007);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00008);
        $response->addMessage($descriptionCM00008);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00009);
        $response->addMessage($descriptionCM00009);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00010);
        $response->addMessage($descriptionCM00010);
        $stmt->execute();

        $stmt = $writeDB->prepare($CM00011);
        $response->addMessage($descriptionCM00011);
        $stmt->execute();
        
        $stmt = $writeDB->prepare($CM00012);
        $response->addMessage($descriptionCM00012);
        $stmt->execute();

        // $writeDB->commit();

        // Send response the migrations are valid
        $response->send();
    } catch (PDOException $ex) {
        // $writeDB->rollBack();
        
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Migration Error: " . $ex->getMessage());
        $response->send();

        error_log("Connection error: " . $ex->getMessage(), 0);
        exit();
    }
}
