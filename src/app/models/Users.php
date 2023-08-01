<?php

use Phalcon\Mvc\Model;


// CREATE TABLE `dbphalcon`.`users` (
//     `id` int NOT NULL,
//     `name` varchar(255) NOT NULL,
//     `email` varchar(255) NOT NULL,
//     `password` varchar(255) NOT NULL,
//     `role` varchar(255) NOT NULL
//   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
//   ALTER TABLE `dbphalcon`.`users` ADD PRIMARY KEY (`id`);
//   ALTER TABLE `dbphalcon`.`users` MODIFY `id` int NOT NULL AUTO_INCREMENT;
    
//   SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
    
//   INSERT INTO `dbphalcon`.`users`(`id`, `name`, `email`, `password`, `role`) SELECT `id`, `name`, `email`, `password`, `role` FROM `dbphalcon`.`users`;

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