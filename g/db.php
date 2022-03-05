<?php

mb_internal_encoding("UTF-8");
header('Content-Type: text/html; charset=utf-8');

function fetchArray($sqliteresult) {
	$result = array();
	while ($row = $sqliteresult->fetchArray(SQLITE3_ASSOC)) {
		$result[] = $row;
	}
	return $result;
}

class ProgrammeSearcher {

	// Docs are available on GitHub:
	// https://github.com/higholymp/web/blob/master/searcher.md

	protected $db;
	protected $year = 2018;

	function __construct($db_name) {
		$this->db = new SQLite3($db_name);
		$this->db->query('PRAGMA encoding = "UTF-8";');
	}

	public function searchProgrammes($name, $unis, $is_paid, $year_f, $year_t, $codes, $worldwide, $proff) {

		$clause = array();
		$result = array();

		if ($name !== FALSE) { $clause[] = "`programmes`.`name` LIKE '%$name%'"; }
		if ($is_paid !== FALSE) { $clause[] = "`is_paid`='$is_paid'"; }
		if ($worldwide !== FALSE) { $clause[] = "`worldwide`='$worldwide'"; }
		if ($proff !== FALSE) { $clause[] = "`proff`='$proff'"; }
		if ($unis !== FALSE) {$clause[] = "`school` IN (SELECT `id` FROM `schools` WHERE `id` IN (" . implode($unis, ', ') . "))"; }
		if (($year_f !== FALSE) && ($year_t !== FALSE)) { $clause[] = "`years` <= $year_t AND `years` >= $year_f";}
		if ($codes !== FALSE) { $clause[] = $this->buildProgrammeCodesCondition($codes); }

		//die(count($clause) . ' ' . $is_paid);

		if (count($clause) == 0) { $clause = '1';} else { $clause = implode($clause, ' AND '); }
		$programmes  = fetchArray($this->db->query("SELECT `programmes`.*, `schools`.`name` as `school_name`, `required_points`.`price`, `required_points`.`point` FROM `programmes` LEFT JOIN `schools` ON (`schools`.`id`=`programmes`.`school`) LEFT JOIN `required_points` ON (`required_points`.`programme` = `programmes`.`id`) WHERE `required_points`.`year`='" . $this->year . "' AND " . $clause . ";"));

		foreach($programmes as &$p) {
			$pid = $p['id'];
			$subjects = fetchArray($this->db->query("SELECT `subject`,`point` FROM `ege_programmes` WHERE `programme`='$pid';"));
			$p['subjects'] = $subjects;
		}

		return $programmes;

	}

	// places : [DB row of `Places`])

	public function getPrivilegesForProgramme($programme) {

		$programme = $this->getProgrammes([$programme])[0];

		$p_id = $programme['id'];
		$p_sc = $programme['school'];

		$q = <<<_END
		SELECT `privileges`.*, COUNT(`closed_privileges`.`id`) as `closed` FROM `privileges` 
		LEFT JOIN `closed_privileges` ON (`closed_privileges`.`privilege` = `privileges`.`id`)
		WHERE (`privileges`.`programme` = $p_id OR `privileges`.`school` = $p_sc )AND (`privileges`.`ege_subject` IN 
		( SELECT `ege_programmes`.`subject` FROM `ege_programmes` WHERE `ege_programmes`.`programme` = `privileges`.`programme` ) OR `privileges`.`is_bvi` = 1) AND `year`='$this->year'
		GROUP BY `privileges`.`id`;
_END;
		$privileges = fetchArray($this->db->query($q));

		$subjects = array_column(fetchArray($this->db->query("SELECT `subject` FROM `ege_programmes` WHERE `programme` = $p_id;")), 'subject');

		$result = array();
		$result['9'] = array();
		$result['10'] = array();
		$result['11'] = array();
		/*foreach ($result as &$row) {
			$row['bvi'] = array();
			$row['100'] = array();
			foreach ($subjects as &$subject) {
				$row['100'][$subject] = array();
			}
		} */

		foreach ($privileges as &$p) {

			$p['olympiads'] = $this->getOlympiadsForPrivilege($p);

			$years = array();
			if (is_int($p['class'])) { $years[] = $p['class']; }
			if ($p['class'] == '10-11') { $years[] = '10'; $years[] = '11';}
			if ($p['class'] == '9-11') { $years[] = '9'; $years[] = '10'; $years[] = '11'; }

			foreach ($years as &$y) {
				/*if ($p['is_bvi'] === 1) {
					$result[$y]['bvi'][$p['ege_subject']][] = $p;
				} else {
					$result[$y]['100'][$p['ege_subject']][] = $p;
				} */
				if (count($p['olympiads']) != 0) {
					$result[$y][] = $p;
				}
			}

		}

		return $result;

	}

