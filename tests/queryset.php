<?php
require_once '../jenga.php';
require_once '../models.php';

use jenga\db\query\QuerySet;

$posts = Post::objects()->filter(array('blog.id' => 1));
//$posts = Post::objects()->filter(array('blog__id !=' => 0));
//$posts = Post::objects()->filter(array('blog__id >=' => 1));
//$posts = Post::objects()->filter(array('blog__id_gte' => 1));
count($posts);

$mongo_posts = MongoPost::objects()->filter(array('blog.name' => 'iCandy Clothing'));
count($mongo_posts);