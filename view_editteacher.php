<?php
/**
 * This page prints a particular instance of praxe_record(s) for editteachers
 * and allows them to manage their schools and locations
 *
 * @package mod/praxe
 *
*/
/// extending class for classes "praxe_view_[role]" ///
require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');
class praxe_view_editteacher extends praxe_view {
	public $form;
	function praxe_view_editteacher() {
		global $DB, $tab, $CFG, $tab_modes, $context;
		switch($tab) {
			case PRAXE_TAB_EDITTEACHER_HOME :
				$detail = optional_param('recordid',0,PARAM_INT);
				require_once($CFG->dirroot . '/mod/praxe/view_extteacher.php');
				if($detail > 0 && ($record = praxe_get_record($detail))) {
					$schid = optional_param('scheduleid',0,PARAM_INT);
					if($schid > 0 && ($schedule = praxe_get_schedule($schid))) {
						$this->content .= praxe_view_extteacher::show_schedule_detail($schedule)."<hr>";
					}
					$this->content .= praxe_view_extteacher::show_record_detail($record);
				}else {
					$this->content .= self::show_all_students_records();
				}
				break;
			case PRAXE_TAB_EDITTEACHER_ADDSCHOOL :
				require_capability('mod/praxe:manageallincourse', $context);
				require_once($CFG->dirroot . '/mod/praxe/c_addschool.php');
				$this->form = new praxe_addschool();
				break;
			case PRAXE_TAB_EDITTEACHER_SCHOOLS :
				require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
				$schoolid = optional_param('schoolid', 0, PARAM_INT);
				if($schoolid == 0) {
					$schools = $DB->get_records('praxe_schools');
					$this->content .= praxe_view_headm::show_schools($schools, array('mode'=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_EDITSCHOOL]));
				}else{
					$this->content .= praxe_view_headm::show_school($schoolid, array('mode'=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_EDITSCHOOL]));
				}
				break;
			case PRAXE_TAB_EDITTEACHER_EDITSCHOOL :
				require_capability('mod/praxe:manageallincourse', $context);
				$schoolid = required_param('schoolid', PARAM_INT);
				if($school = praxe_get_school($schoolid)) {
					require_once($CFG->dirroot . '/mod/praxe/c_addschool.php');
					$this->form = new praxe_addschool();
					$this->form->set_form_to_edit($school);
				}
				break;
			case PRAXE_TAB_EDITTEACHER_TEACHERS :
				$schoolid = optional_param('schoolid', 0, PARAM_INT);
				require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
				if($schoolid > 0) {
					$this->content .= praxe_view_headm::school_teachers_by_schools(null, $schoolid);
				}else {
					$this->content .= praxe_view_headm::school_teachers_by_schools();
				}
				break;
			case PRAXE_TAB_EDITTEACHER_ASSIGNTEACHERS :
				require_capability('mod/praxe:manageallincourse', $context);
				$schoolid = required_param('schoolid', PARAM_INT);
				require_once($CFG->dirroot . '/mod/praxe/c_assignteachers.php');
				$this->form = new praxe_assignteachers($schoolid);
				break;
			case PRAXE_TAB_EDITTEACHER_LOCATIONS :
				$schoolid = optional_param('schoolid', 0, PARAM_INT);
				$locationid = optional_param('locationid', 0, PARAM_INT);
				$edit = optional_param('edit', null, PARAM_TEXT);
				$factual = optional_param('factualloc', 0, PARAM_INT);
				$fyear = optional_param('fyearloc', 0, PARAM_INT);

				/// edit location form ///
				if(!is_null($edit) && $locationid > 0) {
					if(!$loc = praxe_get_location($locationid)) {
						print_error('notallowedaction', 'praxe');
					}
					require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
					$this->form = new praxe_addlocation($loc->school);
					$this->form->set_redirect_url(null, array('mode'=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_LOCATIONS], 'schoolid'=>$schoolid));
					$this->form->set_form_to_edit($loc);
				}else{
				    require_once($CFG->dirroot . '/mod/praxe/view_headm.php');
					$this->content .= praxe_view_headm::show_locations($schoolid,null,array('edit'=>'true', 'mode'=>$tab_modes['editteacher'][PRAXE_TAB_EDITTEACHER_LOCATIONS],'schoolid'=>$schoolid),$factual, $fyear);
				}
				break;
			case PRAXE_TAB_EDITTEACHER_ADDLOCATION :
				require_capability('mod/praxe:manageallincourse', $context);
				$schoolid = required_param('schoolid', PARAM_INT);
				require_once($CFG->dirroot . '/mod/praxe/c_addlocation.php');
				$this->form = new praxe_addlocation($schoolid);
				break;
			default:
				break;
		}
	}
	public function show_all_students_records() {
		global $CFG;
		if($records = praxe_get_praxe_records(praxe_record::getData('id'),null,null,null,true,false)) {
			require_once($CFG->dirroot . '/mod/praxe/view_extteacher.php');
			$this->content .= praxe_view_extteacher::show_records($records);
		}else {
			$this->content .= get_string('no_praxe_records','praxe');
		}
	}
}
?>
