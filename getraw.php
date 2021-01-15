<?php

// http://localhost/voice_v2/getraw.php?site_id=498&link=http://res.cloudinary.com/fivegins/raw/upload/v1610725698/luufiles/huatienchi_xf5f7v.txt

include 'db.php';
include 'functions.php';

/**
 * get site
 */
$link = get_var('link');

$site_id = (int) get_var('site_id');

$query = "SELECT * FROM site WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute(array(':id' => $site_id));
$site = $stmt->fetch();

if ( ! $site ) exit('err');

/**
 * get html
 */
$str = single_curl(trim($link));

$noidung = $str;

// nl2p
if ($site['nl2p'] == 'yes') {
	$noidung = strip_all_tags($noidung, true);
	$noidung = nl2p($noidung);
} else {
	$noidung = strip_all_tags($noidung);
}

// loc
if ($site['loc'] == 'yes') {
	$noidung = loc($noidung, true);
}

/**
 * regex
 */
$query = "SELECT s, r, flag FROM regex WHERE site_id = :site_id";
$stmt = $db->prepare($query);
$stmt->execute(array(':site_id' => $site['id']));
$regexs = $stmt->fetchAll();

foreach ($regexs as $regex) {
	if ($regex['flag'] == 'g') {
		$noidung = preg_replace('/' . $regex['s'] . '/', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'u') {
		$noidung = preg_replace('/' . $regex['s'] . '/u', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'i') {
		$noidung = preg_replace('/' . $regex['s'] . '/i', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'is') {
		$noidung = preg_replace('#' . $regex['s'] . '#is', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'iu') {
		$noidung = preg_replace('/' . $regex['s'] . '/iu', $regex['r'], $noidung);
	}
}


/**
 * show
 */
echo $noidung;
