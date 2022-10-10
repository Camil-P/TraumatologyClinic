<?php

require_once("Database.php");
require_once("../models/Response.php");

try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
    $response = new Response(true, 200);
    $response->addMessage("Database Connection Successfull!");
    $response->send();
    exit;
} catch (PDOException $ex) {
    $response = new Response(false, 500);
    $response->addMessage("Database Connection Error: " . $ex);
    $response->send();
    exit;
}
