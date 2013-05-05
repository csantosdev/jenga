<?php
require_once '../jenga.php';
require_once '../models.php';

$db = Jenga::get_db();
$db->add_table(array('model'=>'Site'));