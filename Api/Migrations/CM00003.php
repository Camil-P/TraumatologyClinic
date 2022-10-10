<?php

$descriptionCM00003 = "CM00003: Create Basic User table";

$CM00003 = "CREATE TABLE `clinic`.`user` (
                `Id` int(11) NOT NULL AUTO_INCREMENT,
                `Name` varchar(25) NOT NULL,
                `Surname` varchar(25) NOT NULL,
                `Gender` enum('Male','Female') NOT NULL,
                `BirthPlace` varchar(25) NOT NULL,
                `BirthDate` date NOT NULL,
                `JMBG` varchar(15) NOT NULL UNIQUE,
                `PhoneNumber` varchar(15) NOT NULL UNIQUE,
                `Email` varchar(25) NOT NULL UNIQUE,
                `Role` enum('Admin','Doctor','Patient') NOT NULL DEFAULT 'Patient',
                `Password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                `Disabled` boolean NOT NULL default 0,
                `LoginAttempts` int(1) NOT NULL default 0,
                PRIMARY KEY (Id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
