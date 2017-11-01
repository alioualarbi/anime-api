<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-07-29
 * Time: 1:37 AM
 */

header('Content-Type: application/json; charset=utf-8');

mb_internal_encoding("UTF-8");

error_reporting(-1);
ini_set('display_errors', 'On');

define('BASE_PATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro\Collection as MicroCollection;
use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;

/**
 * The FactoryDefault Dependency Injector automatically registers the services that
 * provide a full stack framework. These default services can be overidden with custom ones.
 */
$di = new FactoryDefault();

/**
 * Include Services
 */
include BASE_PATH . '/config/services.php';

/**
 * Load config
 */
$config = $di->get('config');

/**
 * Include Autoloader
 */
include BASE_PATH . '/config/loader.php';

/**
 * Starting the application
 * Assign service locator to the application
 */
$app =  new Micro($di);

// Include controllers
$app['controllers'] = function() {
    return [
        'index' => true,
        'anime' => true,
        'episode' => true,
        'genre' => true,
        'video' => true,
        'auth' => true
    ];
};

//Add token authentication
$auth = new AuthMicro($app);

//Check permissions
$auth->onCheck(function($auth) use ($app){
    // to get the payload
    $data = $auth->data();
    $method = $app->request->getMethod();
    if( ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') && ($data['role'] !== 'admin') ){
        $auth->appendMessage('Unauthorized User');
        return false;
    }
    return true;
});

/**
 * IndexController
 */
if ($app['controllers']['index']) {
    $index = new MicroCollection();
    // Set the main handler & prefix
    $index->setHandler( 'IndexController', true);
    // Set routers
    $index->get('/', 'index');
    $app->mount($index);

}

/**
 * AnimeController
 */
if ($app['controllers']['anime']) {

    $anime = new MicroCollection();

    // Set the main handler & prefix
    $anime->setHandler( 'AnimeController', true);
    $anime->setPrefix('/anime');

    // Set routers
    $anime->get('/', 'index');
    $anime->get('/{id:[0-9]+}', 'findById');
    $anime->get('/{id:[0-9]+}/episodes', 'Episodes');
    $anime->get('/latest', 'latestAnime');
    $anime->get('/latest/{limit:[0-9]+}', 'latestAnime');
    $anime->get('/ongoing', 'ongoingAnime');
    $anime->get('/search/{keyword:[0-9a-zA-Z\ \']+}', 'searchAnime');

    $anime->post('/','create');
    $anime->put('/{id:[0-9]+}','update');
    $anime->delete('/{id:[0-9]+}','delete');

    $app->mount($anime);

}

/**
 * EpisodeController
 */
if ($app['controllers']['episode']) {

    $episode = new MicroCollection();

    // Set the main handler & prefix
    $episode->setHandler('EpisodeController', true);
    $episode->setPrefix('/episode');

    // Set routers
    $episode->get('/', 'index');
    $episode->get('/{id:[0-9]+}', 'findById');
    $episode->get('/latest', 'latestEpisodes');
    $episode->get('/latest/{limit:[0-9]+}', 'latestEpisodes');
    $episode->get('/ongoing', 'ongoingEpisodes');
    $episode->get('/ongoing/{limit:[0-9]+}', 'ongoingEpisodes');


    $episode->post('/','create');
    $episode->put('/{id:[0-9]+}','update');
    $episode->delete('/{id:[0-9]+}','delete');

    $app->mount($episode);
}

/**
 * GenreController
 */
if ($app['controllers']['genre']) {

    $genre = new MicroCollection();

    // Set the main handler & prefix
    $genre->setHandler('GenreController', true);
    $genre->setPrefix('/genre');

    // Set routers
    $genre->get('/', 'index');
    $genre->get('/{genre:[a-z-A-Z\ \-]+}', 'get');

    $app->mount($genre);
}

/**
 * AuthController
 */
if ($app['controllers']['auth']) {

    $auth = new MicroCollection();

    // Set the main handler & prefix
    $auth->setHandler('AuthController', true);
    $auth->setPrefix('/auth');

    // Set routers
    $auth->get('/token', 'guestToken');
    $auth->post('/secure', 'secureToken');

    $app->mount($auth);
}

/**
 * Handle 404's
 */
$app->notFound(
    function () use ($app) {
        $app->response->setStatusCode(404, 'Not Found');
        $app->response->sendHeaders();
        $message = 'Nothing to see here. Move along....';
        $app->response->setContent($message);
        $app->response->send();
    }
);

$app->handle();