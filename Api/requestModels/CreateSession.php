<?php

class SessionException extends Exception{};

class CreateSession
{
    private $_email;
    private $_password;

    public function __construct($reqBody)
    {
        if (!isset($reqBody->email)){
            throw new SessionException("Session - Email is not defined.");
        }

        if (!isset($reqBody->password)) {
            throw new SessionException("Session - Password is not defined.");
        }
        $this->setEmail($reqBody->email);
        $this->setPassword($reqBody->password);
    }

    public function asArray()
    {
        $session['email'] = $this->getEmail();
        $session['password'] = $this->getPassword();
        return $session;
    }

    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !isset($email) || strlen($email) < 0 || strlen($email) > 25) {
            throw new SessionException("Session - Email is not valid.");
        }

        $this->_email = trim($email);
    }

    public function setPassword($password)
    {
        if (strlen($password) < 6) {
            throw new SessionException("Session - Password must be at least 6 character long.");
        }

        if (strlen($password) > 16) {
            throw new SessionException("Session - Password can't be longer then 16 characters.");
        }

        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->_password = $password;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function getPassword()
    {
        return $this->_password;
    }
}
