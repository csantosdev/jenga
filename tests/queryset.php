<?php
require_once '../jenga.php';
require_once '../models.php';

use Jenga\DB\Query\QuerySet;

$posts = Post::objects()->filter(array('title' => 'My First Post!'));
count($posts);