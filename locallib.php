<?php
define('PRAXE_TIME_TO_EDIT_SCHEDULE', 60*60*6); // hours before curent time when the availability of student to edit his schedule expires
define('PRAXE_SCHOOL_TYPE_1',1);
define('PRAXE_SCHOOL_TYPE_1_TEXT',get_string('typeschool1','praxe'));
define('PRAXE_SCHOOL_TYPE_2',2);
define('PRAXE_SCHOOL_TYPE_2_TEXT',get_string('typeschool2','praxe'));
define('PRAXE_SCHOOL_TYPE_3',3);
define('PRAXE_SCHOOL_TYPE_3_TEXT',get_string('typeschool3','praxe'));
define('PRAXE_SCHOOL_TYPE_4',4);
define('PRAXE_SCHOOL_TYPE_4_TEXT',get_string('typeschool4','praxe'));
define('PRAXE_SCHOOL_TYPE_5',5);
define('PRAXE_SCHOOL_TYPE_5_TEXT',get_string('typeschool5','praxe'));
define('PRAXE_SCHOOL_TYPE_6',6);
define('PRAXE_SCHOOL_TYPE_6_TEXT',get_string('typeschool_other','praxe'));

define('PRAXE_STATUS_ASSIGNED',0);
define('PRAXE_STATUS_REFUSED',1);
define('PRAXE_STATUS_CONFIRMED',2);
define('PRAXE_STATUS_SCHEDULE_DONE',3);
define('PRAXE_STATUS_FINISHED',4);
define('PRAXE_STATUS_EVALUATED',5);
define('PRAXE_STATUS_CLOSED',6);
define('PRAXE_TAB_VIEW_STUDENT',0);
define('PRAXE_TAB_VIEW_TEACHER',1);
define('PRAXE_TAB_VIEW_EDITTEACHER',2);
define('PRAXE_TAB_VIEW_EXTTEACHER',3);
define('PRAXE_TAB_VIEW_HEADM',4);
define('PRAXE_TAB_STUDENT_HOME',0);
define('PRAXE_TAB_STUDENT_MYSCHOOL',1);
define('PRAXE_TAB_STUDENT_SCHEDULE',2);
define('PRAXE_TAB_STUDENT_EDITLOC',3);
define('PRAXE_TAB_STUDENT_ADDSCHEDULE',4);
//define('PRAXE_TAB_TEACHER_HOME',0);
define('PRAXE_TAB_EDITTEACHER_HOME',0);
define('PRAXE_TAB_EDITTEACHER_SCHOOLS',1);
define('PRAXE_TAB_EDITTEACHER_ADDSCHOOL',2);
define('PRAXE_TAB_EDITTEACHER_VIEWSCHOOL',3);
define('PRAXE_TAB_EDITTEACHER_TEACHERS',4);
define('PRAXE_TAB_EDITTEACHER_ASSIGNTEACHERS',5);
define('PRAXE_TAB_EDITTEACHER_EDITSCHOOL',6);
define('PRAXE_TAB_EDITTEACHER_LOCATIONS',7);
define('PRAXE_TAB_EDITTEACHER_ADDLOCATION',8);
define('PRAXE_TAB_EXTTEACHER_HOME',0);
define('PRAXE_TAB_EXTTEACHER_MYLOCATIONS',1);
define('PRAXE_TAB_EXTTEACHER_MYSCHOOLS',2);
define('PRAXE_TAB_EXTTEACHER_EDITLOCATION',3);
define('PRAXE_TAB_EXTTEACHER_COPYLOCATION',4);
define('PRAXE_TAB_HEADM_HOME',0);
define('PRAXE_TAB_HEADM_ADDSCHOOL',1);
define('PRAXE_TAB_HEADM_TEACHERS',2);
define('PRAXE_TAB_HEADM_ADDLOCATION',3);
define('PRAXE_TAB_HEADM_EDITSCHOOL',4);
define('PRAXE_TAB_HEADM_ASSIGNTEACHERS',5);
define('PRAXE_TAB_HEADM_LOCATIONS',6);
$tab_modes = array(	'student' => array('home','myschool','schedule','editloc','addschedule'),
					'extteacher' => array('home','mylocations','myschools','editlocation','copylocation'),
					'headm' => array('home','addschool','teachers','addlocation','editschool','assignteachers','locations'),
					'teacher' => array(),
					'editteacher' => array('home','schools','addschool','viewschool','teachers','assignteachers','editschool','locations','addlocation')
					);
