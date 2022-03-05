<?php

$a = 'ids';
$ids = '';

	if (isset($_POST[$a])) {
		if (($_POST[$a] != 'null')) {
			$ids = SQLite3::escapeString($_POST[$a]);
		}
	}


//die();
/*die(json_encode($_POST));*/

//die(json_encode($args));

require_once('db.php');
//die(json_encode($args));

$ps = new ProgrammeSearcher('source.db');
$pr = $ps->getProgrammes(json_decode($ids, true));
header('Content-type: application/json; charset=utf-8');
die(json_encode($pr));







?>