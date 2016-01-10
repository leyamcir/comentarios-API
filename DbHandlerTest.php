<?php
include_once 'simpletest/unit_tester.php';
include_once 'simpletest/reporter.php';
include_once 'DbHandler.class.php';

class DbHandlerTest extends UnitTestCase{
	var $dbh;
	var $existent_comment;

	function DbHandlerTest($initialize = false){
		clearstatcache();
    	$this->dbh = new DbHandler();
    	if($initialize){
    		$this->dbh->initialize();
    		$this->dbh->addData(5);
    	}
    	echo "hola";
    	var_dump($initialize);
	}

	function setUp(){
		// will be called automatically before each and every test
	}

	function tearDown(){
		// will be called after each and every test
	}

	private function random_string($length) {
	    $key = '';
	    $keys = array_merge(range(0, 9), range('a', 'z'));

	    for ($i = 0; $i < $length; $i++) {
	        $key .= $keys[array_rand($keys)];
	    }

	    return $key;
	}

	function testAddComment(){
		$author = 'usuarioTest'.$this->random_string(5);
		$content = 'Prueba creación de comentario';

		echo "lala ";

		$this->assertTrue($this->dbh->addComment($author, $content));
		clearstatcache();

		$authorComments = $this->dbh->getAuthorComments($author);

		$lastUserComment = array_pop($authorComments);

		$this->existent_comment = $lastUserComment['_id'];
		$this->assertEqual($lastUserComment['content'], $content);
		$this->assertEqual($lastUserComment['author'], $author);
	}

	function testGetComments(){
		$this->assertIsA($this->dbh->getComments(), "array");
	}

	function testGetComment(){
		$this->assertIsA($this->dbh->getComment($this->existent_comment), "array");
	}

	function testAddFavoriteComment(){
		$author = 'usuarioTest'.$this->random_string(5);

		$this->assertTrue($this->dbh->addFavoriteComment($author, $this->existent_comment));
		clearstatcache();

		$authorFavoriteComments = $this->dbh->getAuthorFavoriteComments($author);
		$lastFavoriteComment = array_pop($authorFavoriteComments);
		$this->assertEqual($lastFavoriteComment['_id'], $this->existent_comment);

		$comment = $this->dbh->getComment($this->existent_comment);
		$lastFavoriteAuthor = array_pop($comment['fav_users']);
		$this->assertEqual($lastFavoriteAuthor, $author);
	}

}

$test = new DbHandlerTest(true);//Inicializa y añade datos al principio (borra datos anteriores)
$test->run(new HtmlReporter());

$test = new DbHandlerTest(false);//Prueba sin inicializar datos
$test->run(new HtmlReporter());

?>