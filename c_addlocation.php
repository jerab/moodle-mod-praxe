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
    	global $USER, $context;
    	    	
    	/// Adding fields
    	$mform =& $this->_form;
    	
    	$mform->addElement('hidden', 'post_form', 'addlocation');
    	
    	//$mform->addElement('header', 'praxeaddlocationfieldset', $this->school->name);
    	
    	
    	$school = praxe_get_school($this->schoolid);
    	/*
    	if(is_array($schools) && count($schools) > 1) {		
			$options = array();
        	foreach($schools as $sch) {
        		$options[$sch->id] = $sch->name;
        	}        		
			$mform->addElement('select', 'school', get_string('school', 'praxe'), $options);
		}else if(is_array($schools)) {*/
			//$sch = array_shift($schools);
			$mform->addElement('hidden', 'school', $school->id);
			$mform->addElement('static', 'static_school', get_string('school', 'praxe'), s($school->name));			
		/*}else{
			$mform->addElement('html', '<div class="praxe_error">'.get_string('noschoolsavailable').'</div>');
			$error = true;
		}*/
		$teachers = praxe_get_ext_teachers_at_school(null, $this->schoolid);
		//print_object($teachers);
		if(is_array($teachers) && count($teachers) > 1) {
    		//print_object($teachers);		
			$options = array();
        	foreach($teachers as $t) {
        		$options[$t->ext_teacher_id] = s($t->firstname).' '.s($t->lastname);
        	}        		
			$mform->addElement('select', 'teacher', get_string('teacher', 'praxe'), $options);
		}else if(is_array($teachers)) {
			$t = array_shift($teachers);
			$mform->addElement('hidden', 'teacher', $t->ext_teacher_id);
			$mform->addElement('static', 'static_teacher', get_string('extteacher', 'praxe'), s($t->firstname).' '.s($t->lastname));
		}else{			
			$this->error = get_string('no_teachers_for_this_school', 'praxe');
		}
		
		if(!$this->error) {
			/*if(is_numeric($stf = praxe_record::getData('studyfield'))) {		
				$mform->addElement('hidden', 'studyfield', $stf);
				$mform->addElement('static', 'static_studyfield', get_string('studyfield', 'praxe'), praxe_record::getData('studyfield_name')." (".praxe_record::getData('studyfield_shortcut').")");				
			}else {*/
				$result = get_records('praxe_studyfields','','','name, shortcut');				
				$options = array();
        		foreach($result as $sf) {
        			$options[$sf->id] = $sf->name." (".$sf->shortcut.")";
        		}        		
				$mform->addElement('select', 'studyfield', get_string('studyfield', 'praxe'), $options);
			//}
											
				$options = array(	PRAXE_ISCED_2 => PRAXE_ISCED_2_TEXT,
							PRAXE_ISCED_3 => PRAXE_ISCED_3_TEXT);
				$mform->addElement('select', 'isced', get_string('iscedlevel', 'praxe'), $options);				
			//}
						
	        $mform->addElement('text', 'subject', get_string('subject', 'praxe'), array('size'=>'64'));
	        $mform->setType('subject', PARAM_TEXT);
	        $mform->addRule('subject', null, 'required', null, 'client');
	        $mform->addRule('subject', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');        
	        $actualyear = (int)date('Y',mktime());
			$options = array(	$actualyear => $actualyear,
								($actualyear+1) => ($actualyear+1),
								($actualyear+2) => ($actualyear+2));
			$mform->addElement('select', 'year', get_string('year', 'praxe'), $options);
			//$mform->addRule('year', null, 'required', null, 'client');
			
			///	Adding the "term" select field to choose the summer term/winter term for current course		
			$options = array(	PRAXE_TERM_WS => PRAXE_TERM_WS_TEXT,
								PRAXE_TERM_SS => PRAXE_TERM_SS_TEXT);
			$mform->addElement('select', 'term', get_string('term', 'praxe'), $options);
			//$mform->addRule('term', null, 'required', null, 'client');
			
	        $act = $mform->addElement('selectyesno', 'active', get_string('active', 'praxe'));
	        $mform->setDefault('active',1);	        
	        $this->add_action_buttons(true, get_string('submit'));
		}/*else{			
			$this->content .= praxe_praxehome_buttons();
		}  */     

    }
    
	/**
     * It is used if the edit data required. Set values of form elements and add hidden values for edit.
     * @param object $data - object with form data - schoolid,name,type,street,city,zip,email,phone,website
     */
    public function set_form_to_edit($data) {
    	$mform =& $this->_form;    	    	
    	foreach($data as $k=>$v) {
    		if($mform->elementExists($k)) {
    			$mform->setDefault($k,s($v),true);	
    		}    		
    	}
    	//$mform->setDefault('post_form','a');
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
    	//$mform->setDefault('post_form','a');
    	//$mform->addElement('hidden', 'edit', 'true');
    	//$mform->addElement('hidden', 'locationid', $data->id);    	    	
    }
}

?>
