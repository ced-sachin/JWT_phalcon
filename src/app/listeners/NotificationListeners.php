<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;
use Phalcon\Acl\Enum;
use \Firebase\Authentication\JWT;

class NotificationListeners 
{
    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application) {
        
        $aclFile = APP_PATH . '/security/acl.cache';
        if (is_file($aclFile)) {
            // Retrieve the serialized ACL object from the cache file
            $aclData = file_get_contents($aclFile);
            // print_r($aclData); //die('data');
            // Unserialize the ACL object
            $acl = unserialize($aclData);
            
            $request = $application->request;

            // Get the JWT token from the query parameter "bearer"
            $bearer = "<script>document.write(localStorage.getItem('token'));</script>"; 
            // die($bearer);
            var_dump($bearer); die('__FILE__');
            if($bearer===null){
                $role = 'guest';
            }else{
                $parser = new JWT();
                $tokenObject = JWT::decode($bearer);
                $role = $tokenObject->sub; 
            }

                try{                  
                    $controllerName = $application->dispatcher->getControllerName();
                    $actionName = $application->dispatcher->getActionName(); // Get the action name
                    // echo $controllerName; die(__FILE__);
                    $controllerName = isset($controllerName) ? $controllerName : 'index'; 
                    $actionName = isset($actionName) ? $actionName : 'home'; //
                    // Check if the role is allowed to access the requested controller and action
                    if (!$acl->isAllowed($role, $controllerName, $actionName )) {
                        echo 'Access Denied :(';
                        die;
                    }
                } catch(\Exception $e) {
                    echo $e->getMessage();
                    die($e->getFile());
                }
        }else {
            echo 'acl file not found';
        }
    }
}