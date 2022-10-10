<?php

include_once("../models/User.php");

class CreateUser
{
    private $_name;
    private $_surname;
    private $_gender;
    private $_birthPlace;
    private $_birthDate;
    private $_jmbg;
    private $_phoneNumber;
    private $_email;
    private $_password;
    private $_role;

    public function __construct($writeDB, $reqBody, $role)
    {
        if (!isset($reqBody->name)){
            throw new UserException("User - name is not set.");
        }
        $this->setName($reqBody->name);

        if (!isset($reqBody->surname)){
            throw new UserException("User - surname is not set.");
        }
        $this->setSurname($reqBody->surname);
        
        if (!isset($reqBody->gender)){
            throw new UserException("User - gender is not set.");
        }
        $this->setGender($reqBody->gender);

        if (!isset($reqBody->birthPlace)){
            throw new UserException("User - birthPlace is not set.");
        }
        $this->setBirthPlace($reqBody->birthPlace);
        
        if (!isset($reqBody->birthDate)){
            throw new UserException("User - birthDate is not set.");
        }
        $this->setBirthDate($reqBody->birthDate);
        
        if (!isset($reqBody->jmbg)){
            throw new UserException("User - jmbg is not set.");
        }
        $this->setJMBG($reqBody->jmbg);

        
        if (!isset($reqBody->phoneNumber)){
            throw new UserException("User - phoneNumber is not set.");
        }
        $this->setPhoneNumber($reqBody->phoneNumber);
        
        if (!isset($reqBody->email)){
            throw new UserException("User - email is not set.");
        }
        $this->setEmail($reqBody->email);
        
        if (!isset($reqBody->password)){
            throw new UserException("User - password is not set.");
        }
        $this->setPassword($reqBody->password);

        $this->userAlreadyExists($writeDB, $this->getEmail(), $this->getJmbg(), $this->getPhoneNumber());
        
        $this->setRole($writeDB, $role);
    }
    
    private function userAlreadyExists($writeDB, $email, $jmbg, $phoneNumber)
    {
        try {
            $query = $writeDB->prepare("SELECT *
                                        FROM user
                                        WHERE Email = '$email'");
            $query->execute();
            
            $rowCount = $query->rowCount();
            if ($rowCount !== 0) {
                throw new UserException("User or doctor already created for the given email.");
            }

            $query = $writeDB->prepare("SELECT *
                                        FROM user
                                        WHERE JMBG = '$jmbg'");
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount !== 0) {
                throw new UserException("User or doctor already created for the given JMBG.");
            }

            $query = $writeDB->prepare("SELECT *
                                        FROM user
                                        WHERE JMBG = '$phoneNumber'");
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount !== 0) {
                throw new UserException("User or doctor already created for the given phone number.");
            }
            
        } catch (PDOException $ex) {
            $response = new Response(false, 500);
            $response->addMessage("Unable to check if user already exists. " . $ex->getMessage());
            $response->send();
            exit();
        }
    }

    public function asArray()
    {
        $user = array();
        $user['name'] = $this->getName();
        $user['surname'] = $this->getSurname();
        $user['gender'] = $this->getGender();
        $user['birthPlace'] = $this->getBirthPlace();
        $user['birthDate'] = $this->getBirthDate();
        $user['jmbg'] = $this->getJmbg();
        $user['phoneNumber'] = $this->getPhoneNumber();
        $user['email'] = $this->getEmail();
        // $user['password'] = $this->getPassword();
        $user['role'] = $this->getRole();

        return $user;
    }

    // SETTERS

    public function setName($name)
    {
        if (strlen($name) < 0 || strlen($name) > 25) {
            throw new UserException("User - Name is not valid.");
        }

        $this->_name = trim($name);
    }

    public function setSurname($surname)
    {
        if (strlen($surname) < 0 || strlen($surname) > 25) {
            throw new UserException("User - Surame is not valid.");
        }

        $this->_surname = trim($surname);
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

        $this->_phoneNumber = trim($phoneNumber);
    }

    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !isset($email) || strlen($email) < 0 || strlen($email) > 25) {
            throw new UserException("User - Email is not valid.");
        }

        $this->_email = trim($email);
    }

    public function setPassword($password)
    {
        if (strlen($password) < 6) {
            throw new UserException("User - Password must be at least 6 character long.");
        }

        if (strlen($password) > 16) {
            throw new UserException("User - Password is can't be longer then 16 characters.");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $this->_password = $hashedPassword;
    }

    public function setRole($writeDB, $role)
    {
        if (!empty($role)) {
            $this->_role = $role;
        } else {
            $this->_role = $this->isFristUser($writeDB) ? "Admin" : "Patient";
        }
    }

    private function isFristUser($writeDB)
    {
        try {
            $query = $writeDB->prepare("SELECT *
            FROM user");
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                return true;
            }
            return false;
        } catch (PDOException $ex) {
            $response = new Response(false, 500);
            $response->addMessage("Unable to check if user is the first user. " . $ex->getMessage());
            $response->send();
            exit();
        }
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

    public function getPassword()
    {
        return $this->_password;
    }

    public function getRole()
    {
        return $this->_role;
    }
}
