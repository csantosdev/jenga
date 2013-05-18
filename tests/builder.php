<?php
require_once '../jenga.php';
require_once '../models.php';

use Jenga\DB\Query\QuerySet;

$post = Post::objects()->filter(array('id' => 1));
count($post);