<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function login($email, $password)
    {
        // Your authentication logic goes here...
        // For example, check user credentials against the 'Users' model in the database.

        $user = Users::findFirst([
            'conditions' => 'email = :email:',
            'bind' => ['email' => $email]
        ]);
        if ($user && password_verify($password, $user->readAttribute('password'))) {
            // Valid credentials, return the user object.
            // echo 'Valid credentials'; die(__METHOD__);

            return $user;
        }

        // Authentication failed, return null.
        return null;
    }
}