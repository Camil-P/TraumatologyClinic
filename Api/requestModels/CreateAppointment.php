<?php

include_once("../models/Appointment.php");

class CreateAppointment
{
    private $_serviceName;
    private $_date;
    private $_startingHour;
    private $_patientId;
    private $_doctorId;
    private $_note;

    public function __construct($writeDB, $reqBody)
    {
        if (!isset($reqBody->serviceName)){
            throw new AppointmentException("Appointment - serviceName is not set.");
        }
        if (!isset($reqBody->date)){
            throw new AppointmentException("Appointment - date is not set.");
        }
        if (!isset($reqBody->startingHour)){
            throw new AppointmentException("Appointment - startingHour is not set.");
        }
        if (!isset($reqBody->patientId)){
            throw new AppointmentException("Appointment - patientId is not set.");
        }
        if (!isset($reqBody->doctorId)){
            throw new AppointmentException("Appointment - doctorId is not set.");
        }

        $this->setServiceName($reqBody->serviceName);
        $this->setDate($reqBody->date);
        $this->setStartingHour($reqBody->startingHour);
        $this->setPatientId($reqBody->patientId);
        $this->setDoctorId($reqBody->doctorId);
        $this->setNote($reqBody->note);
        
        if ($this->appointmentAlreadyExists($writeDB, $this->getDate(), $this->getStartingHour(), $this->getPatientId(), $this->getDoctorID())) {
            throw new AppointmentException("User or doctor already has an appointment for the requested time.");
        }
    }

    public function asArray()
    {
        $appointment = array();
        $appointment["serviceName"] = $this->getServiceName();
        $appointment["date"] = $this->getDate();
        $appointment["startingHour"] = $this->getStartingHour();
        $appointment["patientId"] = $this->getPatientId();
        $appointment["doctorId"] = $this->getDoctorID();
        $appointment["note"] = $this->getNote();

        return $appointment;
    }


    private function appointmentAlreadyExists($writeDB, $date, $startingHour, $patientId, $doctorId)
    {
        try {
            $query = $writeDB->prepare(" SELECT *
            FROM appointment
            WHERE 
                Date = '$date' AND
                StartingHour = $startingHour AND
                PatientId = $patientId OR
                Date = '$date' AND
                StartingHour = $startingHour AND
                DoctorId = $doctorId;");
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                return false;
            }
            return true;
        } catch (PDOException $ex) {
            $response = new Response(false, 500);
            $response->addMessage("Unable to check if appointment already exists. " . $ex->getMessage());
            $response->send();
            exit();
        }
    }

    // SETTERS WITH VALIDATION

    public function setServiceName($serviceName)
    {
        if (!isset($serviceName) || strlen($serviceName) < 0 || strlen($serviceName) > 255) {
            throw new AppointmentException("Appointment - ServiceName is not valid.");
        }

        $this->_serviceName = $serviceName;
    }

    public function setDate($date)
    {
        if (!isset($date) || !(bool)strtotime($date)) {
            throw new AppointmentException("Appointment - Date is not date value");
        }

        $format = "Y-m-d";
        $parsedDate = DateTime::createFromFormat($format, $date);
        if (!$parsedDate) {
            throw new AppointmentException("Appointment - Date is not the right format");
        }

        $date = $parsedDate->format($format);

        if ($date < date($format)) {
            throw new AppointmentException("Appointment - Cannot create appointment if date has passed.");
        }

        $yearSub = $date[3] - date("Y")[3];
        if ($yearSub < 0 || $yearSub > 1) {
            throw new AppointmentException("Appointment - Date is out of bounds");
        }

        $this->_date = $date;
    }

    public function setStartingHour($hour)
    {
        $startingHour = "08";
        $endingHour = "16";
        if (
            !isset($hour)
            || $hour < $startingHour
            || $hour > $endingHour
            || !is_numeric($hour)
            || is_string($hour)
        ) {
            throw new AppointmentException("Appointment - Hour is not valid value");
        }

        $date = $this->getDate();
        if ($date === date("Y-m-d") && $hour <= date("H")) {
            throw new AppointmentException("Appointment - Hour has already passed.");
        }

        $this->_startingHour = $hour;
    }

    public function setPatientId($patientId)
    {
        if (
            !isset($patientId)
            || !is_numeric($patientId)
            || is_string($patientId)
        ) {
            throw new AppointmentException("Appointment - PatientId is not valid value");
        }

        $this->_patientId = $patientId;
    }

    public function setDoctorId($doctorId)
    {
        if (
            !isset($doctorId)
            || !is_numeric($doctorId)
            || is_string($doctorId)
        ) {
            throw new AppointmentException("Appointment - DoctorId is not valid value");
        }

        $this->_doctorId = $doctorId;
    }

    public function setNote($note){
        $this->_note = $note;
    }

    // GETTERS

    public function getServiceName()
    {
        return $this->_serviceName;
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

    public function getDoctorId()
    {
        return $this->_doctorId;
    }

    public function getNote(){
        return $this->_note;
    }
}
