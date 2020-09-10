<?php

$db = new PDO("mysql:host=sh4ob67ph9l80v61.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;dbname=tgb47slod4ufwbhf;charset=utf8", "lb47d1nbnlh667dg", "ryzu6c4we8q4r7du");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);