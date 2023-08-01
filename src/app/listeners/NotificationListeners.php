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
            $bearer = $request->getQuery('bearer');
            if(!$bearer){
                $role = 'guest';
            }else{
                $parser = new JWT();
                $tokenObject = JWT::decode($bearer);
                $role = $tokenObject->sub; 
            }
                try{
                  
                    $controllerName = $application->dispatcher->getControllerName();
                    $actionName = $application->dispatcher->getActionName(); // Get the action name
                    
                    $actionName = isset($actionName) ? $actionName : 'index'; //
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
            $aclDir = APP_PATH . '/security';
            if (!is_dir($aclDir)) {
                mkdir($aclDir, 0777, true);
            }

            // $aclFilePath = fopen( $aclDir . '/acl.cache', 'w+' );
            $aclFilePath = $aclDir . '/acl.cache';

            if (!is_file($aclFilePath)) {
                // ... (create and setup ACL object as before)
                $acl = new Memory();

                $admin = new Role('admin', 'Administrator Access');
                $customer = new Role('customer', 'Manager Department Access');
                $guest = new Role('guest', 'Normal User');
                $acl->addRole($admin);
                $acl->addRole($customer);
                $acl->addRole($guest);
    
                // Components
                $components = [
                    'products' => ['view', 'add', 'edit'],
                    'orders'   => ['view', 'add', 'edit'],
                    'settings' => ['view', 'update'],
                    'index'    => ['home']
                ];
    
                // Allow guest role to access only 'products' component and its 'view' action
                $acl->addComponent('products', ['view']);
                $acl->allow('guest', 'products', 'view');
                $acl->addComponent('index', ['home']);
                $acl->allow('guest', 'index', 'home');
    
                // Allow customer role to access 'products' and 'orders' components and all actions
                foreach ($components as $component => $actions) {
                    $acl->addComponent($component, $actions);
                    $acl->allow('customer', $component, $actions);
                }
    
                // Allow admin role to access all components and actions
                $acl->allow('admin', '*', '*');
                file_put_contents($aclFilePath, serialize($acl));
                echo 'ACL data has been created and saved to acl.cache file.';
            }
        }
    }
}