<?php

use Phalcon\Mvc\Model;
use Products;

class Orders extends Model
{   
    public $id;
    public $customername;
    public $customeraddress;
    public $zipcode;
    public $product;
    public $quantity;
    
    
    public function initialize()
    {
        $this->setSource('orders'); // Set the table name explicitly (optional if table name matches the model class name)
    }

    public function getId()
    {
        return $this->id; 
    }

    public function getProduct()
    {   
        return Products::findFirst($this->product)->getProductname(); 
    }

    public function getCustomeraddress() 
    {
        return $this->customeraddress;
    }

    public function getCustomername()
    {
        return $this->customername; 
    }

    public function getZipcode()
    {
        return $this->zipcode; 
    }

    public function getQuantity()
    {
        return $this->quantity; 
    }
}