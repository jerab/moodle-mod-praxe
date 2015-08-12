<?php

/**
 *
 */

require_once ('c_actionform.php');

class praxe_addlocation extends praxe_actionform {
	private $schoolid = null;
	function praxe_addlocation($schoolid = null) {
		if(!is_null($schoolid)) {
			$this->schoolid = (int)$schoolid;
		}
		parent::praxe_actionform();
	}
    function definition() {
    	global $USER, $context, $DB;
    	/// Adding fields
    	$mform =& $this->_form;
    	$mform->addElement('hidden', 'post_form', 'addlocation');
    	$school = praxe_get_school($this->schoolid);
		$mform->addElement('hidden', 'school', $school->id);
		$mform->addElement('static', 'static_school', get_string('school', 'praxe'), s($school->name));

		$teachers = praxe_get_ext_teachers_at_school(null, $this->schoolid);
		//print_object($teachers);
		if(count($teachers)) {
			$options = array(0 => '---');
        	foreach($teachers as $t) {
        		$options[$t->ext_teacher_id] = s($t->firstname).' '.s($t->lastname);
        	}
			$mform->addElement('select', 'teacher', get_string('teacher', 'praxe'), $options);
		}else{
			$mform->addElement('static', 'static_teacher', get_string('extteacher', 'praxe'), get_string('no_teachers_available', 'praxe'));
		}

		if(!$this->error) {
			$result = $DB->get_records('praxe_studyfields', null, 'name, shortcut');
			$options = array();
        	foreach($result as $sf) {
        		$options[$sf->id] = $sf->name." (".$sf->shortcut.")";
        	}
			$mform->addElement('select', 'studyfield', get_string('studyfield', 'praxe'), $options);
			$options = array(	PRAXE_ISCED_2 => PRAXE_ISCED_2_TEXT,
								PRAXE_ISCED_3 => PRAXE_ISCED_3_TEXT);
			$mform->addElement('select', 'isced', get_string('iscedlevel', 'praxe'), $options);
	        $mform->addElement('text', 'subject', get_string('subject', 'praxe'), array('size'=>'64'));
	        $mform->setType('subject', PARAM_TEXT);
	        $mform->addRule('subject', null, 'required', null, 'client');
	        $mform->addRule('subject', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
	        $actualyear = (int)date('Y',time());
			$options = array(	$actualyear => $actualyear,
								($actualyear+1) => ($actualyear+1),
								($actualyear+2) => ($actualyear+2));
			$mform->addElement('select', 'year', get_string('year', 'praxe'), $options);
			///	Adding the "term" select field to choose the summer term/winter term for current course
			$options = array(	PRAXE_TERM_WS => PRAXE_TERM_WS_TEXT,
								PRAXE_TERM_SS => PRAXE_TERM_SS_TEXT);
			$mform->addElement('select', 'term', get_string('term', 'praxe'), $options);
	        $act = $mform->addElement('selectyesno', 'active', get_string('active', 'praxe'));
	        $mform->setDefault('active',1);
	        $this->add_action_buttons(true, get_string('submit'));
		}
    }
	/**
     * It is used if the edit data required. Set values of form elements and add hidden values for edit.
     * @param object $data - object with form data
     */
    public function set_form_to_edit($data) {
    	$mform =& $this->_form;
    	foreach((array)$data as $k=>$v) {
    	    if($mform->elementExists($k)) {
    			$mform->setDefault($k,s($v),true);
    		}
    	}
    	$actualyear = (int)date('Y',time());
        $freeze = array();
        if(!praxe_is_location_fully_editable($data->id)) {
            $freeze = array('studyfield', 'isced', 'year','term','active');
	    }
        if(!praxe_has_capability('assignteachertolocation')) {
		    $freeze[] = 'teacher';
		}
		$mform->freeze($freeze);

    	$mform->addElement('hidden', 'edit', 'true');
    	$mform->addElement('hidden', 'locationid', $data->id);
    }

	public function set_form_to_copy($data) {
    	$mform =& $this->_form;
    	foreach($data as $k=>$v) {
    		if($mform->elementExists($k)) {
    			$mform->setDefault($k,s($v),true);
    		}
    	}
    }
}

?>
