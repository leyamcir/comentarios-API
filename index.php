<?php

require 'Slim/Slim.php';
require_once 'DbHandler.class.php';

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


$app->post('/inicializar', function() use ($app) {
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
        $response["message"] = "Ha ocurrido un error.";
        appResponse(404, $response);
    }
});


$app->post('/datos_prueba(/:num_comments)', function($num_comments = 10) use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->addData($num_comments);

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


$app->get('/comentarios/favoritos', function() use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->getFavoriteComments();

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
        $response["message"] = "No se encuentra ningún comentario marcado como favorito.";
        appResponse(404, $response);
    }
});


$app->get('/comentarios/:id', function($id_comment) use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->getComment($id_comment);

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
        $response["message"] = "No se encuentra ningún comentario con id = ".$id_comment.".";
        appResponse(404, $response);
    }
})->conditions(array('id' => '[0-9]+'));


$app->get('/comentarios/:user', function($username) use ($app) {
    $response = array();
    $dbh = new DbHandler();

    try{
        //Get comments
        $result = $dbh->getAuthorComments($username);

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
        $response["message"] = "No se encuentra ningún comentario con id = ".$id_comment.".";
        appResponse(404, $response);
    }

});



$app->post('/comentarios/:user', function($username) use ($app) {
    $response = array();
    $dbh = new DbHandler();

    $comment = $app->request()->params('comment');

    if(strlen(@$comment) >0){
        try{
            //Add new comment
            $result = $dbh->addComment($username, $comment);

        } catch(Exception $e) {

            $response["error"] = true;
            $response["message"] = $e->getMessage();
            appResponse(404, $response);

            $app->stop();
        }

        if ($result) {
            $response["error"] = false;
            $response["message"] = "Su comentario ha sido añadido.";
            appResponse(200, $response);

        } else {
            $response["error"] = true;
            $response["message"] = "No se ha podido añadir el comentario.";
            appResponse(404, $response);
        }
    } else {
        $response["error"] = true;
        $response["message"] = "Debe indicar un comentario con el parámetro 'comment'.";
        appResponse(404, $response);
    }

});


$app->post('/comentarios/:user/favoritos/:id', function($username, $id_comment) use ($app) {
    $response = array();
    $dbh = new DbHandler();
    try{
        //Get comments
        $result = $dbh->addFavoriteComment($username, $id_comment);

    } catch(Exception $e) {

        $response["error"] = true;
        $response["message"] = $e->getMessage();
        appResponse(404, $response);

        $app->stop();
    }

    if ($result) {
        $response["error"] = false;
        $response["message"] = "El comentario ha sido añadido como favorito.";
        appResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "No se ha podido añadir el comentario como favorito.";
        appResponse(404, $response);
    }

})->conditions(array('id' => '[0-9]+'));


$app->get('/comentarios/:user/favoritos', function($username) use ($app) {
    $response = array();
    $dbh = new DbHandler();
    try{
        //Get comments
        $result = $dbh->getAuthorFavoriteComments($username);

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
        $response["message"] = "No se encuentran favoritos para este usuario.";
        appResponse(404, $response);
    }

});


$app->run();

?>
