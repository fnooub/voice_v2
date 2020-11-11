<?php

include "db.php";
include "functions.php";

// ghi du lieu
if (isset($_POST['submit'])) {
	extract($_POST);
	if (!empty($text)) {

		$data = json_decode($text, true);

		foreach ($data as $value) {
			$query = "INSERT INTO regex (s, r, flag, site_id) values (:s, :r, :flag, :site_id)";
			$stmt = $db->prepare($query);
			$stmt->execute(array(
				':s' => $value['s'],
				':r' => $value['r'],
				':flag' => $value['flag'],
				':site_id' => $site_id
			));
		}

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
$query = "SELECT s, r, flag FROM regex WHERE site_id = :site_id ORDER BY id ASC";
$stmt = $db->prepare($query);
$stmt->execute(array(':site_id' => $site_id));
$regexs = $stmt->fetchAll();



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
	<textarea name="text" style="width: 100%; height: 200px" placeholder="Text"><?= json_encode($regexs) ?></textarea>
	<input type="submit" name="submit" value="Replace">
</form>


</body>
</html>