require_once($CFG->dirroot . '/mod/praxe/lib.php');
function praxe_array_search($needle,$haystack,$arraykey=false) {
	if(!is_array($haystack)) {
		return false;
	}
	foreach($haystack as $key=>$value) {
		$current_key=$key;
		if($arraykey){
			if($needle == $value[$arraykey]){
				return $key;
			}
			if(praxe_array_search($needle,$value->$arraykey) == true) {
				return $current_key;
			}
		}else{
			if($needle == $value){
				return $value;
			}
			if(praxe_array_search($needle,$value) == true) {
				return $current_key;
			}
		}
	}
	return false;
}
function praxe_has_capability($strcap) {
	global $context;
	return (has_capability('mod/praxe:'.$strcap, $context) || has_capability('mod/praxe:manageallincourse', $context));
}
function praxe_praxehome_buttons($print = false) {
	global $cm;
	$ret = '';
	$ret .= '<div class="praxe homelink" style="text-align:center">'. "\n";
	$ret .= '<a href="view.php?id='.$cm->id.'">'.s($cm->name).'</a>' . "\n";
	$ret .= '</div>' . "\n";
	if(!$print) {
		return $ret;
	}
	echo $ret;
}
function praxe_object_search($needle,$haystack,$arraykey=false) {
	if(!is_array($haystack)) {
		return false;
	}
	foreach($haystack as $key=>$value) {
		$current_key=$key;
		if($arraykey){
			if($needle == $value->$arraykey){
				return $key;
			}
			if(praxe_object_search($needle,$value->$arraykey) == true) {
				return $current_key;
			}
		}else{
			if($needle == $value){
				return $value;
			}
			if(praxe_object_search($needle,$value) == true) {
				return $current_key;
			}
		}
	}
	return false;
}
function praxe_get_isced_text($isced) {
	if($isced == PRAXE_ISCED_0) {
		return PRAXE_ISCED_0_TEXT;
	}elseif($isced == PRAXE_ISCED_2) {
		return PRAXE_ISCED_2_TEXT;
	}elseif($isced == PRAXE_ISCED_3) {
		return PRAXE_ISCED_3_TEXT;
	}
	return get_string('no_existing_isced','praxe');
}
function praxe_get_term_text($term) {
	if($term == PRAXE_TERM_WS) {
		return PRAXE_TERM_WS_TEXT;
	}elseif($term == PRAXE_TERM_SS) {
		return PRAXE_TERM_SS_TEXT;
	}
	return get_string('no_existing_term','praxe');
}
/**
 * Returns all locations depending on isced and(or) studyfield, if they are set. Otherwise returns all locations.
 * @param int $isced [optional][default null]
 * @param int $studyfield [optional][default null]
 * @param bool $active [optional][default null] - true, false
 * @param bool bOnlyActual[optional][default 0] - if is set to true or 1 select ignore all params before and returns only locations for this instance of praxe
 * @param int $year [optional][default 0] - specific year
 * @return array/false - result of get_records_sql()
 */
