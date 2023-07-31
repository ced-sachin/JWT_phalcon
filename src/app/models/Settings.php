<?php

use Phalcon\Mvc\Model;

class Settings extends Model
{  
    public $id;
    public $titleoptimization;
    public $defaultprice;
    public $defaultzipcode;
    public $defaultstock;

    public function initialize()
    {
        $this->setSource('settings'); // Set the table name explicitly (optional if table name matches the model class name)
    }

    public function getTitleoptimization()
    {
        return $this->titleoptimization;
    }

    public function getDefualtprice() {
        return $this->defaultprice;
    }

    public function getDefualtzipcode() {
        return $this->defaultzipcode;
    }

    public function getDefaultstock() {
        return $this->defaultstock;
    }
}