<?php

$descriptionCM00011 = "CM00011: Added table assigndoctorrequest";

$CM00011 = "CREATE TABLE `clinic`.`assigndoctorrequest` (
                `Id` INT(11) NOT NULL AUTO_INCREMENT,
                `PatientId` INT(11) NOT NULL,
                `RequestDoctorId` INT(11) NOT NULL,
                `PreviouseDoctorId` INT(11) NOT NULL,
                PRIMARY KEY (`Id`)) 
                ENGINE = InnoDB;";
