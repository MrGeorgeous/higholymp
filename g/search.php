<?php

//die(json_encode($_POST));

$args = array(
			'name' => FALSE,
			'unis' => FALSE,
			'is_paid' => FALSE,
			'years_s' => FALSE,
			'years_f' => FALSE,
			'codes' => FALSE,
			'worldwide' => FALSE,
			'proff' => FALSE
		);

foreach ($args as $a => $v) {

	if (isset($_POST[$a])) {
		if (($_POST[$a] != 'null')) {
			$args[$a] = SQLite3::escapeString($_POST[$a]);
			if ($a == 'unis') { $args[$a] = json_decode($args[$a], false); }
			if ($a == 'codes') { $args[$a] = json_decode($args[$a], false); }
		}
	}
}

//die();
/*die(json_encode($_POST));*/

//die(json_encode($args));

require_once('db.php');
//die(json_encode($args));

$ps = new ProgrammeSearcher('source.db');
$pr = call_user_func_array(array($ps, 'searchProgrammes'), $args);
header('Content-type: application/json; charset=utf-8');
die(json_encode($pr));







?>