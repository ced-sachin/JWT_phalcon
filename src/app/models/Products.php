<?php

use Phalcon\Mvc\Model;

class Products extends Model
{   public $id;
    public $productname;
    public $description;
    public $tag;
    public $price;
    public $stock;

    public function initialize()
    {
        $this->setSource('products'); // Set the table name explicitly (optional if table name matches the model class name)
    }

    public function getId()
    {
        return $this->id; 
    }

    public function getProductname()
    {
        return $this->productname; 
    }

    public function getDescription()
    {
        return $this->description; 
    }

    public function getTag()
    {
        return $this->tag; 
    }

    public function getPrice()
    {
        return $this->price; 
    }

    public function getStock()
    {
        return $this->stock; 
    }
}