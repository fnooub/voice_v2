<?php

include 'db.php';
include 'functions.php';

/**
 * get site
 */
$link = get_var('link');
$flag = get_var('flag');

/**
 * get id
 */
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

/**
 * tieu de
 */
$tieude = '';
$noidung = '';
// metruyenchu
if ($flag == 'mtc') {
	$tieude = get_row('<div class="h1 mb-4 font-weight-normal nh-read__title">', '</div>', $str);
	$noidung = get_row('<div class="nh-read__content.+?>', '</div>\s*<div', $str, false);
}
// tang thu vien
elseif ($flag == 'ttv') {
	$tieude = get_row('<h2>', '</h2>', $str);
	$noidung = get_row('<div class="box-chap box-chap-\d+">', '</div>', $str, false);
}
// truyenfull
elseif ($flag == 'tf') {
	$tieude = get_row('<h2><a.+?>', '</a></h2>', $str);
	$tieude = strip_tags($tieude);
	$noidung = get_row('<div id="chapter-c" class="chapter-c"><div class="visible-md visible-lg ads-responsive incontent-ad" id="ads-chapter-pc-top" align="center" style="height:90px"></div>', '</div>', $str);
}
// TRUYENCV
elseif ($flag == 'tcv') {
	$tieude = get_row('<h2 class="title">', '</h2>', $str);
	$noidung = get_row('<div class="content" id="js-truyencv-content".+?>', '<div class="fb-like"', $str);
}

/**
 * output
 */
$tieude = get_title($tieude);

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
	} elseif ($regex['flag'] == 'td') {
		$tieude = preg_replace('/' . $regex['s'] . '/iu', $regex['r'], $tieude);
	}
}


/**
 * show
 */
echo "$tieude<br>➥<br>➥<br><br>$noidung<br>⊙⊙";

function get_title($str)
{
	$str = preg_replace('@chương 0+(\d+)@siu', 'Chương $1', $str);
	$str = preg_replace('/\s*\(.+/', '', $str);

	return trim( $str );
}