function praxe_get_locations($isced = 0, $studyfield = null, $active = null, $bOnlyActual = 0, $year = 0) {
	global $CFG, $DB;
	$sql = "SELECT loc.*, school.name, school.street, school.city, school.zip, school.headmaster, school.email, school.phone, school.website,
				head.firstname as head_name, head.lastname as head_lastname,
				ext.id as extteacherid, teacher.id as teacherid, teacher.firstname as teacher_name, teacher.lastname as teacher_lastname,
				studyf.name as studyfieldname, studyf.shortcut
				FROM {praxe_locations} loc
				LEFT JOIN {praxe_schools} school ON (school = school.id)
				LEFT JOIN {user} head ON (head.id = school.headmaster)
				LEFT JOIN {praxe_school_teachers} ext ON (ext.id = loc.teacher)
				LEFT JOIN {user} teacher ON (teacher.id = ext_teacher)
				LEFT JOIN {praxe_studyfields} studyf ON (studyf.id = studyfield)";
	$where = array();
	/// location for a specifit isced level ///
	if($bOnlyActual) {
		$where[] = "loc.year = ".praxe_record::getData('year');
		$where[] = "loc.term = ".praxe_record::getData('term');
		$where[] = "loc.studyfield = ".praxe_record::getData('studyfield');
		if(praxe_record::getData('isced') > 0) {
			$where[] = "loc.isced = ".praxe_record::getData('isced');
		}
	}else {
		if($isced > 0) {
			$where[] = "isced = ".(int)$isced;
		}
		if(!is_null($studyfield)) {
			$where[] = "studyfield = ".(int)$studyfield;
		}
		if(!is_null($active)) {
			$where[] = ($active) ? 'active = 1' : 'active = 0';
		}
		if($year) {
		    $where[] = 'loc.year = '.(int)$year;
		}
	}
	if(count($where)) {
		$sql .= " WHERE ".implode(" AND ",$where);
	}
	//print_object($sql);
	return $DB->get_records_sql($sql);
}
function praxe_get_locations_by_schooldata($schoolid = null, $headm = null, $bOnlyActual = 0, $year = 0) {
	global $CFG, $DB;
	$sql = "SELECT loc.*, school.name, school.street, school.city, school.zip, school.headmaster, school.email, school.phone, school.website,
				head.firstname as head_name, head.lastname as head_lastname,
				ext.id as extteacherid, teacher.id as teacherid, teacher.firstname as teacher_name, teacher.lastname as teacher_lastname,
				studyf.name as studyfieldname, studyf.shortcut
				FROM {praxe_locations} loc
				LEFT JOIN {praxe_schools} school ON (school = school.id)
				LEFT JOIN {user} head ON (head.id = school.headmaster)
				LEFT JOIN {praxe_school_teachers} ext ON (ext.id = loc.teacher)
				LEFT JOIN {user} teacher ON (teacher.id = ext_teacher)
				LEFT JOIN {praxe_studyfields} studyf ON (studyf.id = studyfield)";
	$where = array();
	if(!is_null($schoolid)) {
		$where[] = "school = ".(int)$schoolid;
	}
	if(!is_null($headm)) {
		$where[] = "headmaster = ".(int)$headm;
	}
	if($bOnlyActual != 0) {
		$where[] = "loc.year = ".praxe_record::getData('year');
		$where[] = "loc.term = ".praxe_record::getData('term');
		$where[] = "loc.studyfield = ".praxe_record::getData('studyfield');
		if(praxe_record::getData('isced') > 0) {
			$where[] = "loc.isced = ".praxe_record::getData('isced');
		}
	}else {
	    if($year) {
	        $where[] = "loc.year = ".(int)$year;
	    }
	}
	if(count($where)) {
		$sql .= " WHERE ".implode(" AND ",$where);
	}
	//print_object($sql);
	return $DB->get_records_sql($sql);
}
/**
 * Return result of get_record_sql. Includes data of headmaster, external teacher and school.
 * @param int $id
 * @param int $teacherid [optional] - id of user who is external teacher assigned to the location
 */
