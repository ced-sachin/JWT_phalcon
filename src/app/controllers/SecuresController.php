<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;


class SecuresController extends Controller
{
    public function buildaclAction()
    {
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