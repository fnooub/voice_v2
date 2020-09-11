<?php 
include ('dumper.php');
$world_dumper = Shuttle_Dumper::create(array(
	'host' => 'sh4ob67ph9l80v61.cbetxkdyhwsb.us-east-1.rds.amazonaws.com',
	'username' => 'lb47d1nbnlh667dg',
	'password' => 'ryzu6c4we8q4r7du',
	'db_name' => 'tgb47slod4ufwbhf',
));

$voice = time() . '.sql';
// dump the database to plain text file
$world_dumper->dump($voice);

?>
<a href="<?= $voice ?>"><?= $voice ?></a>