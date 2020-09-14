<?php

include "db.php";
include "functions.php";

// ghi du lieu
if (isset($_POST['submit'])) {
	extract($_POST);
	$flag = isset($flag) ? $flag : 'g';
	if (isset($space)) {
		$s = str_split_search($s);
	}
	if (isset($quote)) {
		$s = regex_quote($s, '/');
	}
	if (!empty($s)) {
		$query = "INSERT INTO regex (s, r, flag, site_id) values (:s, :r, :flag, :site_id)";
		$stmt = $db->prepare($query);
		$stmt->execute(array(
			':s' => $s,
			':r' => $r,
			':flag' => $flag,
			':site_id' => $site_id
		));
	}
}

/**
 * get site
 */
$site_id = (int) get_var('site_id');

$query = "SELECT * FROM site WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute(array(':id' => $site_id));
$site = $stmt->fetch();

/**
 * get regex
 */
$query = "SELECT * FROM regex WHERE site_id = :site_id ORDER BY id ASC";
$stmt = $db->prepare($query);
$stmt->execute(array(':site_id' => $site_id));
$regexs = $stmt->fetchAll();


// xoa id
if (isset($_GET['xoa_id'])) { 
	$query = "DELETE FROM regex WHERE id = :id";
	$stmt = $db->prepare($query);
	$stmt->execute(array(':id' => $_GET['xoa_id']));
	header('Location: regex.php?site_id=' . $_GET['site_id']);
	exit;
}

// xóa trang
if(isset($_GET['xoa_site'])){ 
	$query = "DELETE FROM site WHERE id = :id";
	$stmt = $db->prepare($query) ;
	$stmt->execute(array(':id' => $_GET['xoa_site']));

	$query = "DELETE FROM regex WHERE site_id = :site_id";
	$stmt = $db->prepare($query) ;
	$stmt->execute(array(':site_id' => $_GET['xoa_site']));

	header('Location: index.php');
	exit;
}

function str_split_search($str)
{
	$str = preg_replace('/\s+/', '', $str);
	$str = preg_split('/(?<!^)(?!$)/u', $str );
	return implode("\s*", $str);
}

function regex_quote($str)
{
	$subject = preg_quote($str, '/');
	$search = array('\(\.\+\?\)', '\(\.\*\?\)', '\.\*\?', '\.\+\?');
	$replace = array('(.+?)', '(.*?)', '.*?', '.+?');
	return str_replace($search, $replace, $subject);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tieu de</title>
</head>
<body>
<?php include "navbar.php"; ?>
<form action="" method="post">
	<input type="hidden" name="site_id" value="<?= $site['id'] ?>">
	<textarea name="s" style="width: 100%" placeholder="search"></textarea>
	<textarea name="r" style="width: 100%" placeholder="replace"></textarea>
	<input type="radio" name="flag" value="u"> <b>/u</b>
	<input type="radio" name="flag" value="i"> <b>/i</b>
	<input type="radio" name="flag" value="is"> <b>#is</b>
	<input type="radio" name="flag" value="iu"> <b>/iu</b>
	<input type="radio" name="flag" value="td"> <b>/tđ</b>
	<input type="checkbox" name="space"> <b>/sp</b>
	<input type="checkbox" name="quote"> <b>/quote</b>
	<br>
	<input type="submit" name="submit" value="Replace">
</form>
<p><a href="?xoa_site=<?= $site['id'] ?>" onclick = "if (! confirm('Xoá site?')) { return false; }">Xoá site</a> | <a href="config_site.php?site_id=<?= $site['id'] ?>">Config site</a></p>
<hr>
<div style="white-space: nowrap;overflow: auto;">
	<?php foreach ($regexs as $regex): ?>
		<?php
			$s = str_replace(' ', '▂', $regex['s']);
			$r = str_replace(' ', '▂', $regex['r']);
			$id = $regex['id'];

			if ($regex['flag'] == 'u') {
				$flag = '/u';
			} elseif ($regex['flag'] == 'i') {
				$flag = '/i';
			} elseif ($regex['flag'] == 'is') {
				$flag = '#is';
			} elseif ($regex['flag'] == 'iu') {
				$flag = '/iu';
			} elseif ($regex['flag'] == 'td') {
				$flag = '/td';
			} else {
				$flag = '/g';
			}
		?>
		<pre>[<b><?= sprintf("%02d", $id) ?></b>] <span style="background-color: yellow"><?= htmlspecialchars($s) ?> <font color="red"><?= $flag ?></font> <?= (($r != null) ? htmlspecialchars($r) : '<font color="gray"><i>null</i></font>') ?></span> [<a href="?site_id=<?= $site_id ?>&xoa_id=<?= $id ?>" onclick = "if (! confirm('Xoá?')) { return false; }">xóa</a> | <a href="edit.php?id=<?= $id ?>">sửa</a>]</pre>
	<?php endforeach ?>
</div>

</body>
</html>
