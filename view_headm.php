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
	public function show_locations($schoolid = null, $headmid = null, $editlinkparams = array(), $bOnlyActual = 0, $year = 0) {
		global $USER, $cm, $OUTPUT;
		$schoolid = ($schoolid) ? $schoolid : null;
		$locs = praxe_get_locations_by_schooldata($schoolid, $headmid, $bOnlyActual, $year);
		if(!$locs) {
			return get_string('nolocationsavailable','praxe');
		}

		$table = new html_table();
		$h = array();
		$h[] = get_string('school','praxe');
		$h[] = get_string('subject','praxe');
		$h[] = get_string('extteacher','praxe');
		$h[] = get_string('studyfield','praxe');
		$h[] = get_string('iscedlevel','praxe');
		$h[] = get_string('year','praxe');
		$h[] = get_string('term','praxe');
		$h[] = get_string('actual_status','praxe');
		$h[] = get_string('active','praxe');
		$table->head = $h;
		$table->align = array ('left', 'left', 'left', 'left', 'left', 'center');
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
			if($editable) {
				$params = array('locationid'=>$loc->id);
				foreach($editlinkparams as $name=>$val) {
					$params[s($name)] = s($val);
				}
				$row[] = $OUTPUT->action_icon(praxe_get_base_url($params), new pix_icon('t/edit',$stredit));
				if(count($table->head) == 7) {
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
	public function show_school($schoolid, $editlinkparams = array()) {
		global $USER, $OUTPUT;

		if(!$school = praxe_get_school($schoolid)) {
		    return null;
		}
		$table = new html_table();
		$table->attributes['class'] = 'generaltable boxaligncenter';

        $table->head = array(get_string('school_detail','praxe'));
        $table->headspan = array(2);

        $table->colclasses = array('praxe_cell right', 'praxe_cell left');

        $table->data[] = array(get_string('school_detail','praxe'), s($school->name));
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
	 * @return string
	 */
	public function show_schools($schools, $editlinkparams = array()) {
		global $USER, $OUTPUT;

		$table = new html_table();
		$strname = get_string('schoolname','praxe');
		$strtype = get_string('schooltype','praxe');
		$straddress = get_string('address','praxe');
		$strcontact = get_string('contact','praxe');
		$table->head = array($strname, $strtype, $straddress, $strcontact);
		$table->align = array ('left', 'left', 'left', 'left');
		foreach($schools as $sch) {
			$address = s($sch->street).', '.s($sch->zip).'  '.s($sch->city);
			$contact = new html_table();
			//$contact->head = array('');
			$contact->data = array();
			if(!empty($sch->phone)) {
				$contact->data[] = array(s($sch->phone));
			}
			if(!empty($sch->email)) {
				$contact->data[] = array(s($sch->email));
			}
			if(!empty($sch->website)) {
				$contact->data[] = array(s($sch->website));
			}
			$schooltype = constant('PRAXE_SCHOOL_TYPE_'.(int)$sch->type.'_TEXT');
			$contact = html_writer::table($contact);
			$row = array(s($sch->name), $schooltype, $address, $contact);
			if(praxe_has_capability('editanyschool') || (praxe_has_capability('editownschool') && $sch->headmaster == $USER->id)) {
				$stredit = get_string('editschool','praxe');
				$params = array("schoolid"=>$sch->id);
				foreach($editlinkparams as $name=>$val) {
					$params[s($name)] = s($val);
				}
				$row[] = $OUTPUT->action_icon(praxe_get_base_url($params), new pix_icon('t/edit',$stredit));
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
	public function school_teachers_by_schools($headm = null, $schoolid = null) {
		//global $course, $CFG;
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
/// no schools assigned to user yet ///
?>
