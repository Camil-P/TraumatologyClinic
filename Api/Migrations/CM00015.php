<?php

$descriptionCM00015 = "Created MessageDocotrId_fk relation";

$CM00015 = "ALTER TABLE `message` 
            ADD CONSTRAINT `MessageReceiverUserId_fk` 
            FOREIGN KEY (`Receiver`) 
            REFERENCES `user`(`Id`) 
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT;";