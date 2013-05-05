<?php
require_once '../jenga.php';
require_once '../models.php';

$site = new Site();
echo 'Your newly created Site object:<br/>';
$site->name = 'iCandy Clothing';
$site->description = 'Urban Apparel Shop';
echo "<br/><br/>" . var_dump($site);
$site->save();