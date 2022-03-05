<?php

class Verificator {

	protected $db;

	function __construct($db_name) {
		$this->db = new SQLite3($db_name);
	}

	public function privilegesToSubjects() {

		$this->db->query('PRAGMA encoding = "UTF-8";');
		$r = $this->db->query("SELECT `privileges`.* FROM `privileges` WHERE `privileges`.`ege_subject` NOT IN (SELECT `ege_programmes`.`subject` FROM `ege_programmes` WHERE `ege_programmes`.`programme` = `privileges`.`programme` )  AND `is_bvi`='0' GROUP BY `privileges`.`id`;");

		$arr = array();
		while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
		    $arr[] = $row;
		}

		return (count($arr) == 0);
	}
}


$v = new Verificator('source.db');
echo $v->privilegesToSubjects();

?>