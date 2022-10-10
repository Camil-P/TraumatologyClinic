<?php

$descriptionCM00004 = "CM00004: Create patient table";

$CM00004 = "CREATE TABLE `clinic`.`patient` (
                `Id` int(11) NOT NULL AUTO_INCREMENT,
                `UserId` int(11) NOT NULL UNIQUE,
                `ImprovementOpinion` varchar(255) DEFAULT NULL,
                `DoctorsOpinion` varchar(255) DEFAULT NULL,
                `DoctorId` int(11) DEFAULT NULL,
                PRIMARY KEY (Id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
