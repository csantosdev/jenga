<?php
require_once '../jenga.php';
require_once '../models.php';

$site = new Site();
echo 'Your newly created Site object:<br/>';
var_dump($site);
$site->name = 'iCandy Clothing';
$site->description = 'Urban Apparel Shop';
echo "<br/><br/>" . var_dump($site);
$site->save();

$instruction = new MongoFeedInstruction();
$instruction->type = 1;
$instruction->required = false;
$instruction->multiple = false;
$instruction->fields = [1,2];
$instruction->save();