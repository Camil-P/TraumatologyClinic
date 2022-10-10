<?php

$descriptionCM00008 = "CM00008: Add relation PatientUser_pk";

$CM00008 = "ALTER TABLE `patient` 
            ADD CONSTRAINT `PatientUser_pk` 
            FOREIGN KEY (`UserId`) 
            REFERENCES `user`(`Id`) 
            ON DELETE RESTRICT
            ON UPDATE RESTRICT;";