	public function getOlympiadsForPrivilege($privilege) {

		$p_p = $privilege['subject'];
		$p_l = $privilege['level'];
		$p_i = $privilege['id'];

		if ($p_l != 'all') {
			$lcl = "AND `level` = $p_l";
		} else {
			$lcl = '';
		}
		if ($privilege['closed'] == 0) {
			return fetchArray($this->db->query("SELECT * FROM `olympiads` WHERE `id` IN (SELECT `olympiad` FROM `profiles` WHERE `profile` = '$p_p') $lcl"));
		} else {
			return fetchArray($this->db->query("SELECT * FROM `olympiads` WHERE `id` IN (SELECT `olympiad` FROM `closed_privileges` WHERE `privilege` = '$p_i');"));
		}
		
	}

	public function getPrivilegesByAchievements($programmes, $achieves) {
		if (($programmes !== FALSE)) {
			if (count($programmes) == 0) {
				$ids = 'AND 1';
			} else { $ids = 'AND `programmes`.`id` IN (' . implode($programmes, ', ') . ')'; }
		} else {
			$ids = 'AND 1';
		}

		$privileges = array();
		// Для каждой льготы получить все привилегии для Программ
		// Если closed = 0, то помечать. Если нет, то делать доп. запрос
		foreach ($achieves as $a) {

			//$a = json_decode($a, false);
			$cl = '';
			if ($a['class'] == '11') { $cl = "`class`='9-11' OR `class`='10-11' OR `class`='11'";}
			if ($a['class'] == '10') { $cl = "`class`='9-11' OR `class`='10-11' OR `class`='10'";}
			if ($a['class'] == '9') { $cl = "`class`='9-11' OR `class`='9'";}
			$level = $this->getOlympiad($a['id'])['level'];
			$year = $this->year; //$a['year'];
			$oid = $a['id'];
			$place = 'third'; if ($a['place'] == 2) { $place = 'second'; } if ($a['place'] == 1) { $place = 'first'; }
			$q1 = <<<_END
			SELECT DISTINCT `programmes`.`id`, `programmes`.`code`, `privileges`.`ege_subject`, `privileges`.`is_bvi`, `programmes`.`name`, `programmes`.`worldwide`, `schools`.`name` as `school_name`, COUNT(`closed_privileges`.`id`) as `closed` FROM `programmes` LEFT JOIN `schools` ON (`programmes`.`school` = `schools`.`id`) LEFT JOIN `privileges` ON (`privileges`.`programme` = `programmes`.`id`) LEFT JOIN `closed_privileges` ON (`closed_privileges`.`privilege` = `privileges`.`id`) GROUP BY `privileges`.`id` HAVING (`closed` = 0 OR '$oid' IN (SELECT `olympiad` FROM `closed_privileges` WHERE `closed_privileges`.`privilege`=`privileges`.`id`)) AND (`level`='$level' OR `level`='all') AND `$place`='1' $ids AND ($cl) AND `year`='$year' AND `subject` IN (SELECT `profile` FROM `profiles` WHERE `olympiad`='$oid') ORDER BY `programme` ASC, `closed` DESC, `is_bvi` DESC ;
_END;
			$q2 = <<<_END
			SELECT DISTINCT `programmes`.`id`, `programmes`.`code`, `privileges`.`ege_subject`, `privileges`.`is_bvi`, `programmes`.`name`, `programmes`.`worldwide`, `schools`.`name` as `school_name`, COUNT(`closed_privileges`.`id`) as `closed` FROM `programmes` LEFT JOIN `schools` ON (`programmes`.`school` = `schools`.`id`) LEFT JOIN `privileges` ON (`privileges`.`school` = `programmes`.`school`) LEFT JOIN `closed_privileges` ON (`closed_privileges`.`privilege` = `privileges`.`id`) GROUP BY `privileges`.`id` HAVING (`closed` = 0 OR '$oid' IN (SELECT `olympiad` FROM `closed_privileges` WHERE `closed_privileges`.`privilege`=`privileges`.`id`)) AND (`level`='$level' OR `level`='all') AND `$place`='1' $ids AND ($cl) AND `year`='$year' AND `subject` IN (SELECT `profile` FROM `profiles` WHERE `olympiad`='$oid') ORDER BY `programme` ASC, `closed` DESC, `is_bvi` DESC ;
_END;
			$rrr = array_merge(fetchArray($this->db->query($q1)), fetchArray($this->db->query($q2)));
			$privileges = array_merge($privileges, $rrr);
			/*foreach ($pri as $p) {
				if ($p['closed'] != 0) {
					$pid = $p['id'];
					$check = count(fetchArray($this->db->query("SELECT * FROM `closed_privileges` WHERE `privilege`='$pid' AND `olympiad`='$oid';")));
					if ($check != 0) {
						$privileges[] = $p;
					}
				} else {
					$privileges[] = $p;
				}
			}*/
		}
		
		//var_dump($programmes);
		//var_dump(array_unique(array_column($privileges, 'id')));

		/*
		
		12 May - it used to work*/
		
		$sev = array_diff($programmes, array_unique(array_column($privileges, 'id')));
		if (count($sev) != 0) {
			$t = array_merge($privileges, $this->getProgrammes($sev));
			//return $t;
		} else {
			$t = $privileges;
			//return $t;
		}
		
		
		
		foreach ($t as &$tt) {
		    $programme =  $this->getProgrammes( [ $tt['id'] ] )[0];
		    if ($tt['is_bvi'] !== '') {
		        if ($programme['point'] != 0) {
		            $tt['average_priv'] = round ( ($programme['point'] - 100) / (count($programme['subjects']) - 1) , 1);
		        } else {
		            $tt['average_priv'] = 0;
		        }
		        
		    } else {
		         $tt['average_priv'] = null;
		    }
		    $tt['average'] = round ( ($programme['point']) / (count($programme['subjects'])) , 1);
		    $tt['subjects'] = $programme['subjects'];
		    $tt['point'] = $programme['point'];
		    $tt['subj_count'] = count($programme['subjects']);
		}
		
		return $t;
		
		/*$sev = array_diff(array_unique(array_column($programmes, 'id')), array_unique(array_column($privileges, 'id')));
		$t_ids = array_merge(array_unique(array_column($privileges, 'id')), $sev);
		
		$ts = $ps->getProgrammes( $t_ids );
		for ($ts as &$r) {
		    if 
		}
		
		$n = array();
		foreach ($r as $t) {
		    $n[] = $r;
		    $p = $ps->getProgrammes( $r['id'] )[0]['point'];
		    $n[count($n) - 1]['average'] = $r['is_bvi'] ?
		}
		*/
		
		//var_dump(array_column($privileges, 'id'));

		/*$ids = array_unique(array_column($privileges, 'id'));
		foreach ($ids as $i => $v) {

		}

		return unique_multidim_array($privileges, 'id'); */
	}

