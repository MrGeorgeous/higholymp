<?php

$args = array(
			'pids' => FALSE,
			'olymps' => FALSE
		);

foreach ($args as $a => $v) {

	if (isset($_POST[$a])) {
		if (($_POST[$a] !== 'null')) {
			$args[$a] = /*SQLite3::escapeString(*/$_POST[$a]/*)*/;
		}
	}
	
}


require_once('db.php');

$ps = new ProgrammeSearcher('source.db');
//var_dump(json_decode($args['pids'], true));
//var_dump(json_decode($args['olymps'], true));
$pr = $ps->getPrivilegesByAchievements(json_decode($args['pids'], true), json_decode($args['olymps'], true));
header('Content-type: application/json; charset=utf-8');
die(json_encode($pr));


?>