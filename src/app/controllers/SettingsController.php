<?php


use Phalcon\Mvc\Controller;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;



class SettingsController extends Controller
{
    public function viewAction()
    {  
        $settings = Settings::findFirst(1);
        if($settings) {
            $this->view->settings = Settings::findFirst(1);
        }else {
            $this->view->settings = null;
        }
    }

    public function updateAction()
    {  
        $formData = $this->request->getPost();

        // Find the existing record to update (assuming 'id' is the primary key)
        $settingExists = Settings::findFirst(1);
        if(!empty($formData)) {
            if (!$settingExists) {
              $setting = new Settings();
            } else {
              $setting = Settings::findFirst(1);   
            }
            $setting->assign(
                $formData,
                [
                    'titleoptimization',
                    'defaultprice',
                    'defaultzipcode', 
                    'defaultstock',
                ]
            );
            // Set the hashed password to the 'password' attribute of the model
            try{ 
                $success = $setting->save();
            } catch(\Exception $e) {
                print_r($e->getMessage()." exception at line no. ". $e->getLine()." in file ". $e->getFile()); 
            } catch(\Error $e) {
                print_r($e->getMessage()." error at line no. ". $e->getLine()." in file ". $e->getFile());
            }
            $this->view->success = $success;

            if($success === true) {
                $this->view->message = "Settings updated successfully";
                return $this->response->redirect('settings/index');
            } else {
                $this->view->message = "Settings not updated due to following reason: <br>".implode("<br>", $setting->getMessages());
                return $this->response->redirect('settings/index');
            } 
        }  
    }
}