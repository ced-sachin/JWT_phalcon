<?php

use Phalcon\Mvc\Controller;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;
use Phalcon\Translate\Adapter\NativeArray;

class IndexController extends Controller
{
    public function homeAction()
    {
        $this->view->t    = $this->getTranslator();
    }
    
    /**
     * @return NativeArray
     */
    private function getTranslator(): NativeArray
    {
        $language = 'fr';
        $messages = [];

        $translationFile = __DIR__ . '/../messages/'.$language.'.php';

        if (true !== file_exists($translationFile)) {
            $translationFile = __DIR__ . '/../messages/fr.php';
        }

        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);

        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}