<?php

use Phalcon\Mvc\Controller;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;


class OrdersController extends Controller
{
    public function indexAction()
    {  
       $this->view->orders = Orders::find();
    }

    public function addAction()
    {  
        $formData = $this->request->getPost();
        $settings = Settings::find(1);
        if(!empty($formData)) {
            
            $order = new Orders();
            $order->assign(
                $formData,
                [
                    'customername',
                    'customeraddress',
                    'zipcode', 
                    'product',
                    'quantity'
                ]
            );     
            
            $defaultZipcode = $settings->getDefualtzipcode();
            $defaultQuantity = $settings->getDefualtquantity();  
            $order->zipcode = !empty($defaultZipcode) ?  $defaultZipcode : $formData['zipcode'];
            $order->quantity = !empty($defaultQuantity) ? $defaultQuantity : $formData['quantity']; 
            
            try {
                $success = $order->save();
            } catch(\Exception $e) {
                print_r($e->getMessage()." exception at line no. ". $e->getLine()." in file ". $e->getFile()); die(__METHOD__);
            } catch(\Error $e) {
                print_r($e->getMessage()." error at line no. ". $e->getLine()." in file ". $e->getFile()); die(__METHOD__);
            }
            $this->view->success = $success;

            if($success){
                $this->view->message = "Order Created Successfully";
            }else{
                $this->view->message = "Order not created due to following reason: <br>".implode("<br>", $order->getMessages());
            } 
        }              
    }
}