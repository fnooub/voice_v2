<?php

include 'db.php';
include 'functions.php';

/**
 * get id
 */
$site_id = (int) get_var('site_id');

$query = "SELECT * FROM site WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute(array(':id' => $site_id));
$site = $stmt->fetch();

if ( ! $site ) exit('err');

$soChuong = isset($_GET['c']) ? '&c=' . $_GET['c'] : null;
$base_url = single_curl(base_url('set.php?link=' . $site['url'] . '&flag=' . $site['flag'] . $soChuong));
file_put_contents("$site_id.json", $base_url);

echo 'string';