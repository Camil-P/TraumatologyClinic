<?php

$descriptionCM00009 = "CM00009: Add relation AppointmentDoctor_fk";

$CM00009 = "ALTER TABLE `appointment`
            ADD CONSTRAINT `AppointmentDoctor_fk`
            FOREIGN KEY (`DoctorId`) REFERENCES
            `user`(`Id`) ON DELETE RESTRICT
            ON UPDATE RESTRICT;";
