<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Set credentials */
$cred = set_cred('ddwt19', 'ddwt19');

/* Connect to DB */
$db = connect_db('localhost', 'ddwt19_week3', 'ddwt19', 'ddwt19');

/* Create Router instance */
$router = new \Bramus\Router\Router();

// Credential check
$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
    // Validate authentication
    $login = set_cred($_SERVER['HTTP_USERNAME'], $_SERVER['HTTP_PASSWORD']);
    if (!check_cred($login, $cred)){
        http_content_type();
       echo json_encode([
            'type' => '401',
            'message' => 'Unauthorised Request'
        ]);
       exit();
    }
});

// Add routes here
$router->mount('/api', function() use ($router, $db){
    // will result in /api/
    $router->get('/', function (){
       echo 'api overview';
   });
    // will result in /api/series
    $router->get('/series', function() use($db){
        http_content_type();
        $data = get_series($db);
        echo json_encode($data);
    });
    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        http_content_type();
        $data = get_serie_info($db, $id);
        echo json_encode($data);
    });
    $router->delete('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        http_content_type();
        $result = remove_serie($db, $id);
        echo json_encode($result);
    });
    $router->post('/series/', function () use($db){
        http_content_type();
        $serie_info = $_POST;
        $result = add_serie($db, $serie_info);
        echo json_encode($result);
    });
    $router->put('/series/(\d+)', function ($id) use($db){
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        http_content_type();
        $serie_info = $_PUT + ["serie_id" => $id];
        $result = update_serie($db, $serie_info);
        echo json_encode($result);
    });
});

// Error handling
$router->set404(function (){
    header('HTTP/1.1 404 Not Found');
    echo 'Error 404: Page not found';
});
/* Run the router */
$router->run();
