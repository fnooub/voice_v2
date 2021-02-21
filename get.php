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
	$noidung = get_row('id="js-read__content".+?>', '<i class="h4 nh-icon icon-star"></i>', $str, false);
	$noidung = preg_replace('@<div.*?>.+?</div>@si', '', $noidung);
	if ($site['nl2p'] == 'yes') {
		$noidung = preg_replace('/>[^>]*<a href=\"[^\"]*\" target=\"_blank\">.+?</', '><', $noidung);
	}
}
// vtruyen
elseif ($flag == 'vt') {
	$tieude = get_row('<div class="h1 mb-4 font-weight-normal nh-read__title">', '</div>', $str);
	$noidung = get_row('id="js-read__content".+?>', '<i class="h4 nh-icon icon-flower"></i>', $str, false);
	$noidung = preg_replace('@<div.*?>.+?</div>@si', '', $noidung);
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
	$noidung = preg_replace('/\n/', ' ', $noidung);
}
// TRUYENCV
elseif ($flag == 'tcv') {
	$tieude = get_row('<h2 class="title">', '</h2>', $str);
	$noidung = get_row('<div class="content" id="js-truyencv-content".+?>', '<div class="fb-like"', $str);
}
// truyencuatui
elseif ($flag == 'tct') {
	$tieude = get_row('<h1>', '</h1>', $str);
	$noidung = get_row('<div class="txt">', '</div><a href', $str);
}
// bns
elseif ($flag == 'bns') {
	$tieude = get_row('<h1.+?>', '</h1>', $str);
	$noidung = get_row('<div id="noi-dung">', '</div>', $str);
}
// kh
elseif ($flag == 'kh') {
	$tieude = get_row('<h1.+?>', '</h1>', $str);
	$noidung = get_row('<div class="story-content.+?">', '<div class="w-100 center-block center-text">', $str);
}

// tyy
elseif ($flag == 'tyy') {
	$tieude = get_row('<h1.+?>\s*', '\s*</h1>', $str);
	$noidung = get_row('<div class="inner"><div>', '</div></div>', $str);
}

// chivi
elseif ($flag == 'chivi') {
	$tieude = get_row('<h1.+?>\s*', '\s*</h1>', $str);
	$noidung = get_row('</h1>', '</article>', $str);
$noidung = preg_replace('/<section>.+?<\/section>/', '', $noidung);
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

// remove ads
$noidung = preg_replace(array('/Bạn đang đọc truyện được copy tại/iu', '/Bạn đang đọc truyện được lấy tại/iu', '/Bạn đang đọc truyện tại/iu', '/Text được lấy tại/iu', '/nguồn truyện\s*:/iu', '/nguồn\s*:/iu', '/chấm cơm\.?/iu', '/www\s*\./i', '/https?\s*:\s*\/?\/?/i', '/\.\s*vn/i', '/\.\s*com/iu', '/Truyen\s*FULL/iu', '/truyện\s*full/iu', '/Đọc Truyện Online Tại/iu', '/Đọc Truyện Kiếm Hiệp Hay Nhất\:?/iu', '/Truyện.{1,10}Hiệp/iu', '/truyenyy/iu', '/Truyện\s*YY/iu', '/Bạn đang đọc chuyện tại/iu', '/Bạn đang xem truyện được sao chép tại:/iu', '/chấm c\.o\.m/iu'), '', $noidung);

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
