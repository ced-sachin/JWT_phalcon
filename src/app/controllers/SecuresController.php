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
        $aclFile = APP_PATH.'/security/acl.cache';
        
        if(true !== is_file($aclFile)) {
            $acl = new Memory();
            $acl->addRole('admin');
            $acl->addRole('customer');
            $acl->addRole('guest');

            $acl->addComponent(
                'product',
                [
                    'index',
                    'add'
                ]
            );
            $acl->addComponent(
                'order',
                [
                    'index',
                    'add'
                ]
            );
   
            $acl->addComponent(
                'setting',
                [
                    'index',
                    'update'
                ]
            );
   
            $acl->allow('admin', '*', '*');
            $acl->allow('customer', 'product', ['index','add']);
            $acl->allow('customer', 'order', ['index','add']);
            $acl->allow('guest', 'product', ['index']);
            $acl->deny('guest','*','*');
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
            // echo '<pre>'; print_r($aclFile); die(__METHOD__);
        } else {
            $acl = unserialize(
                file_get_contents($aclFile)
            );
        }

        if(true === $acl->isAllowed('customer','product','index')) {
            echo 'Access granted';
        } else {
            echo 'Access denied :(';
        }
    }
}