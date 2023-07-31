<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;
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


                // Check if the role is allowed to access the requested controller and action
                if (!$acl->isAllowed($role, $controllerName, 'index')) {
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

            $aclFilePath = $aclDir . '/acl.cache';

            if (!is_file($aclFilePath)) {
            // ... (create and setup ACL object as before)
            $acl = new Memory();
            $acl->addRole('admin');
            $acl->addRole('customer');
            $acl->addRole('guest');

            $acl->addComponent(
                'products',
                [
                    'index',
                    'add'
                ]
            );
            $acl->addComponent(
                'orders',
                [
                    'index',
                    'add'
                ]
            );
   
            $acl->addComponent(
                'settings',
                [
                    'index',
                    'update'
                ]
            );
   
            // Admin will have access to all (product add/edit, order add/edit, settings and all the above pages)
            // manager will have access to product add/edit and order add/edit
            // guest will have access to only product view
            $acl->allow('admin', '*', '*');
            $acl->allow('customer', 'products', ['index','add']);
            $acl->allow('customer', 'orders', ['index','add']);
            $acl->allow('guest', 'products', ['index']);
            $acl->deny('guest','*','*');
                // Save the ACL object to the cache file
            file_put_contents($aclFilePath, serialize($acl));

            echo 'ACL data has been created and saved to acl.cache file.';
            }
        }
    }
}