function praxe_get_location($id, $teacherid = null) {
	global $CFG, $DB;
	$sql = "SELECT loc.*, school.name, school.street, school.city, school.zip, school.headmaster, school.email, school.phone, school.website,
				head.firstname as head_name, head.lastname as head_lastname,
				teacher.id as teacherid, teacher.firstname as teacher_name, teacher.lastname as teacher_lastname
				FROM {praxe_locations} loc
				LEFT JOIN {praxe_schools} school ON (school = school.id)
				LEFT JOIN {user} head ON (head.id = school.headmaster)
				LEFT JOIN {praxe_school_teachers} ext ON (ext.id = loc.teacher)
				LEFT JOIN {user} teacher ON (teacher.id = ext_teacher)
				WHERE loc.id = $id";
	if(!is_null($teacherid)) {
		$sql .= " AND teacher.id = ".(int)$teacherid;
	}
	return $DB->get_record_sql($sql);
}
function praxe_get_available_locations($user, $isced = 0, $studyfield = null) {
	global $cm, $course, $CFG, $DB;
	if(!is_array($all = praxe_get_locations($isced, $studyfield, true, true))) {
		return false;
	}
	/// used locations in all instances of praxe, which are set iqual like this instance (term, year, studyfield,isced) ///
	$sql = "SELECT rec.location, rec.*
				FROM {praxe_records} rec
				INNER join {praxe} praxe ON (praxe = praxe.id)
				WHERE year = ".date('Y',mktime())." AND term = ".praxe_record::getData('term')."
				AND studyfield = ".praxe_record::getData('studyfield')."
				AND (status <> ".PRAXE_STATUS_REFUSED." OR student = ".$user.")";
	/// selection of location with specific isced level ///
	if($isced > 0) {
		$sql .= "AND isced = ".praxe_record::getData('isced');
	}
	if(!is_array($used = $DB->get_records_sql($sql))) {
		return $all;
	}
	$result = array_diff_key($all, $used);
	return $result;
}
/**
 *
 * Returns all records according to parameters.<br>
 * Return data includes basic location, school, extteacher and student informations.
 * @param int $praxeid [optional] - id of praxe instance
 * @param array $order [optional]
 * @param int $teacherid [optional]
 * @param int $studentid [optional]
 *
 * @return array
 */
function praxe_get_praxe_records($praxeid = null, $order = null, $teacherid = null, $studentid = null) {
	global $CFG,$DB;
	$sql = "SELECT rec.*, loc.subject, loc.id as locid, school.name as schoolname, school.id as schoolid,
			stud.firstname, stud.lastname, stud.id as userid, ext.id as extteacherid,
			teacher.id as teacherid, teacher.firstname as teacher_firstname, teacher.lastname as teacher_lastname
			FROM {praxe_records} rec
			LEFT JOIN {praxe_locations} loc ON(location = loc.id)
			LEFT JOIN {praxe_schools} school ON(school = school.id)
			LEFT JOIN {user} stud ON(student = stud.id)
			LEFT JOIN {praxe_school_teachers} ext ON(teacher = ext.id)
			LEFT JOIN {user} teacher ON(ext_teacher = teacher.id)";
	$where = array();
	if(!is_null($praxeid)) {
		$where[] = "praxe = $praxeid";
	}
	if(!is_null($teacherid)){
		$where[] = "teacher.id = $teacherid";
	}
	if(!is_null($studentid)){
		$where[] = "rec.student = $studentid";
	}
	if(count($where)) {
		$sql .= ' WHERE '.implode(' AND ',$where);
	}
	if(empty($order) || !is_array($order)) {
		$order = 'name, subject, lastname, firstname';
	}else {
		$order = implode(',',$order);
	}
	$sql .= " order by $order";
	return $DB->get_records_sql($sql);
}
/**
 *
 * @param int $headmst[optional] - headmaster id
 * @param int $teacher[otpional] - teacher(user) id
 * @param int $location[optional] - location id
 * @return array/false - result of get_records_sql()
 */
