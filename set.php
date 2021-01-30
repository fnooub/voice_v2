<?php

include 'functions.php';

/**
 * get site
 */
$link = (string) get_var('link');
$flag = (string) get_var('flag');

/**
 * get html
 */
$str = single_curl(trim($link));

$data = [];
/**
 * metruyenchu.com
 */
if ($flag == 'mtc') {
	$soChuong = get_rows('<div class="font-weight-semibold h4 mb-1">', '</div>', $str);

	for ($i = 1; $i <= trim($soChuong[0]); $i++) {
		$data[] = $link . '/chuong-' . $i;
	}	
}
/**
 * vtruyen.com
 */
elseif ($flag == 'vt') {
	$soChuong = get_rows('<div class="font-weight-semibold h4 mb-1">', '</div>', $str);

	for ($i = 1; $i <= trim($soChuong[0]); $i++) {
		$data[] = $link . '/chuong-' . $i;
	}	
}
/**
 * tang thu vien
 */
elseif ($flag == 'ttv') {
	// get id
	preg_match('#value="(\d+)"#is', $str, $id);

	// get danh sach chuong
	$list = single_curl('https://truyen.tangthuvien.vn/story/chapters?story_id=' . $id[1]);

	$data = get_links($list);			
}
// truyenfull
elseif ($flag == 'tf') {

	//tong
	if(!preg_match('#<ul class="pagination pagination-sm">#is', $str)){
		$last = 1;
	}else{
		if(preg_match('#Trang ([0-9]{1,3})">Cuối#is', $str)){
			preg_match('#Trang ([0-9]{1,3})">Cuối#is', $str, $tong);
			$last = $tong[1];
		}else{
			preg_match('#(.*)>([0-9]{1,3})</a></li><li>#is', $str, $tong);
			$last = $tong[2];
		}
	}

	// get multi pages
	$urls = array();
	for ($i=1; $i <= $last; $i++) { 
		$urls[] = $link . 'trang-' . $i . '/';
	}

	$multi_curl = multi_curl($urls);

	$list_chapter = get_rows('<ul class="list-chapter">', '</ul>', $multi_curl);
	$data = get_links(print_r($list_chapter, true));

}

// tcv
elseif ($flag == 'tcv') {
	$soChuong = isset($_GET['c']) ? $_GET['c'] : 3000;
	for ($i = 1; $i <= $soChuong; $i++) {
		$data[] = $link . 'chuong-' . $i . '/';
	}
}

// tct
elseif ($flag == 'tct') {
	$list = get_row('<div class="txt chuongs">', '</div>', $str);

	$links = get_links($list);

	$data = array();
	foreach ($links as $lnk) {
		$data[] = 'https://m.truyencuatui.net' . $lnk;
	}
}
// bns
elseif ($flag == 'bns') {
	$links = get_rows('<div class="mucluc-chuong"><a href="', '">', $str);

	$data = array();
	foreach ($links as $lnk) {
		$data[] = 'https://bachngocsach.com' . $lnk;
	}
}

// kyhuyen
elseif ($flag == 'kh') {
	preg_match_all('@<ul>(.+?)</ul>@si', $str, $out);
	unset($out[1][0]) ;
	$links = get_links(print_r($out[1], true));
	$data = $links;
}

// truyenyy
elseif ($flag == 'tyy') {
	preg_match('@<li>(\d+) <small>Chương</small></li>@si', $str, $soChuong);

	// lay link
	for ($i = 1; $i <= $soChuong[1]; $i++) {
		$data[] = $link . 'chuong-' . $i . '.html';
	}
}

// chivi.xyz
elseif ($flag == 'chivi') {
	preg_match('/<a href=".+?page=(\d+)".+?<use xlink:href="\/icons\.svg#chevrons-right"><\/use>/', $str, $soTrang);
	// get multi pages
	$urls = array();
	for ($i=1; $i <= $soTrang[1]; $i++) { 
		$urls[] = $link . '?page=' . $i;
	}

	$multi_curl = multi_curl($urls);

	$list_chapter = get_rows('<div class="chlist _page', '<nav class="pagi', $multi_curl);
	$links = get_links(print_r($list_chapter, true));
	$data = array();
	foreach ($links as $lnk) {
		$data[] = 'https://chivi.xyz' . $lnk;
	}
}

echo json_encode($data);