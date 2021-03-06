<?php
/**
 * This page prints a particular instance of praxe_record(s) for external_headmasters
 * and allows them to manage their schools and locations
 *
 * @author  Tomas Jerabek <t.jerab@gmail.com>
 * @version
 * @package mod/praxe
 */
/// extending class for classes "praxe_view_[role]" ///
require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');
class praxe_view_headm extends praxe_view {
	public $form;
	function praxe_view_headm() {
		global $USER, $cm, $tab, $tab_modes, $CFG, $context;
		//$praxeaction = optional_param('praxeaction', null, PARAM_ALPHAEXT);
		//$school = optional_param('school', null, PARAM_INT);
		switch($tab) {
			case PRAXE_TAB_HEADM_HOME :
				//$schoolid = optional_param('schoolid', 0, PARAM_INT);
				if($schools = praxe_get_schools($USER->id)) {
					$this->content .= self::show_schools($schools, array('mode'=>$tab_modes['headm'][PRAXE_TAB_HEADM_EDITSCHOOL]));
				}else {
				    $this->content .= get_string('noschoolsavailable','praxe');
				}
				break;
			case PRAXE_TAB_HEADM_ADDSCHOOL :
				self::addschool_form();
				break;
			case PRAXE_TAB_HEADM_TEACHERS :
				$schoolid = optional_param('schoolid', 0, PARAM_INT);
				if($schoolid > 0) {
					$this->content .= self::school_teachers_by_schools($USER->id, $schoolid);
				}else {
					$this->content .= self::school_teachers_by_schools($USER->id);
				}
				break;
			case PRAXE_TAB_HEADM_LOCATIONS :
				$schoolid = optional_param('schoolid', 0, PARAM_INT);
				if($schoolid > 0) {
					$this->content .= self::show_locations($schoolid);
				}else {
					$this->content .= self::show_locations(null, $USER->id);
					$this->content .= "<p>".get_string('to_create_location_choose_school','praxe')."</p>";
				}
				break;
			case PRAXE_TAB_HEADM_EDITSCHOOL :
				$schoolid = required_param('schoolid', PARAM_INT);
				$school = praxe_get_school($schoolid);
				if(!praxe_has_capability('editownschool') || !is_object($school) || !$school->headmaster == $USER->id) {
					print_error('notallowedaction', 'praxe');
				}
				self::addschool_form();
				$this->form->set_form_to_edit($school);
				break;
			case PRAXE_TAB_HEADM_ASSIGNTEACHERS :
				require_capability('mod/praxe:assignteachertoownschool',$context, $USER->id);
				$schoolid = required_param('schoolid', PARAM_INT);
				require_once($CFG->dirroot . '/mod/praxe/c_assignteachers.php');
				$this->form = new praxe_assignteachers($schoolid);
				break;
			case PRAXE_TAB_HEADM_ADDLOCATION :
				require_capability('mod/praxe:createownlocation',$context, $USER->id);
				$schoolid = required_param('schoolid', PARAM_INT);
				require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
				$this->form = new praxe_addlocation($schoolid);
				break;
			default:
				redirect($CFG->wwwroot.'/mod/praxe/view.php?id='.$cm->id);
				break;
		}
	}
	public function addschool_form() {
		global $CFG;
		require_once($CFG->dirroot . '/mod/praxe/c_addschool.php');
		$this->form = new praxe_addschool();
	}
	public function addlocation_form() {
		global $CFG;
		require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
		$this->form = new praxe_addlocation();
	}
	/**
	 *
	 * @param string $schoolid
	 * @param string $headmid
	 * @param unknown $editlinkparams
	 * @param number $bOnlyActual
	 * @param number $year
	 * @param array $aSort Associative array with column as key and ASC/DESC/null as value
	 * @return Ambigous <string, lang_string>|string
	 */
	public static function show_locations($schoolid = null, $headmid = null, $editlinkparams = array(), $bOnlyActual = 0, $year = 0, $aSort = array()) {
		global $USER, $cm, $OUTPUT, $DB;
		$schoolid = ($schoolid) ? $schoolid : null;
		$sortAvailable = array('sschool'=>'name', 'sstudyfield'=>'studyfieldname', 'sisced'=>'isced', 'syear'=>'year', 'ssubject'=>'subject', 'sactive'=>'active');
		$aSortcorrect = array_intersect(array_keys($sortAvailable), array_keys($aSort));
		$sort = array();
		foreach($aSortcorrect as $k) {
			if(!is_null($aSort[$k])) {
				$sort[] = $sortAvailable[$k].' '.$aSort[$k];
			}
		}
		$locs = praxe_get_locations_by_schooldata($schoolid, $headmid, $bOnlyActual, $year, implode(', ', array_reverse($sort)));
		if(!$locs) {
			return get_string('nolocationsavailable','praxe');
		}
		$defEnableAssignStudent = praxe_has_capability('assignstudenttolocation');

		$table = new html_table();

		$baseLink = praxe_get_base_url(array('mode'=>'locations', 'schoolid'=>(int)$schoolid, 'fyearloc'=>$year, 'factual'=>$bOnlyActual));
		$h = array();
		$aHeadSorting = array(	'sschool'=>get_string('school','praxe'),
								'ssubject'=>get_string('subject','praxe'),
								'sactive'=>get_string('active','praxe'),
								'sstudyfield'=>get_string('studyfield','praxe'),
								'sisced'=>get_string('iscedlevel','praxe'),
								'syear'=>get_string('year','praxe'));
		foreach($aHeadSorting as $k=>$text) {
			if(isset($aSort[$k])) {
				$toLink = ($aSort[$k] == 'ASC') ? 'DESC' : 'ASC';
				$sOut = $OUTPUT->action_link($baseLink.'&amp;'.$k.'='.$toLink, $text);
				$sOut .= $OUTPUT->pix_icon('t/sort_'.strtolower($aSort[$k]), $aSort[$k]);
			}else {
				$sOut = $OUTPUT->action_link($baseLink.'&amp;'.$k.'=ASC', $text);
			}
			$aHeadSorting[$k] = $sOut;
		}
		$h[] = $aHeadSorting['sschool'];
		$h[] = $aHeadSorting['ssubject'];
		$h[] = get_string('extteacher','praxe');
		$h[] = $aHeadSorting['sstudyfield'];
		$h[] = $aHeadSorting['sisced'];
		$h[] = $aHeadSorting['syear'];
		$h[] = get_string('term','praxe');
		$h[] = get_string('actual_status','praxe');
		$h[] = $aHeadSorting['sactive'];
		$h[] = get_string('student','praxe');
		$table->head = $h;
		$table->align = array ('left', 'left', 'left', 'left', 'left', 'center','center','left','center');
		$table->data = array();
		$stredit = get_string('edit');
		foreach($locs as $loc) {
		    $editable = false;
			if(praxe_has_capability('manageallincourse')
				|| praxe_has_capability('editanylocation')
				|| (praxe_has_capability('editownlocation') && ($USER->id == $loc->headmaster || $USER->id == $loc->teacherid))) {
					$editable = true;
			}
			$teacher = '';
			if(!is_null($loc->teacherid)) {
				$teacher = praxe_get_user_fullname((object)array('id'=>$loc->teacherid, 'firstname'=>s($loc->teacher_name), 'lastname'=>$loc->teacher_lastname));
			}
			$stf = s($loc->studyfieldname).' ('.s($loc->shortcut).')';
			$status = praxe_get_use_status_of_location($loc->id);
			$active = ($loc->active == 1) ? get_string('yes') : get_string('no');
			$row = array(s($loc->name), s($loc->subject), $teacher, $stf,
								praxe_get_isced_text($loc->isced),
								s($loc->year),
								praxe_get_term_text($loc->term),
								$status,
								$active);

			/// icon for assign student / student ///
			$sql = "SELECT p.*, s.firstname, s.lastname FROM {praxe_records} p LEFT JOIN {user} s ON (s.id = p.student)
					WHERE location = ?
					ORDER BY timemodified DESC
					LIMIT 1";
			$params = array($loc->id);
			if($ret = $DB->get_record_sql($sql, $params)){
				$row[] = praxe_get_user_fullname((object)array('id' => $ret->student, 'firstname' => $ret->firstname, 'lastname' => $ret->lastname));
				$enableAssignStudent = ($defEnableAssignStudent && $ret->status == PRAXE_STATUS_REFUSED);
			}else {
				$row[] = "&nbsp;";
				$enableAssignStudent = $defEnableAssignStudent;
			}

			if($editable || $enableAssignStudent) {
				$editRow = array();
				$params = array('locationid' => $loc->id);
				foreach($editlinkparams as $name=>$val) {
					$params[s($name)] = s($val);
				}

				if($editable) {
					$editRow[] = $OUTPUT->action_icon(praxe_get_base_url($params), new pix_icon('t/edit',$stredit));
				}

				if($enableAssignStudent) {
					$params['assignuser'] = 1;
					$editRow[] = $OUTPUT->action_icon(praxe_get_base_url($params,'assigntolocation'), new pix_icon('i/users',get_string('assignusertolocation','praxe')));
				}

				$row[] = implode(' ', $editRow);
				if(count($table->head) < count($row)) {
					$table->head[] = get_string('edit');
					$table->align[] = 'center';
				}
			}
			$row = new html_table_row($row);
			if($loc->active != 1) {
			    $row->attributes['class'] = 'praxe_noactive_location';
			}
			$table->data[] = $row;
		}
		return html_writer::table($table);
	}
	/**
	 *
	 * @param int $schoolid
	 * @param array $editlinkparams [optional]
	 * @return string - table parsed by html_writer
	 */
	public static function show_school($schoolid, $editlinkparams = array()) {
		global $USER, $OUTPUT;

		if(!$school = praxe_get_school($schoolid)) {
		    return null;
		}
		$table = new html_table();
		$table->attributes['class'] = 'generaltable boxaligncenter';

        $table->head = array(get_string('school_detail','praxe'));
        $table->headspan = array(2);

        $table->colclasses = array('praxe_cell right', 'praxe_cell left');

        $table->data[] = array(get_string('schoolname','praxe'), s($school->name));
		$table->data[] = array(get_string('schooltype','praxe'), praxe_get_schooltype_info($school->type));
		$table->data[] = array(get_string('street','praxe'), s($school->street));
		$table->data[] = array(get_string('city','praxe'), s($school->city));
		$table->data[] = array(get_string('zipcode','praxe'), s($school->zip));
		$table->data[] = array(get_string('phone','praxe'), s($school->phone));
		$table->data[] = array(get_string('email','praxe'), s($school->email));
		$table->data[] = array(get_string('website','praxe'), s($school->website));
	    if(!is_null($school->headmaster)) {
			$table->data[] = array(get_string('headmaster','praxe'), praxe_get_user_fullname($school->headmaster));
		}

		$ret = '';
		if(praxe_has_capability('editanyschool')) {
		    //|| (praxe_has_capability('editownschool') AND isset($school->teachers[$USER->id]))) {
		    $params = array("schoolid" => $schoolid, "detail" => 1);
		    foreach($editlinkparams as $name=>$val) {
				$params[s($name)] = s($val);
			}
			$ret = $OUTPUT->single_button(praxe_get_base_url($params), get_string('editschool','praxe'), 'get', array('class'=>'praxe_button praxe_center'));
		}
		return html_writer::table($table).$ret;
	}
	/**
	 *
	 * @param array $schools - array of schools objects to be displayed
	 * @param array $editlinkparams[optional] - default parameters to url that are always added: id=$cm->id, schoolid=id of school.<br>
	 * Extra parameters to be added must be in array format as nameOfParameter=>value
	 * @param array $aSort[optional] - used sorting in actual list of schools (name, type) - values (ASC, DESC)
	 * Items must be in array format as nameOfParameter=>value
	 * @return string
	 */
	public static function show_schools($schools, $editlinkparams = array(), $aSort = array()) {
		global $USER, $OUTPUT;
		$baseLink = praxe_get_base_url(array('mode'=> 'schools'));
		$table = new html_table();
		$strname = get_string('schoolname','praxe');
		if(isset($aSort['name'])) {
			$toLink = ($aSort['name'] == 'ASC') ? 'DESC' : 'ASC';
			$strname = $OUTPUT->action_link($baseLink.'&amp;sname='.$toLink, $strname);
			$strname .= $OUTPUT->pix_icon('t/sort_'.strtolower($aSort['name']), $aSort['name']);
		}else {
			$strname = $OUTPUT->action_link($baseLink.'&amp;sname=ASC', $strname);
		}
		$strtype = get_string('schooltype','praxe');
		if(isset($aSort['type'])) {
			$toLink = ($aSort['type'] == 'ASC') ? 'DESC' : 'ASC';
			$strtype = $OUTPUT->action_link($baseLink.'&amp;stype='.$toLink, $strtype);
			$strtype .= $OUTPUT->pix_icon('t/sort_'.strtolower($aSort['type']), $aSort['type']);
		}else {
			$strtype = $OUTPUT->action_link($baseLink.'&amp;stype=ASC', $strtype);
		}
		$straddress = get_string('address','praxe');
		$strcontact = get_string('contact','praxe');
		$table->head = array($strname, $strtype, $straddress, $strcontact);
		$table->align = array ('left', 'left', 'left', 'left');
		$params = array();
		foreach($editlinkparams as $name=>$val) {
			$params[s($name)] = s($val);
		}
		$baseLink = praxe_get_base_url($params);
		foreach($schools as $sch) {
			$address = s($sch->street).', '.s($sch->zip).'  '.s($sch->city);
			$contact = array();
			if(!empty($sch->email)) {
				$contact[] = s($sch->email);
			}
			if(!empty($sch->phone)) {
				$contact[] = s($sch->phone);
			}
			if(!empty($sch->website)) {
				$contact[] = s($sch->website);
			}
			$contact = html_writer::tag('p', implode("<br>", $contact));

			$schooltype = constant('PRAXE_SCHOOL_TYPE_'.(int)$sch->type.'_TEXT');
			$row = array(s($sch->name), $schooltype, $address, $contact);
			if(praxe_has_capability('editanyschool') || (praxe_has_capability('editownschool') && $sch->headmaster == $USER->id)) {
				$stredit = get_string('editschool','praxe');
				$row[] = $OUTPUT->action_icon($baseLink.'&amp;schoolid='.$sch->id, new pix_icon('t/edit',$stredit));
			}
			$table->data[] = $row;
		}
		if(isset($stredit)) {
			$table->head[] = $stredit;
			$table->align[] = 'center';
		}
		return html_writer::table($table);
	}
	/**
	 *
	 * @param int $headm [optional][default null]
	 * @param int $schoolid [optional][default null]
	 * @return string - if no records, returns empty string
	 */
	public static function school_teachers_by_schools($headm = null, $schoolid = null) {
		$ret = '';
		$ext = praxe_get_ext_teachers_at_school($headm, $schoolid);
		if(!$ext) {
			return get_string('no_teachers_for_this_school','praxe');
		}
		$arr = array();
		foreach($ext as $id=>$teach) {
			if(!isset($arr[$teach->schoolid])) {
				$arr[$teach->schoolid] = new stdClass();
				$arr[$teach->schoolid]->name = $teach->name;
				$arr[$teach->schoolid]->street = $teach->street;
				$arr[$teach->schoolid]->teachers = array();
			}
			$arr[$teach->schoolid]->teachers[$id] = new stdClass();
			$arr[$teach->schoolid]->teachers[$id]->firstname = $teach->firstname;
			$arr[$teach->schoolid]->teachers[$id]->lastname = $teach->lastname;
			$arr[$teach->schoolid]->teachers[$id]->id = $teach->teacherid;
		}
		$strstud = get_string('firstname').' / '.get_string('lastname');
		$strloc = get_string('locations','praxe');
		$table = new html_table();
		$table->head  = array ($strstud);
		$table->align = array ('left');
		foreach($arr as $sch) {
			$data = array();
			foreach($sch->teachers as $teach) {
				if(is_null($teach->id)) {
					continue;
				}
				$data[] = array(praxe_get_user_fullname($teach->id));
			}
			if(count($data)) {
				$table->data = $data;
				if(is_null($schoolid)) {
					$ret .= '<h3>'.s($sch->name).'</h3>';
				}
				$ret .= html_writer::table($table);
			}
		}
		return $ret;
	}
}
?>