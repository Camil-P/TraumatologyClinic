<?php

$descriptionCM00010 = "CM00010: Add relations AssignmentPatient_fk, AssignmentDoctor_fk, AssignmentPreviousDoctor_fk";

$CM00010 = "ALTER TABLE `assigndoctorrequest` 
            ADD CONSTRAINT `AssignmentPatient_fk` 
            FOREIGN KEY (`PatientId`) 
            REFERENCES `patient`(`Id`) 
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT; 
            ALTER TABLE `assigndoctorrequest` 
            ADD CONSTRAINT `AssignmentDoctor_fk` 
            FOREIGN KEY (`RequestDoctorId`) 
            REFERENCES `user`(`Id`) 
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT; 
            ALTER TABLE `assigndoctorrequest` 
            ADD CONSTRAINT `AssignmentPreviousDoctor_fk` 
            FOREIGN KEY (`PreviouseDoctorId`) 
            REFERENCES `user`(`Id`) 
            ON DELETE RESTRICT 
            ON UPDATE RESTRICT;";
