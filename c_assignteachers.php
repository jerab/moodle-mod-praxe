<?php

/**
 * 
 */

require_once ('c_actionform.php');

/// only headmasters and editing teachers should have access to this operation
class praxe_assignteachers extends praxe_actionform {
	
	private $schoolid = null;
	function praxe_assignteachers($schoolid = null) {		
		if(!is_null($schoolid)) {
			$this->schoolid = (int)$schoolid; 
		}
		parent::praxe_actionform();	
	}
	
    public function definition() {       	
        global $USER, $cm, $context;
        $error = false;
        
        $mform =& $this->_form;
        $mform->addElement('hidden', 'post_form', 'assignteachers');
		if(!$school = praxe_get_school($this->schoolid)) {
			error(get_string('notallowedaction','praxe'));
 		}
 		$mform->addElement('hidden', 'teacher_school', $this->schoolid);
 		$mform->addElement('static', 'static1', get_string('school','praxe'), s($school->name));

 		$ext = get_users_by_capability($context, 'mod/praxe:beexternalteacher', 'u.id, u.firstname, u.lastname','lastname, firstname', null, null, null, null, null, false); 		 		
    	if(is_array($ext) && count($ext)) {        	
	    	if(is_array($used_ext = praxe_get_ext_teachers_at_school(null,$this->schoolid))) {	 		
		 		foreach($used_ext as $t) {
		 			if(isset($ext[$t->teacherid])) {
		 				unset($ext[$t->teacherid]);
		 			}
		 		}
	    	}	    	
	 		if(!count($ext)) {
	 			$this->error = get_string('no_teachers_available','praxe');
	 		}else {
		 		$options = array();
	        	foreach($ext as $h) {
	        		$options[$h->id] = s($h->firstname)." ".s($h->lastname);
	        	}        		
				$mform->addElement('select', 'ext_teacher', get_string('extteacher', 'praxe'), $options);
	 		}
        }else{
        	$this->error = get_string('no_teachers_available','praxe');
        }        
        $this->add_action_buttons(true, get_string('submit'));           	    	    	 
    }    
}

?>