	public function getOlympiad($id) {
		return fetchArray($this->db->query("SELECT * FROM `olympiads` WHERE `id`=$id;"))[0];
	}

	public function getProgrammes($ids) {
		if (count($ids) != 0 ) { $clause = ' AND `programmes`.`id` IN (' . implode($ids, ', ') . ')'; } else { $clause = '';}
		$programmes = fetchArray($this->db->query("SELECT `programmes`.*, `schools`.`name` as `school_name`, `required_points`.`price`, `required_points`.`point` FROM `programmes` LEFT JOIN `schools` ON (`schools`.`id`=`programmes`.`school`) LEFT JOIN `required_points` ON (`required_points`.`programme` = `programmes`.`id`) WHERE `required_points`.`year`='" . $this->year . "' " . $clause));
		foreach($programmes as &$p) {
			$pid = $p['id'];
			$subjects = fetchArray($this->db->query("SELECT `ege_programmes`.`subject`, `ege_programmes`.`point`, MIN(`ege_point`) as `olymp` FROM `ege_programmes` LEFT JOIN `privileges` ON (`privileges`.`programme` = '$pid' AND `privileges`.`ege_subject` = `ege_programmes`.`subject`) WHERE `ege_programmes`.`programme`='$pid' GROUP BY `ege_programmes`.`subject`;"));
			$p['subjects'] = $subjects;
			$p['is_bvi'] = '';
			$p['ege_subject'] = '';
		}
		return $programmes;
	}

	// Returns database query condition clause to filter
	// programmes by specified codes
	private function buildProgrammeCodesCondition($codes) {
		$clause = array();
		foreach ($codes as &$code) {
		    if (substr($code, (-1) * strlen('00')) === '00') {
		    	$code = str_replace('00', '__', $code);
		    	$clause[] = '`code` LIKE "' . $code . '"';
		    } else {
		    	$clause[] = '`code` = "' . $code . '"';
		    }
		}
		return '(' . implode($clause, ' OR ') . ')';
	}

}


function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
} 

/*echo "<pre>";
$ps = new ProgrammeSearcher('source.db');

$achievements = array();
$achievements[] = array('olympiad' => $ps->getOlympiad(114), 'year' => '2018', 'place' => '2', 'class' => '11');
$programmes = $ps->searchProgrammes(FALSE, FALSE , FALSE, FALSE);
var_dump($ps->getPrivilegesByAchievements($programmes, $achievements));
//die();

//var_dump($ps->searchProgrammes([0,1], 0, [3,6], ['01.00.00', '38.00.00']));

$p = $ps->searchProgrammes([0,3], 0, [3,6], ['01.00.00', '38.03.05']);
$pr = $ps->getPrivilegesForProgramme($p[0], '2018');
//$op = $ps->getOlympiadsForPrivilege($pr['11']['bvi'][0]);
var_dump($pr);

for ($j=5; $j <= 54; $j++) {
	echo '<option value="' . $j . '.00.00">' . $j . ' – </option>' . "\n";
} */

?>