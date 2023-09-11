<?php

use Phalcon\Mvc\Controller;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;


class ProductsController extends Controller
{
    public function viewAction()
    {   
        $this->view->products = Products::find();
    }

    public function addAction()
    {   
        $form = new Form();
        $formData = $this->request->getPost();

        if(!empty($formData)) {
            $product = new Products();
            $product->assign(
                $formData,
                [
                    'productname',
                    'description',
                    'tag', 
                    'price',
                    'stock'
                ]
            );
            // Set the hashed password to the 'password' attribute of the model
            try{ 
                $success = $product->save();
            } catch(\Exception $e) {
                print_r($e->getMessage()." exception at line no. ". $e->getLine()." in file ". $e->getFile()); 
            } catch(\Error $e) {
                print_r($e->getMessage()." error at line no. ". $e->getLine()." in file ". $e->getFile());
            }
            $this->view->success = $success;

            if($success){
                $this->view->message = "Product added successfully";
            }else{
                $this->view->message = "Product not added due to following reason: <br>".implode("<br>", $product->getMessages());
            } 
        }  
    }

}