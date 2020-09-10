<?php

include 'db.php';
include 'functions.php';

/**
 * submit
 */
if (isset($_POST['submit'])) {
	extract($_POST);
	$loc = isset($loc) ? 'yes' : 'no';
	$nl2p = isset($nl2p) ? 'yes' : 'no';

	if (empty($start) || empty($end)) {
		$error = 'Trống!';
	}
	if ($start >= $end) {
		$error = 'Start phải bé hơn End';
	}

	if (!isset($error)) {
		$query = "UPDATE site SET start = :start, end = :end, nl2p = :nl2p, loc = :loc, date_update = :date_update WHERE id = :id";
		$stmt = $db->prepare($query);
		$stmt->execute(array(
			':id' => $id,
			':start' => $start,
			':end' => $end,
			':nl2p' => $nl2p,
			':loc' => $loc,
			':date_update' => time()
		));

		/**
		 * get du lieu
		 */

		$str = single_curl(base_url('set.php?flag=' . $flag . '&link=' . $url));

		$data = json_decode($str, true);

		for ($i = ($start-1); $i < $end; $i++) {
			$urls[] = $data[$i];
		}

		$conts = multi_curl($urls);
		if ($flag == 'mtc') {
			$tieude = get_rows('<div class="h1 mb-4 font-weight-normal nh-read__title">\s*', '\s*</div>', $conts);
		} else {
			$tieude = get_rows('<title>', '</title>', $conts);
		}
		
		$datas = array_combine($tieude, $urls);
	}
}

/**
 * get id
 */
$query = "SELECT * FROM site ORDER BY date_update DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$site = $stmt->fetch();

if ( ! $site ) exit('err');

?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>sieu-than-sung-thu-diem</title>
<style>
	a { text-decoration: none; }
	input[name=url],
	input[type=submit],
	input[type=number],
	textarea {
		display: block;
		margin: 10px 0;
	}
	input[name=url],
	input[type=submit],
	textarea {
		width: 100%;
	}
	input[type=number] {
		display: block;
		margin: 10px 0;
	}
	input[type=radio] {
		display: inline-block;
		padding-right: 10px;
	}
</style>
<?php if (isset($error)): ?>
	<p><?= $error ?></p>
<?php endif ?>
<!-- result -->
<p>
	<a style="background-color: yellow; padding: 10px" href="<?= base_url('gets.php?site_id=' . $site['id'] . '&flag=' . $site['flag'] . '&link=' . $site['url'] . '&s=' . $site['start'] . '&e=' . $site['end']) ?>">
		GET s=<?= $site['start'] ?>&e=<?= $site['end'] ?>
	</a>
</p>
<?php if (isset($datas)): ?>
	<div style="white-space: nowrap;overflow: auto;">
		<?php $count = 1 ?>
		<?php foreach ($datas as $tieude => $url): ?>
			<pre>[<?= $count ?>] =&gt; <a href="<?= base_url('get.php?site_id=' . $site['id'] . '&flag=' . $site['flag'] . '&link=' . $url) ?>"><?= $tieude ?></a></pre>
			<?php $count++ ?>
		<?php endforeach ?>
	</div>
<?php endif ?>
<code><?= $site['url'] ?></code>
<hr>
<form method="post">
	<input type="hidden" name="id" value="<?= $site['id'] ?>">
	<input type="hidden" name="url" value="<?= $site['url'] ?>">
	<input type="hidden" name="flag" value="<?= $site['flag'] ?>">
	<input type="number" name="start" onfocus="this.value=''" value="<?= $site['start'] ?>">
	<input type="number" name="end" onfocus="this.value=''" value="<?= $site['end'] ?>">
	<input type="checkbox" name="loc"<?php if ($site['loc'] == 'yes') echo 'checked' ?>> lọc
	<input type="checkbox" name="nl2p"<?php if ($site['nl2p'] == 'yes') echo 'checked' ?>> nl2p
	<input type="submit" name="submit" value="Config">
</form>
<p>
	<a href="regex.php?site_id=<?= $site['id'] ?>">Regex</a>
</p>
<?php include "navbar.php"; ?>