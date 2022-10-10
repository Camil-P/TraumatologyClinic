<?php

require_once('Response.php');
// require_once('Appointment.php');
// date_default_timezone_set('Europe/Belgrade');

$response = new Response(true, 200);
$response->addMessage("App works");
$response->send();
