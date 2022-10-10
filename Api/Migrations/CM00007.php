<?php

$descriptionCM00007 = "CM00007: Add relation AppointmentPatient_fk";

$CM00007 = "ALTER TABLE `appointment` 
            ADD CONSTRAINT `AppointmentPatient_fk` 
            FOREIGN KEY (`PatientId`) 
            REFERENCES `patient`(`Id`) 
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT;";
