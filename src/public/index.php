<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use App\Controllers\SecureController;


$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
// require_once __DIR__ . '/../vendor/firebase/php-jwt/src/JWT.php';
// // require_once __DIR__ . '/../vendor/autoload.php';
// require_once __DIR__ . '/../vendor/firebase/php-jwt/src/SignatureInvalidException.php';

// Register an autoloader
$loader = new Loader();



use Firebase\JWT\JWT;

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerFiles([ BASE_PATH . "/vendor/autoload.php" ]);
$loader->registerDirs(array(
    '../app/controllers/',
    '../app/models/',
    '../app/plugins/',
))->register();

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH.'/components',
        'App\Listeners' => APP_PATH.'/listeners'
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->setShared('secureController', function () {
    return new SecureController();
});

$application = new Application($container);

$eventsManager = new EventsManager();

$eventsManager->attach(
    'application:beforeHandleRequest',
    new App\Listeners\NotificationListeners()
);

$eventsManager->fire('application:beforeHandleRequest', $application);

$container->set(
    'eventsManager',
    $eventsManager
);

$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'jwt_phalcon-mysql-server-1',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'dbphalcon',
                ]
            );
        }
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
