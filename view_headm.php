<?php 

/**
 * This page prints a particular instance of praxe_record(s) for external_headmasters
 * and allows them to manage their schools and locations
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
 * @package mod/praxe
 */
/*
if(!has_capability('mod/praxe:editschool',$context)) {
	error("You don't have rights for this action!");
} 
*/
/// extending class for classes "praxe_view_[role]" ///
require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');

class praxe_view_headm extends praxe_view {
	
	private $url;
	public $form;
	
	function praxe_view_headm() {
		global $USER, $cm, $tab, $tab_modes, $CFG, $context;
		$this->url = "view.php?id=".$cm->id;
		//$praxeaction = optional_param('praxeaction', null, PARAM_ALPHAEXT);
		//$school = optional_param('school', null, PARAM_INT);
		
		switch($tab) {
			case PRAXE_TAB_HEADM_HOME :
				$schoolid = optional_param('schoolid', null, PARAM_INT);
				if(is_array($schools = praxe_get_schools($USER->id))) {					
					$this->content .= self::show_schools($schools, array('mode'=>$tab_modes['headm'][PRAXE_TAB_HEADM_EDITSCHOOL]));					
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
					error(get_string('notallowedaction','praxe'));	
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
	
	public function show_locations($schoolid = null, $headmid = null, $editlinkparams = array(), $bOnlyActual = 0) {
		global $USER, $CFG, $cm;
		if(!is_array($locs = praxe_get_locations_by_schooldata($schoolid, $headmid, $bOnlyActual))) {
			$ret = get_string('nolocationsavailable','praxe');			
			return $ret;
		}
						
		$table = new stdClass();
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
				$params = array("locationid=$loc->id");
				foreach($editlinkparams as $name=>$val) {
					$params[] = s($name)."=".s($val);
				}								
				$row[] = "<a title=\"$stredit\" href=\"".praxe_get_base_url($params)."\">"
								."<img src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> "; 
        		
				if(count($table->head) == 7) {
					$table->head[] = get_string('edit');
					$table->align[] = 'center';
				}
			}
			$table->data[] = $row;
		}
		return print_table($table, true);		 
	}
	
	public function show_school($schoolid) {		
		global $cm, $USER;
		$school = praxe_get_school($schoolid);		
		$ret = '';
		$ret .= "<table class=\"generaltable boxaligncenter\" cellspacing=\"1\" cellpadding=\"5\" >";
			$ret .= "<tr><th class=\"header\" colspan=\"2\">".get_string('school_detail','praxe')."</th></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('schoolname','praxe').":</td><td class=\"praxe_cell left\">".s($school->name)."</td></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('schooltype','praxe').":</td><td>".praxe_get_schooltype_info($school->type)."</td></tr>";
			//$address = s($school->street).'<br>'.s($school->zip).'&nbsp;&nbsp;'.s($school->city);
			//$ret .= "<tr><td class=\"praxe_cell right\">".get_string('address','praxe').":</td><td>".$address."</td></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('street','praxe').":</td><td>".s($school->street)."</td></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('city','praxe').":</td><td>".s($school->city)."</td></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('zipcode','praxe').":</td><td>".s($school->zip)."</td></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('phone','praxe').":</td><td>".s($school->phone)."</td></tr>";			
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('email','praxe').":</td><td>".s($school->email)."</td></tr>";
			$ret .= "<tr><td class=\"praxe_cell right\">".get_string('website','praxe').":</td><td>".s($school->website)."</td></tr>";
			if(!is_null($school->headmaster)) {
				$headm = praxe_get_user_fullname($school->headmaster);
				$ret .= "<tr><td class=\"praxe_cell right\">".get_string('headmaster','praxe').":</td><td>$headm</td></tr>";
			}
					
		$ret .= "</table>";		
		return $ret;
	}
	
	/**
	 * 
	 * @param array $schools - array of schools objects to be displayed 
	 * @param array $editlinkparams[optional] - default parameters to url that are always added: id=$cm->id, schoolid=id of school.<br>
	 * Extra parameters to be added must be in array format as nameOfParameter=>value 
	 * @return string 
	 */
	public function show_schools($schools, $editlinkparams = array()) {
		global $context, $USER, $CFG, $cm;
		
		$table = new stdClass();
		$strname = get_string('schoolname','praxe');
		$strtype = get_string('schooltype','praxe');
		$straddress = get_string('address','praxe');
		$strcontact = get_string('contact','praxe');
		$table->head = array($strname, $strtype, $straddress, $strcontact);
		$table->align = array ('left', 'left', 'left', 'left');		
		foreach($schools as $sch) {
			$address = s($sch->street).', '.s($sch->zip).'  '.s($sch->city);
			
			$contact = new stdClass();
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
			eval('$schooltype =  PRAXE_SCHOOL_TYPE_'.(int)$sch->type.'_TEXT;');
			$contact = print_table($contact,true);
			$row = array(s($sch->name), $schooltype, $address, $contact);
			
			
			if(praxe_has_capability('editanyschool') || (praxe_has_capability('editownschool') && $sch->headmaster == $USER->id)) {
				$stredit = get_string('edit');
				$params = array("id=$cm->id","schoolid=$sch->id");
				foreach($editlinkparams as $name=>$val) {
					$params[] = s($name)."=".s($val);
				}
				$params = implode('&amp;',$params);				
				$row[] = "<a title=\"$stredit\" href=\"view.php?$params\">"
								."<img src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> "; 
        		
			}
			
			$table->data[] = $row;			 
		}
		
		if(isset($stredit)) {
			$table->head[] = $stredit;
			$table->align[] = 'center';
		}
		
		return print_table($table, true);
	}
	/**
	 * 
	 * @param int $headm [optional][default null]
	 * @param int $schoolid [optional][default null]
	 * @return string - if no records, returns empty string
	 */
	public function school_teachers_by_schools($headm = null, $schoolid = null) {
		global $course, $CFG;
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
		
		$strstud = get_string('firstname').'  '.get_string('lastname');
		$strloc = get_string('locations','praxe');		
		$table = new stdClass();					
		$table->head  = array ($strstud);
		$table->align = array ('left');
		foreach($arr as $sch) {			
			$data = array();			
			foreach($sch->teachers as $teach) {
				if(is_null($teach->id)) {
					continue;
				}
				//$url = $CFG->wwwroot.'/user/view.php?id='.(int)$teach->id.'&amp;course='.$course->id;
				$data[] = array(praxe_get_user_fullname($teach->id));			
			}
			if(count($data)) {
				$table->data = $data;
				if(is_null($schoolid)) {
					$ret .= '<h3>'.s($sch->name).'</h3>';
				}
				$ret .= print_table($table,true);
			}
		}
		return $ret;		
	}	
	
}
/// no schools assigned to user yet ///





?>