function praxe_get_schools($headmst = null, $teacher = null, $location = null) {
	global $CFG, $DB;
	// show all schools //
	$sql = "SELECT school.id, school.name, school.type, school.street, school.city, school.zip, school.email, school.phone, school.website, school.headmaster, school.usermodified, school.timecreated, school.timemodified, headm.firstname, headm.lastname
			FROM {praxe_schools} school
			LEFT JOIN {user} headm ON(headmaster = headm.id)
			LEFT JOIN {praxe_school_teachers} ext ON(teacher_school = school.id)
			LEFT JOIN {user} teacher ON(ext_teacher = teacher.id)
			LEFT JOIN {praxe_locations} loc ON(loc.school = school.id)";
	$where = array();
	if(!is_null($headmst)){
		$where[] = "headmaster = $headmst";
	}
	if(!is_null($teacher)){
		$where[] = "teacher.id = $teacher";
	}
	if(!is_null($location)){
		$where[] = "loc.id = $location";
	}
	if(count($where)) {
		$sql .= ' WHERE '.implode(' AND ',$where);
	}
	$sql .= ' GROUP BY school.id, school.name, school.type, school.street, school.city, school.zip, school.email, school.phone, school.website, school.headmaster, school.usermodified, school.timecreated, school.timemodified, headm.firstname, headm.lastname';
	$sql .= ' ORDER BY name';
	return $DB->get_records_sql($sql);
}
function praxe_get_ext_teachers_at_school($headm = null, $schoolid = null) {
	global $CFG, $DB;
	// get all info about school //
	$sql = "SELECT ext_teach.id as ext_teacher_id, head.firstname as headm_firstname, head.lastname as headm_lastname,
			teacher.id as teacherid, teacher.firstname as firstname, teacher.lastname as lastname,
			school.id as schoolid, school.name, school.street, school.city, school.headmaster
			FROM {praxe_school_teachers} ext_teach
			LEFT JOIN {praxe_schools} school ON (school.id = teacher_school)
			LEFT JOIN {user} head ON (headmaster = head.id)
			LEFT JOIN {user} teacher ON (ext_teacher = teacher.id)";
	$where = array();
	if(!is_null($headm)){
		$where[] = "headmaster = ".(int)$headm;
	}
	if(!is_null($schoolid)) {
		$where[] = "school.id = ".(int)$schoolid;
	}
	if(count($where)) {
		$sql .= " WHERE ".implode(" AND ",$where);
	}
	$sql .= " ORDER BY school.name, lastname, firstname";
	//print_object($sql);
	return $DB->get_records_sql($sql);
}
function praxe_get_record($recordid) {
	global $CFG, $DB;
	$sql = "SELECT rec.*, stud.id as userid, stud.firstname, stud.lastname,
			subject, loc.id as locationid, name as name,
			ext.id as extteacherid, teacher.id as teacherid, teacher.firstname as teacher_firstname, teacher.lastname as teacher_lastname
			FROM {praxe_records} rec
			LEFT JOIN {user} stud ON(stud.id = rec.student)
			LEFT JOIN {praxe_locations} loc ON(location = loc.id)
			LEFT JOIN {praxe_schools} school ON(school = school.id)
			LEFT JOIN {praxe_school_teachers} ext ON(teacher = ext.id)
			LEFT JOIN {user} teacher ON(teacher.id = ext_teacher) WHERE rec.id = {$recordid}";
	return $DB->get_record_sql($sql);
}
function praxe_get_schedule($schid) {
	global $CFG, $DB;
	$sql = "SELECT sch.*, rec.status, rec.praxe, rec.student, schins.schedule, schins.inspector, inspect.firstname, inspect.lastname
			FROM {praxe_schedules} sch
			LEFT JOIN {praxe_records} rec ON(rec.id = sch.record)
			LEFT JOIN {praxe_schedule_inspections} schins ON(schedule = sch.id)
			LEFT JOIN {user} inspect ON(inspect.id = inspector)
			WHERE sch.id = $schid";
	$ret = $DB->get_records_sql($sql);
	if(!is_array($ret)) {
		return $ret;
	}
	foreach($ret as $insp) {
		if(!isset($r)) {
			$r = $insp;
			$r->inspectors = array();
		}
		if(!is_null($insp->inspector)) {
			$r->inspectors[$insp->inspector] = (object) array('id'=>$insp->inspector, 'firstname'=>$insp->firstname, 'lastname'=>$insp->lastname);
		}
	}
	unset($r->inspector);
	unset($r->firstname);
	unset($r->lastname);
	return $r;
}
/**
 *
 * @param int $recid - id of prexe record connected to the schedule items
 * @param array $order [optional] - array of fields to sort by
 * @param bool $incDeleted [optional][default false] - also get items 'deleted' by students
 * @return result of get_records_sql
 */
