<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SignupController extends Controller{

    public function IndexAction(){

    }
    
    public function registerAction(){
        $user = new Users();
        $formData = $this->request->getPost();
        $password = $this->security->hash($formData['password']);
        $email = $formData['email'];
        $role = $formData['role'];
        $user->assign(
            $formData,
            [
                'name',
                'email',
                'password', 
                'role'
            ]
        );
        $user->password = $password;
        // Replace these values with your own configuration
        $allowedRoles = ['admin', 'customer', 'guest'];

        // Validate role against the allowed roles
        if (!in_array($role, $allowedRoles)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role']);
            exit;
        }

        // Generate JWT token
        $payload = [
            'sub' => $role, // Subject parameter (username in this case)
            'user' => $email,    // Custom claim 'role'
            'exp' => time() + (60 * 60) // Token expiration time (1 hour)
        ];
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $success = $user->save();
        $jwt = JWT::encode($payload, $passphrase);echo '<pre>';print_r($jwt); die(__METHOD__);
        $this->view->success = $success;

        if($success){
            $this->view->message = "Register succesfully";
        } else {
            $this->view->message = "Not Register succesfully due to following reason: <br>".implode("<br>", $user->getMessages());
        }
    }
}