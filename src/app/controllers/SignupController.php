<?php

use Phalcon\Mvc\Controller;
use \Firebase\Authentication\JWT;
use Phalcon\Security\Random;

class SignupController extends Controller{

    public function IndexAction(){

    }
    
    public function registerAction(){
        $user = new Users();
        $formData = $this->request->getPost();
        $password = $this->security->hash($formData['password']);
        $email = $formData['email'];
        $role = $formData['role'];
        $name = $formData['name'];
        
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
            'sub'=> $role, 
            'user' => $name,    // Custom claim 'role'
            'exp' => time() + (60 * 60) // Token expiration time (1 hour)
        ];
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $success = $user->save();
        $jwt = Jwt::encode($payload, $passphrase);//echo '<pre>';print_r($jwt);//die(__METHOD__);
        // // Store the JWT token in the session or DI container
        // $this->session->set('jwtToken', $jwt); // If using Phalcon session
        // // Alternatively, store the JWT token in the DI container
        // $this->container->setShared('jwtToken', $jwt);
        $this->view->success = $success;
        
        if($success){
            $this->view->message = "Register succesfully";
            echo "<script>localStorage.setItem('token', '$jwt');</script>";
            return $this->response->redirect('products/view?bearer=' . $jwt);
        } else {
            $this->view->message = "Not Register succesfully due to following reason: <br>".implode("<br>", $user->getMessages());
            return $this->response->redirect('index/index');
        }
    }
}