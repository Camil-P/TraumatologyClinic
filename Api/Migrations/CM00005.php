<?php

$descriptionCM00005 = "CM00005: Create session table";

$CM00005 = "CREATE TABLE `clinic`.`session` ( 
                `Id` INT NOT NULL AUTO_INCREMENT, 
                `UserId` INT NOT NULL, 
                `AccessToken` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                `AccessTokenExpiry` DATETIME NOT NULL,
                `Role` enum('Admin','Doctor','Patient') NOT NULL DEFAULT 'Patient',
                PRIMARY KEY (`Id`),
                UNIQUE (`AccessToken`)
            ) ENGINE = InnoDB;";
