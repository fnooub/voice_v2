<?php

include 'functions.php';

$link = get_var('link');
$flag = get_var('flag');
$s = get_var('s');
$e = get_var('e');
$site_id = get_var('site_id');
$c = isset($_GET['c']) ? '&c=' . $_GET['c'] : null;

$str = single_curl(base_url('set.php?flag=' . $flag . '&link=' . $link . $c));

$data = json_decode($str, true);

for ($i = ($s-1); $i < $e; $i++) {
	$urls[] = base_url('get.php?site_id=' . $site_id . '&flag=' . $flag . '&link=') . $data[$i];
}

$noidung = multi_curl($urls);

$noidung = str_replace('⊙⊙', '…<br>…<br>…<br><br>', $noidung);

echo $noidung;
echo "s=$s&e=$e";