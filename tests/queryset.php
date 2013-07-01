<?php
require_once '../jenga.php';
require_once '../models.php';

use Jenga\DB\Query\QuerySet;

//$posts = Post::objects()->filter(array('blog.id' => 1));
//$posts = Post::objects()->filter(array('blog__id !=' => 0));
//$posts = Post::objects()->filter(array('blog__id >=' => 1));
//$posts = Post::objects()->filter(array('blog__id_gte' => 1));
//count($posts);

$mongo_posts = MongoPost::objects()->filter(
	['blog.name' => 'iCandy Clothing', 'blog.site.active' => true]
);
echo "Mongo Posts:" . count($mongo_posts);
echo "<br/>Mongo Post var_dump";
var_dump($mongo_posts);