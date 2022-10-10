<?php

// pass za camila = camil123
// pass za dzela = dzelal123

class DoctorException extends Exception
{
};

class Doctor
{
  private $_name;
  private $_surname;
  private $_gender;
  private $_birthPlace;
  private $_phoneNumber;
  private $_email;
  private $_assigned;

  public function __construct($name, $surname, $gender, $birthPlace, $phoneNumber, $email, $assigned)
  {
    $this->setName($name);
    $this->setSurname($surname);
    $this->setGender($gender);
    $this->setBirthPlace($birthPlace);
    $this->setPhoneNumber($phoneNumber);
    $this->setEmail($email);
    $this->setAssigned($assigned);
  }

  public function asArray()
  {
    $Doctor = array();
    $Doctor['name'] = $this->getName();
    $Doctor['surname'] = $this->getSurname();
    $Doctor['gender'] = $this->getGender();
    $Doctor['birthPlace'] = $this->getBirthPlace();
    $Doctor['phoneNumber'] = $this->getPhoneNumber();
    $Doctor['email'] = $this->getEmail();
    $Doctor['assigned'] = $this->isAssigned();

    return $Doctor;
  }

  // SETTERS

  public function setName($name)
  {
    if (strlen($name) < 0 || strlen($name) > 25) {
      throw new DoctorException("Doctor - Name is not valid.");
    }

    $this->_name = $name;
  }

  public function setSurname($surname)
  {
    if (strlen($surname) < 0 || strlen($surname) > 25) {
      throw new DoctorException("Doctor - Surame is not valid.");
    }

    $this->_surname = $surname;
  }

  public function setGender($gender)
  {
    if ($gender !== "Male" && $gender !== "Female") {
      throw new DoctorException("Doctor - Gender is not valid");
    }

    $this->_gender = $gender;
  }


  public function setBirthPlace($birthPlace)
  {
    if (strlen($birthPlace) < 0 || strlen($birthPlace) > 25) {
      throw new DoctorException("Doctor - Brth Place is not valid");
    }

    $this->_birthPlace = $birthPlace;
  }

  public function setPhoneNumber($phoneNumber)
  {
    if (strlen($phoneNumber) < 0 || strlen($phoneNumber) > 15) {
      throw new DoctorException("Doctor - Phone Number is not valid.");
    }

    $this->_phoneNumber = $phoneNumber;
  }

  public function setEmail($email)
  {
    if (strlen($email) < 0 || strlen($email) > 25) {
      throw new DoctorException("Doctor - Email is not valid.");
    }

    $this->_email = trim($email);
  }

  public function setAssigned($assigned)
  {
    if ($assigned != 0 & $assigned != 1){
      throw new DoctorException("Doctor - Assigned must be boolean value.");
    }

    $this->_assigned = $assigned;
  }

  // GETTERS 

  public function getName()
  {
    return $this->_name;
  }

  public function getSurname()
  {
    return $this->_surname;
  }

  public function getGender()
  {
    return $this->_gender;
  }

  public function getBirthPlace()
  {
    return $this->_birthPlace;
  }

  public function getPhoneNumber()
  {
    return $this->_phoneNumber;
  }

  public function getEmail()
  {
    return $this->_email;
  }
  
  public function isAssigned()
  {
    return !!$this->_assigned;
  }
}