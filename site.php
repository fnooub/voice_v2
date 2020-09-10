<?php

include 'db.php';
include 'functions.php';

/**
 * submit
 */
if (isset($_POST['link'])) {
	$link = post_var('link');
	// metruyenchu
	if (preg_match('@metruyenchu@si', $link)) {
		$flag = 'mtc';
	}
	// truyencv
	elseif (preg_match('@truyencv@si', $link)) {
		$flag = 'tcv';
	}
	// truyenfull
	elseif (preg_match('@truyenfull@si', $link)) {
		$flag = 'tf';
	}
	// ttv
	elseif (preg_match('@tangthuvien@si', $link)) {
		$flag = 'ttv';
	}

	$stmt = $db->prepare('SELECT id FROM site WHERE url = :url');
	$stmt->execute(array(':url' => $link ));

	if (!empty($link) && $stmt->rowCount() == 0) {
		$query = "INSERT INTO site (url, flag, start, end, nl2p, loc, date_update) VALUES (:url, :flag, :start, :end, :nl2p, :loc, :date_update)";
		$stmt = $db->prepare($query) ;
		$stmt->execute(array(
			':url' => $link,
			':flag' => $flag,
			':start' => '1',
			':end' => '10',
			':nl2p' => 'yes',
			':loc' => 'yes',
			':date_update' => time()
		));

		/**
		 * save localhost
		 */
		$id = $db->lastInsertId();
		if (post_var('save')) {
			$base_url = single_curl(base_url('set.php?link=' . $link . '&flag=' . $flag));
			file_put_contents("$id.json", $base_url);
		}
		header('Location: ' . base_url());
		exit;
	}
}

/**
 * lay du lieu
 */
$query = "SELECT * FROM site ORDER BY date_update DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll();

?>
<title>TCV REGEX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
	a { text-decoration: none; }
</style>
<?php include "navbar.php"; ?>
<form action="" method="post">
	<input type="text" name="link">
	<input type="submit" value="New site">
	<input type="checkbox" name="save"> save
</form>

<?php if (!empty($result)): ?>
	<div style="white-space: nowrap;overflow: auto;">
		<?php foreach ($result as $row): ?>
			<?php
			$name = parse_url($row['url'])['path'];
			$name = str_replace(array('/truyen/', '/'), '', $name);
			?>
			<pre>[<?= $row['id'] ?>] <a href="config_site.php?site_id=<?= $row['id'] ?>"><?= $name ?></a> <a href="regex.php?site_id=<?= $row['id'] ?>" style="background-color: yellow">Regex</a> <a href="<?= $row['url'] ?>"><span style="background-color: #c9f795"><?= $row['flag'] ?></span></a> <a href="<?= base_url('save.php?site_id=' . $row['id']) ?>"><span style="background-color: #ffc107">save</span></a></pre>
		<?php endforeach ?>
	</div>
<?php endif ?>
