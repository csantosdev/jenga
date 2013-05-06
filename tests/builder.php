<?php
require_once '../jenga.php';
require_once '../models.php';

use Jenga\DB\Query\QueryBuilder;

$builder = new QueryBuilder();

$models = array('comment','post');
$inner_joins = array(
	array('id' => 'post_id')
);
$wheres = array();

// Comment::objects->filter(array('post__id' => 1));
$builder->create_select_statement($models, $inner_joins, $wheres);
$qs = Comment::objects()->filter(array('post__id' => 1));
$qs->query;
