<?php
/**
 * Class to handle all db operations
 */
if (!class_exists('DbHandler')) {
	class DbHandler {
		private $m;
		private $db;
		private $funGetNextSequence;

		//Constructor
	    function __construct() {
	        $this->m = new MongoClient();
	    	$this->db = $this->m->api_comments6;
	    	$this->funGetNextSequence = 'function getNextSequence(name) {
				var ret = db.counters.findAndModify(
					{
					query: { _id: name },
					update: { $inc: { seq: 1 } },
					new: true
					}
				);

			   return ret.seq;
			}';
	    }

	    //Public functions

	    public function initialize(){
		    $collection = $this->db->counters;

		    $document = array(
		        "_id" => "comment_id",
		        "seq" => 0
		    );
		    $collection->insert($document);

		    return true;
	    }

	    public function addData(){
		    $collection_comments = $this->db->comments;
		    $collection_users = $this->db->users;

			//$scope = array();
			$code = new MongoCode($this->funGetNextSequence);

			$fun_exec = $this->db->execute($code, array("comment_id"));

			//Add comment 1
		    $document = array(
		    	"_id" => $fun_exec['retval'],
		    	"content" => "First test post",
		    	"author" => "author1",
		    	"date" => new MongoDate()
		    );
		    $collection_comments->insert($document);

		    //Add author1 comment
		    $collection_users->update(
		    	array("_id"=> "author1"),
		    	array('$addToSet' => array("comments" => $fun_exec['retval'])),
		    	array("upsert" => true)
		    );


		    $fun_exec = $this->db->execute($code, array("comment_id"));

		// add another record
		    $document = array(
		    	"_id" => $fun_exec['retval'],
		    	"content" => "Second test post",
		    	"author" => "author2",
		    	"date" => new MongoDate()
		    );
		    $collection_comments->insert($document);

		    //Add author2 comment
		    $collection_users->update(
		    	array("_id"=> "author2"),
		    	array('$addToSet' => array("comments" => $fun_exec['retval'])),
		    	array("upsert" => true)
		    );

		    return true;
		}

	    public function getComments(){
	    	$collection = $this->db->comments;
	    	$cursor = $collection->find();

		    $result = array();
		    foreach ($cursor as $document) {
		        $result[] = $document;
		    }

		    return (count($result) > 0) ? $result : NULL;
	    }

	    public function getComment($id_comment){
	    	$collection = $this->db->comments;
	    	$cursor = $collection->find(
		        array(
		            '_id' => intval($id_comment)
		        )
		    );;

		    $result = array();
		    foreach ($cursor as $document) {
		        $result[] = $document;
		    }

		    return (count($result) > 0) ? $result : NULL;
	    }


		public function addComment($author, $content){
		    $collection_comments = $this->db->comments;
		    $collection_users = $this->db->users;

			//$scope = array();
			$code = new MongoCode($this->funGetNextSequence);

			$fun_exec = $this->db->execute($code, array("comment_id"));

			//Add comment
		    $document = array(
		    	"_id" => $fun_exec['retval'],
		    	"content" => $content,
		    	"author" => $author,
		    	"date" => new MongoDate()
		    );
		    $collection_comments->insert($document);

		    //Add author comment
		    $collection_users->update(
		    	array("_id"=> $author),
		    	array('$addToSet' => array("comments" => $fun_exec['retval'])),
		    	array("upsert" => true)
		    );

		    return true;
		}


		public function addFavoriteComment($author, $id_comment){
	    	$collection_comments = $this->db->comments;
		    $collection_users = $this->db->users;

		    //Add user favorite comment
		    $collection_users->update(
		    	array("_id"=> $author),
		    	array('$addToSet' => array("fav_comments" => $id_comment)),
		    	array("upsert" => true)
		    );

		    //Add comment favorited user
		    $collection_comments->update(
		    	array("_id"=> (int)$id_comment),
		    	array('$addToSet' => array("fav_users" => $id_comment))
		    );

		    return true;
		}

		public function getFavoriteComments(){
	    	$collection = $this->db->comments;
	    	$cursor = $collection->find(
				array(
				    'fav_users' => array(
				    	'$exists' => "true",
				    	'$ne' => "[]"
				    )
				)
			);

		    $result = array();
		    foreach ($cursor as $document) {
		        $result[] = $document;
		    }

		    return (count($result) > 0) ? $result : NULL;
		}


		public function getAuthorFavoriteComments($author){
		    $collection_comments = $this->db->comments;
	    	$collection_users = $this->db->users;

	    	$cursor = $collection_users->find(
		        array(
		            '_id' => $author
		        )
		    );;

		    $result = array();
		    foreach ($cursor as $document) {
		        $result = $document['fav_comments'];
		    }

		    foreach ($result as $num) {
		    	$array_ids[] = intval($num);
		    }

		    if(count($array_ids)){
		    	 $cursor2 = $collection_comments->find(
			        array(
			            '_id' => array(
			            	'$in' => $array_ids
			            )
			        )
			    );
			    $result2 = array();
			    foreach ($cursor2 as $document) {
			        $result2[] = $document;
			    }
		    }

		    return (count($result2) > 0) ? $result2 : NULL;
		}

	}
}
?>