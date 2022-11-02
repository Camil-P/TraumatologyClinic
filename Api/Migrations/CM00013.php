<?php

$descriptionCM00013 = "CM00013: Create Message table";

$CM00013 = "CREATE TABLE `clinic`.`message` ( 
                `Id` INT(11) NOT NULL AUTO_INCREMENT, 
                `Sender` INT(11) NOT NULL, 
                `Receiver` INT(11) NOT NULL, 
                `Content` VARCHAR(255) NOT NULL, 
                PRIMARY KEY (`Id`)) ENGINE = InnoDB;";