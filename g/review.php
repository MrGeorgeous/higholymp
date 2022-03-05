<?php


require_once('db.php');

$ps = new ProgrammeSearcher('source.db');


if (isset($_POST['id'])) { 
} else {
	die('[]');
}

if ($_POST['id'] == NULL) {
	die('[]');
}

$id = SQLite3::escapeString($_POST['id']);
$pr = $ps->getProgrammes( [ $id ] )[0];
$pp = $ps->getPrivilegesForProgramme( $id );

header('Content-type: application/json; charset=utf-8');
die(json_encode(['programme' => $pr, 'privileges' => $pp]));


?>