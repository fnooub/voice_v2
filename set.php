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

	for ($i = 1; $i <= $soChuong[1]; $i++) {
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

echo json_encode($data);