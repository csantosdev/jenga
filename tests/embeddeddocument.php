<?php
require_once '../jenga.php';
require_once 'models.php';

/*
 * Creating a Pull Instruction object. This adds a document to the 'pullinstruction' collection.
 * But what will happen if we have this saved in a EmbeddedDocument?
 */
$pull = new PullInstruction();
$pull->required = false;
$pull-> multiple = false;
$pull->fields = [1,2];
//$pull->save(); To not save this into it's own collection, simply do not save it.

$account = new Account();
$account->name = 'iCandy Clothing';
$account->email = 'info@icandyclothing.com';
$account->save();

$merchant = new Merchant();
$merchant->name = 'DrJays';
$merchant->save();

$feed = new Datafeed();
$feed->account = $account;
$feed->merchant = $merchant;
$feed->feed_instructions = [$pull]; // We add our new pull FeedInstruction object
$feed->save(); // This should save the $pull object into the $feed doc in Mongo.

/*
 * Edit the embedded object and re-save the $feed object!
 */
$pull->fields = [3,4];
$feed->save();

/*
 * Pulling the object from the database.
 */
$feed_id = $feed->id;
$feed = null;
$feed = Datafeed::objects()->get(['_id' => $feed_id]);
echo 'Account Name: ' . $feed->account->name;
echo "<br>Email: " . $feed->account->email;
echo "<br>Feed Instruction field[0]: " . $feed->feed_instructions[0]->fields[0];
var_dump($feed->feed_instructions);
