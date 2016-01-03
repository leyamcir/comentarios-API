<?php

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/comentarios', 'showData');
$app->get('/comentarios_pretty', 'showData2');
$app->post('/crear', 'initialize');
$app->post('/datos_prueba', 'addData');
$app->run();

function initialize(){
// connect
    $m = new MongoClient();
// select your database
    $db = $m->api_comments3;
// select your collection
    $collection = $db->counters;
// add a record
    $document = array(
    	"_id" => "comment_id",
    	"seq" => 0
    );
    $collection->insert($document);

    echo "Inicialización realizada con éxito.";
}


function addData()
{
// connect
    $m = new MongoClient();
// select your database
    $db = $m->api_comments3;
// select your collection
    $collection = $db->comments;
// add a record
    $fun = 'function getNextSequence(name) {
		var ret = db.counters.findAndModify(
			{
			query: { _id: name },
			update: { $inc: { seq: 1 } },
			new: true
			}
		);

	   return ret.seq;
	}';

	$scope = array();
	$code = new MongoCode($fun, $scope);

	$fun_exec = $db->execute($code, array("comment_id"));

    $document = array(
    	"_id" => $fun_exec['retval'],
    	"content" => "First test post",
    	"author" => "author1",
    	"date" => new MongoDate()
    );
    $collection->insert($document);

    $fun_exec = $db->execute($code, array("comment_id"));

// add another record
    $document = array(
    	"_id" => $fun_exec['retval'],
    	"content" => "Second test post",
    	"author" => "author2",
    	"date" => new MongoDate()
    );
    $collection->insert($document);

    echo "Añadidos datos de prueba";
}

function showData(){
// connect
    $m = new MongoClient();
// select your database
    $db = $m->api_comments3;
// select your collection
    $collection = $db->comments;
// find everything in the collection
    $cursor = $collection->find();
// Show the result here
    $result = array();
    foreach ($cursor as $document) {
        $result[] = $document;
    }

    echo json_encode($result);

}

function showData2()
{
// connect
    $m = new MongoClient();
// select your database
    $db = $m->api_comments3;
// select your collection
    $collection = $db->comments;
// find everything in the collection
    $cursor = $collection->find();
// Show the result here
    $result = array();
    foreach ($cursor as $document) {
        echo json_encode($document)."\n";
    }

}


?>