function praxe_get_schedules($recid, $order = null, $incDeleted = false) {
	global $CFG, $DB;
	$sql = 	"SELECT sch.*, rec.status, rec.praxe, rec.student, schins.schedule, schins.inspector, inspect.firstname, inspect.lastname
			FROM {praxe_schedules} sch
			LEFT JOIN {praxe_records} rec ON(rec.id = sch.record)
			LEFT JOIN {praxe_schedule_inspections} schins ON(schedule = sch.id)
			LEFT JOIN {user} inspect ON(inspect.id = inspector)
			WHERE record=$recid";
	if(!$incDeleted) {
		$sql .= " AND sch.deleted IS NULL";
	}
	if(!is_array($order) || !count($order)) {
		$order = array('timestart', 'timeend');
	}
	$sql .= " ORDER BY ".implode(', ',$order);
	$ret = $DB->get_records_sql($sql);
	if(!$ret) {
		return $ret;
	}
	$rr = array();
	foreach($ret as $insp) {
		if(!isset($rr[$insp->id])) {
			$rr[$insp->id] = $insp;
			$rr[$insp->id]->inspectors = array();
		}
		if(!is_null($insp->inspector)) {
			$rr[$insp->id]->inspectors[$insp->inspector] = (object) array('id'=>$insp->inspector, 'firstname'=>$insp->firstname, 'lastname'=>$insp->lastname);
		}
		if(isset($rr[$insp->id]->inspector)) {
			unset($rr[$insp->id]->inspector);
			unset($rr[$insp->id]->firstname);
			unset($rr[$insp->id]->lastname);
		}
	}
	return $rr;
}
function praxe_get_school($schoolid) {
	global $CFG, $DB;
	if(empty($schoolid)){
		return false;
	}
	// get all info about school //
	$sql = "SELECT school.*, head.firstname as headm_firstname, head.lastname as headm_lastname,
			ext_teach.id as ext_teacher_id, teacher.id as teacher_id, teacher.firstname as teacher_firstname,
			teacher.lastname as teacher_lastname
			FROM {praxe_schools} school
			LEFT JOIN {user} head ON (headmaster = head.id)
			LEFT JOIN {praxe_school_teachers} ext_teach ON (school.id = teacher_school)
			LEFT JOIN {user} teacher ON (ext_teacher = teacher.id)
			WHERE school.id = {$schoolid}";
	$res = $DB->get_recordset_sql($sql);

	foreach($res as $id=>$sch) {
		if(!isset($result)) {
			$result = $sch;
			$result->teachers = array();
		}
		if(!is_null($sch->teacher_id)) {
			$result->teachers[$sch->teacher_id] = new stdClass();
		    $result->teachers[$sch->teacher_id]->firstname = $sch->teacher_firstname;
			$result->teachers[$sch->teacher_id]->lastname = $sch->teacher_lastname;
			$result->teachers[$sch->teacher_id]->user_id = $sch->teacher_id;
			$result->teachers[$sch->teacher_id]->id = $sch->ext_teacher_id;
		}
	}
	if(isset($result)) {
		unset($result->teacher_firstname);
		unset($result->teacher_lastname);
		unset($result->teacher_id);
		unset($result->ext_teacher_id);
		return $result;
	}else{
		return false;
	}
}
function praxe_get_base_url($params = array()) {
	global $cm;
	(array)($params);
	$params = array_merge(array('id'=>$cm->id),$params);
	return new moodle_url("/mod/praxe/view.php",$params);
}
function praxe_get_schooltype_info($schooltype) {
	eval('$type = PRAXE_SCHOOL_TYPE_'.$schooltype.'_TEXT;');
	if(is_string($type)) {
		return $type;
	}
	return '';
}
function praxe_get_status_info($statusvalue, $role = 'global') {
	switch($statusvalue) {
		case PRAXE_STATUS_ASSIGNED :
			$text = get_string('status_assigned_text','praxe');
			break;
		case PRAXE_STATUS_REFUSED :
			if($role == 'student') {
			    $text = get_string('status_refused_text_for_student','praxe');
			}else {
		        $text = get_string('status_refused_text','praxe');
			}
			break;
		case PRAXE_STATUS_CONFIRMED :
			$text = get_string('status_confirmed_text','praxe');
			break;
		case PRAXE_STATUS_SCHEDULE_DONE :
			$text = get_string('status_schedule_done_text','praxe');
			break;
		case PRAXE_STATUS_EVALUATED :
			$text = get_string('status_evaluated_text','praxe');
			break;
		case PRAXE_STATUS_FINISHED :
			$text = get_string('status_finished_text','praxe');
			break;
		case PRAXE_STATUS_CLOSED :
			$text = get_string('status_closed_text','praxe');
			break;
		default :
			$text = '';
			break;
	}
	return $text;
}
function praxe_get_stud_record($userid) {
	global $CFG, $DB;
	$sql = "SELECT stud.id as userid, stud.firstname, stud.lastname,
			rec.*, subject, name as schoolname
			FROM {user} stud
			LEFT JOIN {praxe_records} rec ON(student = stud.id)
			LEFT JOIN {praxe_locations} loc ON(location = loc.id)
			LEFT JOIN {praxe_schools} school ON(school = school.id)
			WHERE user.id = '{$userid}'";
	return $DB->get_record_sql($sql);
}

