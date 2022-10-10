<?php

$descriptionCM00001 = "CM00001: Create appointment table";

$CM00001 = "CREATE TABLE `clinic`.`appointment` (
    `Id` int(11) NOT NULL AUTO_INCREMENT,
    `ServiceName` varchar(25) NOT NULL,
    `Date` date NOT NULL,
    `StartingHour` int(11) NOT NULL,
    `PatientId` int(11) NOT NULL,
    `DoctorId` int(11) NOT NULL,
    `Note` VARCHAR(255) NULL,
    PRIMARY KEY (Id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
