<?php

// pass za camila = camil123
// pass za dzela = dzelal123

class UserException extends Exception
{
};

class User
{
  private $_id;
  private $_name;
  private $_surname;
  private $_gender;
  private $_birthPlace;
  private $_birthDate;
  private $_jmbg;
  private $_phoneNumber;
  private $_email;
  private $_role;
  private $_password;
  private $_disabled;
  private $_loginAttempts;

  public function __construct($id, $name, $surname, $gender, $birthPlace, $birthDate, $jmbg, $phoneNumber, $email, $role, $password, $disabled, $loginAttempts)
  {
    $this->setId($id);
    $this->setName($name);
    $this->setSurname($surname);
    $this->setGender($gender);
    $this->setBirthPlace($birthPlace);
    $this->setBirthDate($birthDate);
    $this->setJMBG($jmbg);
    $this->setPhoneNumber($phoneNumber);
    $this->setEmail($email);
    $this->setRole($role);
    $this->setPassword($password);
    $this->setDisabled($disabled);
    $this->setLoginAttempts($loginAttempts);
  }

  public function asArray()
  {
    $user = array();
    $user['id'] = $this->getId();
    $user['name'] = $this->getName();
    $user['surname'] = $this->getSurname();
    $user['gender'] = $this->getGender();
    $user['birthPlace'] = $this->getBirthPlace();
    $user['birthDate'] = $this->getBirthDate();
    $user['jmbg'] = $this->getJmbg();
    $user['phoneNumber'] = $this->getPhoneNumber();
    $user['email'] = $this->getEmail();
    $user['role'] = $this->getRole();
    $user['password'] = $this->getPassword();
    $user['disabled'] = $this->isDisabled();
    $user['loginAttempts'] = $this->getLoginAttempts();

    return $user;
  }

  // SETTERS

  public function setId($id)
  {
    if ($id !== null && (!is_numeric($id) || $id <= 0 || $this->_id !== null)) {
      throw new UserException("User - Id is not valid.");
    }

    $this->_id = $id;
  }

  public function setName($name)
  {
    if (strlen($name) < 0 || strlen($name) > 25) {
      throw new UserException("User - Name is not valid.");
    }

    $this->_name = $name;
  }

  public function setSurname($surname)
  {
    if (strlen($surname) < 0 || strlen($surname) > 25) {
      throw new UserException("User - Surame is not valid.");
    }

    $this->_surname = $surname;
  }

  public function setGender($gender)
  {
    if ($gender !== "Male" && $gender !== "Female") {
      throw new UserException("User - Gender is not valid");
    }

    $this->_gender = $gender;
  }


  public function setBirthPlace($birthPlace)
  {
    if (strlen($birthPlace) < 0 || strlen($birthPlace) > 25) {
      throw new UserException("User - Brth Place is not valid");
    }

    $this->_birthPlace = $birthPlace;
  }

  public function setBirthDate($birthDate)
  {
    if (!(bool)strtotime($birthDate)) {
      throw new UserException("User - Date is not date value");
    }

    $format = "Y-m-d";
    $parsedDate = DateTime::createFromFormat($format, $birthDate);
    if (!$parsedDate) {
      throw new UserException("User - Date is not the right format");
    }

    $this->_birthDate = $birthDate;
  }

  public function setJMBG($jmbg)
  {
    if (strlen($jmbg) < 0 || strlen($jmbg) > 15) {
      throw new UserException("User - JMBG is not valid.");
    }

    $this->_jmbg = $jmbg;
  }

  public function setPhoneNumber($phoneNumber)
  {
    if (strlen($phoneNumber) < 0 || strlen($phoneNumber) > 15) {
      throw new UserException("User - Phone Number is not valid.");
    }

    $this->_phoneNumber = $phoneNumber;
  }

  public function setRole($role)
  {
    if ($role !== "Admin" && $role !== "Patient" && $role !== "Doctor") {
      throw new UserException("User - Role is not valid.");
    }

    $this->_role = $role;
  }

  public function setEmail($email)
  {
    if (strlen($email) < 0 || strlen($email) > 25) {
      throw new UserException("User - Email is not valid.");
    }

    $this->_email = trim($email);
  }

  public function setPassword($password)
  {
    if (strlen($password) < 0 || strlen($password) > 255) {
      throw new UserException("User - Password is not valid.");
    }

    $this->_password = $password;
  }

  public function setDisabled($disabled){
    if ($disabled != 0 & $disabled != 1){
      throw new UserException("User - Disabled must be boolean value.");
    }

    $this->_disabled = $disabled;
  }

  public function setLoginAttempts($loginAttempts){
    if ($loginAttempts >= 3){
      throw new UserException("User's account is currently locked out");
    }

    $this->_loginAttempts = $loginAttempts;
  }

  // GETTERS 

  public function getId()
  {
    return $this->_id;
  }

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

  public function getBirthDate()
  {
    return $this->_birthDate;
  }

  public function getJmbg()
  {
    return $this->_jmbg;
  }

  public function getPhoneNumber()
  {
    return $this->_phoneNumber;
  }

  public function getEmail()
  {
    return $this->_email;
  }

  public function getRole()
  {
    return $this->_role;
  }

  public function getPassword()
  {
    return $this->_password;
  }

  public function isDisabled()
  {
    return !!$this->_disabled;
  }

  public function getLoginAttempts()
  {
    return $this->_loginAttempts;
  }
}
