<?php

class AppointmentException extends Exception
{
};

class Appointment
{
    private $_id;
    private $_serviceName;
    private $_completionStatus;
    private $_date;
    private $_startingHour;
    private $_patientId;
    private $_doctorId;
    private $_note;

    public function __construct($id, $serviceName, $date, $hour, $patientId, $doctorId, $note)
    {
        $this->setId($id);
        $this->setServiceName($serviceName);
        $this->setDate($date);
        $this->setStartingHour($hour);
        $this->setCompletionStatus();
        $this->setPatientId($patientId);
        $this->setDoctorId($doctorId);
        $this->setNote($note);
    }

    public function asArray()
    {
        $appointment = array();
        $appointment["id"] = $this->getId();
        $appointment["serviceName"] = $this->getServiceName();
        $appointment["completionStatus"] = $this->getCompletionStatus();
        $appointment["date"] = $this->getDate();
        $appointment["startingHour"] = $this->getStartingHour();
        $appointment["patientId"] = $this->getPatientId();
        $appointment["doctorId"] = $this->getDoctorID();
        $appointment["note"] = $this->getNote();

        return $appointment;
    }

    // GETTERS

    public function getId()
    {
        return $this->_id;
    }

    public function getServiceName()
    {
        return $this->_serviceName;
    }

    public function getCompletionStatus()
    {
        return $this->_completionStatus;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function getStartingHour()
    {
        return $this->_startingHour;
    }

    public function getPatientId()
    {
        return $this->_patientId;
    }

    public function getDoctorID()
    {
        return $this->_doctorId;
    }

    public function getNote(){
        return $this->_note;
    }

    // SETTERS WITH VALIDATION

    public function setId($id)
    {
        if ($id !== null && (!is_numeric($id) || $id <= 0 || $this->_id !== null)) {
            throw new AppointmentException("Appointment - Id is not valid.");
        }

        $this->_id = $id;
    }

    public function setServiceName($serviceName)
    {
        if (strlen($serviceName) < 0 || strlen($serviceName) > 255) {
            throw new AppointmentException("Appointment - ServiceName is not valid.");
        }

        $this->_serviceName = $serviceName;
    }

    public function setCompletionStatus()
    {
        if (date("Y-m-d") > $this->_date) {
            $this->_completionStatus = true;
        } elseif (date("Y-m-d") < $this->_date) {
            $this->_completionStatus = false;
        } else {
            if (date("H") >= $this->_startingHour) {
                $this->_completionStatus = true;
            } else {
                $this->_completionStatus = false;
            }
        }
    }

    public function setDate($date)
    {
        if (!(bool)strtotime($date)) {
            throw new AppointmentException("Appointment - Date is not date value");
        }

        $format = "Y-m-d";
        $parsedDate = DateTime::createFromFormat($format, $date);
        if (!$parsedDate) {
            throw new AppointmentException("Appointment - Date is not the right format");
        }

        $yearSub = $parsedDate->format($format)[3] - date("Y")[3];
        if ($yearSub < 0 && $yearSub > 1) {
            throw new AppointmentException("Appointment - Date is out of bounds");
        }
        $this->_date = $date;
    }

    public function setStartingHour($hour)
    {
        $startingHour = "08";
        $endingHour = "16";
        if (
            $hour < $startingHour
            || $hour > $endingHour
            || !is_numeric($hour)
        ) {
            throw new AppointmentException("Appointment - Hour is not valid value" . $hour);
        }

        $this->_startingHour = $hour;
    }

    public function setPatientId($patientId)
    {
        if (!is_numeric($patientId)) {
            throw new AppointmentException("Appointment - PatientId is not valid value");
        }

        $this->_patientId = $patientId;
    }

    public function setDoctorId($doctorId)
    {
        if (!is_numeric($doctorId)) {
            throw new AppointmentException("Appointment - DoctorId is not valid value");
        }

        $this->_doctorId = $doctorId;
    }

    public function setNote($note){
        $this->_note = $note;
    }
}
