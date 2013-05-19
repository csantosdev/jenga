<?php
require_once '../jenga.php';
require_once '../models.php';

use Jenga\DB\Query\QuerySet;

//$post = Post::objects()->filter(array('id' => 1));
$posts_with_comments = Post::objects()->filter(array('blog__id' => 1));
count($posts_with_comments);