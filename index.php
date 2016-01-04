<?php

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function appResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}


$app->get('/', function() use($app) {
    $app->response->setStatus(200);
    echo "Welcome to Slim 2.0 based API";
});


$app->post('/crear', function() use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->initialize();

    } catch(Exception $e) {

        $response["error"] = true;
        $response["message"] = $e->getMessage();
        appResponse(404, $response);

        $app->stop();
    }

    if ($result) {
        $response["message"] = "Inicialización realizada con éxito.";
        appResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "Ha ocurrido un error";
        appResponse(404, $response);
    }
});


$app->post('/datos_prueba', function() use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->addData();

    } catch(Exception $e) {

        $response["error"] = true;
        $response["message"] = $e->getMessage();
        appResponse(404, $response);

        $app->stop();
    }

    if ($result) {
        $response["message"] = "Datos de prueba añadidos con éxito.";
        appResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "Ha ocurrido un error";
        appResponse(404, $response);
    }
});


$app->get('/comentarios', function() use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->getComments();

    } catch(Exception $e) {

        $response["error"] = true;
        $response["message"] = $e->getMessage();
        appResponse(404, $response);

        $app->stop();
    }

    if ($result != NULL) {
        appResponse(200, $result);

    } else {
        $response["error"] = true;
        $response["message"] = "No hay comentarios disponibles.";
        appResponse(404, $response);
    }
});


$app->run();

?>