function praxe_get_use_status_of_location($locid, $year=null) {
	global $CFG, $DB;
	$sql = "SELECT rec.*, stud.firstname, stud.lastname
			FROM {praxe_records} rec
			LEFT JOIN {user} stud ON(student = stud.id) WHERE";
	$where = " rec.location = ".$locid;
	if(!is_null($year)) {
		$where .= ' AND year = '.(int)$year;
	}
	if($ret = $DB->get_record_sql($sql.$where)){
	    if($ret->status == PRAXE_STATUS_REFUSED) {
			return praxe_get_status_info($ret->status).' '.get_string('available_location','praxe');
		}else{
		    return praxe_get_status_info($ret->status);
		}
	}
	return get_string('available_location','praxe');
}
/**
 * Returns fulname of given user according to capabilities of current user to view fullnames and details of given user
 * @param mixed $user - Object of user data or id of user. If id set, the object of user data will be created by this id.<br>
 * Object requires data id, lastname, firstname!
 * @return string/false - if error occures or bad parameter is given, returns false.
 */
function praxe_get_user_fullname($user) {
	global $USER, $context, $CFG, $course, $DB;
	if(!is_object($user)) {
		$user = $DB->get_record('user',array('id'=>(int)$user));
	}
	if(!is_object($user)) {
		return false;
	}
	$usercontext = context_user::instance($user->id);
	$contextcanviewdetails = has_capability('moodle/user:viewdetails', $context);
	$usercontextcanviewdetails = has_capability('moodle/user:viewdetails', $usercontext);
	if ($piclink = ($USER->id == $user->id || $contextcanviewdetails || $usercontextcanviewdetails)) {
		if ($usercontextcanviewdetails) {
			$canviewfullname = has_capability('moodle/site:viewfullnames', $usercontext);
		} else {
			$canviewfullname = has_capability('moodle/site:viewfullnames', $context);
		}
		$profilelink = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id.'">'.fullname($user, $canviewfullname).'</a></strong>';
	} else {
		$profilelink = '<strong>'.fullname($user, has_capability('moodle/site:viewfullnames', $context)).'</strong>';
	}
	return $profilelink;
}
function praxe_get_yearclass($yearclass) {
	$yc = $yearclass;
	if($yearclass >= 6 && $yearclass <= 9) {
		$yc = PRAXE_ISCED_2_TEXT." - $yc.";
	}else if($yearclass >= 10) {
		$yc = PRAXE_ISCED_3_TEXT.' - '.($yc-9).".";
	}
	return $yc;
}
function praxe_get_years_for_filter($active = null, $school = null, $bOnlyActual = 0, $teacher = null, $headm = null) {
	global $CFG, $DB;
	$sql = "SELECT loc.*, school.headmaster, ext_teacher
				FROM {praxe_locations} loc
				LEFT JOIN {praxe_schools} school ON (school = school.id)
				LEFT JOIN {praxe_school_teachers} ext ON (ext.id = loc.teacher)";
	$where = array();

	if($teacher) {
	    $where[] = "ext_teacher = ".(int)$teacher;
	}
    if($headm) {
	    $where[] = "headmaster = ".(int)$headm;
	}
    if($active) {
		$where[] = ($active) ? 'active = 1' : 'active = 0';
	}
    if($school) {
		$where[] = 'loc.school = '.(int)$school;
	}
	if($bOnlyActual) {
		$where[] = "loc.year = ".praxe_record::getData('year');
		$where[] = "loc.term = ".praxe_record::getData('term');
		$where[] = "loc.studyfield = ".praxe_record::getData('studyfield');
		if(praxe_record::getData('isced') > 0) {
			$where[] = "loc.isced = ".praxe_record::getData('isced');
		}
	}
	if(count($where)) {
		$sql .= " WHERE ".implode(" AND ",$where);
	}
	return $DB->get_records_sql($sql);
}

