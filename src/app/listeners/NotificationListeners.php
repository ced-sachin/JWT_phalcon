<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;
use Phalcon\Acl\Enum;

// use App\Controllers\SecuresController;

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

            // Check if unserialization was successful and the ACL object is valid
            if ($acl instanceof \Phalcon\Acl\Adapter\Memory) {
                $role = $application->request->getQuery('role');
                $controllerName = $application->dispatcher->getControllerName();
                $actionName = $application->dispatcher->getActionName(); // Get the action name
                
                $actionName = isset($actionName) ? $actionName : 'index'; //
                // Check if the role is allowed to access the requested controller and action
                if (!$acl->isAllowed($role, $controllerName, $actionName )) {
                    echo 'Access Denied :(';
                    die;
                }
            } else {
                // Handle the case when unserialization fails or the ACL object is not valid
                echo 'Error: Unable to retrieve valid ACL data.';
                die;
            }
        } else {
            $aclDir = APP_PATH . '/security';
            if (!is_dir($aclDir)) {
                mkdir($aclDir, 0777, true);
            }

            // $aclFilePath = fopen( $aclDir . '/acl.cache', 'w+' );
            $aclFilePath = $aclDir . '/acl.cache';

            if (!is_file($aclFilePath)) {
            // ... (create and setup ACL object as before)
            $acl = new Memory();
            // $acl->setDefaultAction(Enum::ALLOW);
            $admin     = new Role('admin', 'Administrator Access');
            $customer = new Role('customer', 'Manager Department Access');
            $guest = new Role('guest', 'normal user');
            $acl->addRole($admin );
            $acl->addRole($customer);
            $acl->addRole( $guest);

            $acl->addComponent(
                'products',
                [
                    'view',
                    'add'
                ]
            );
            $acl->addComponent(
                'orders',
                [
                    'view',
                    'add'
                ]
            );
   
            $acl->addComponent(
                'settings',
                [
                    'view',
                    'update'
                ]
            );

            // Assuming the correct component name is 'some_controller' (replace it with your actual component name)
            $acl->addComponent(
                'index',
                [
                    'index',  // Add other actions as needed
                ]
            );
            $acl->setDefaultAction(\Phalcon\Acl\Enum::ALLOW);
            // Allow 'guest' role to access 'some_controller' actions
            $acl->allow('*', 'index', 'index');   
            $acl->allow('admin', '*', '*');
            $acl->allow('guest', 'products' , ['view']);
            $acl->allow('customer', 'products' , '*');
            $acl->allow('customer', 'orders' ,  '*');
            // Allow guest role to access the default controller and action           
            // Save the ACL object to the cache file
            file_put_contents($aclFilePath, serialize($acl));

            echo 'ACL data has been created and saved to acl.cache file.';
            }
        }
    }
}