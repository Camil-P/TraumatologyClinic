<?php

$descriptionCM00014 = "Created MessagePatientId_fk relation";

$CM00014 = "ALTER TABLE `message` 
            ADD CONSTRAINT `MessageSenderUserId_fk` 
            FOREIGN KEY (`Sender`) 
            REFERENCES `user`(`Id`) 
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT;";