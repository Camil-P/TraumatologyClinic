<?php

$descriptionCM00006 = "CM00006: Add relation SessionUserId_fk";

$CM00006 = "ALTER TABLE `session` 
            ADD CONSTRAINT `SessionUserId_fk`
            FOREIGN KEY (`UserId`)
            REFERENCES `user`(`Id`)
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT;";