/**
 *
 * @param int $locid - location id
 * @return bool
 */
function praxe_is_location_fully_editable($locid) {
    global $DB;
	$sql = "SELECT rec.id
			FROM {praxe_records} rec
			LEFT JOIN {user} stud ON(student = stud.id) WHERE rec.location = {$locid}";
	$loc = $DB->get_record_sql($sql);
	if(!$loc) {
	    return true;
	}
    return false;
}
function praxe_print_tab_footer() {
	echo "</div></div>";
}
class praxe_record {
    public static $data;
	function praxe_record($userid) {
		global $cm, $course, $praxe, $DB;
		praxe_record::$data = $praxe;
		if($result = $DB->get_records('praxe_records', array('praxe' => $praxe->id, 'student' => $userid), 'timecreated DESC')) {
		    $result = array_shift($result);
			foreach($result as $col=>$val) {
					$name = "rec_$col";
					praxe_record::$data->$name = $val;

			}
			praxe_record::$data->location = praxe_get_location($result->location);
		}
		if(is_numeric($praxe->studyfield)) {
			if($stf = $DB->get_record('praxe_studyfields', array('id' => $praxe->studyfield))) {
				praxe_record::$data->studyfield_name = $stf->name;
				praxe_record::$data->studyfield_shortcut = $stf->shortcut;
			}
		}
	}
	/**
	 * If the variable is set and is defined, returns its value. Otherwise returns whole array of static object $data
	 * @param string $var[optional] - if not set, all object of data will be return.
	 * @return mixed / NULL
	 */
	public function getData($var = null) {
		if(is_null($var)){
			return praxe_record::$data;
		}
		if(isset(praxe_record::$data->$var)){
			return praxe_record::$data->$var;
		}
	}
}
?>
