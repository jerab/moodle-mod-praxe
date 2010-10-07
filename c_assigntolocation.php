<?php 

/**
 * 
 */

require_once ('c_actionform.php');

class praxe_assigntolocation extends praxe_actionform {
	
	function praxe_assigntolocation() {		
		parent::praxe_actionform(get_class($this));	
	}
	
    public function definition() {       	
        global $USER, $cm;        
 		if(is_array($locations = praxe_get_available_locations($USER->id, praxe_record::getData('isced'), praxe_record::getData('studyfield')))
    		&& count($locations)) {    		
    		
    		//$mform =& $this->_form;
    		$this->content_before_form .= get_string('assigntolocation_text_forstudents', 'praxe');    
    		$form = '<form class="mform" action="'.praxe_get_base_url().'" method="post">';
			$form .= '<input type="hidden" name="post_form" value="assignlocation" />';
			$form .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';		
    		$table = new stdClass();    		
    		$table->head = array('',
    							get_string('school','praxe'),
    							get_string('subject','praxe'),
    							get_string('teacher','praxe')
    						);
			$table->align = array('center','left','center','center');    							
			foreach($locations as $loc) {  			   		    			
    			$row = array('<input id="praxe_loc_'.$loc->id.'" type="radio" name="location" value="'.$loc->id.'" />');    			
    			$sch = "<a href=\"".praxe_get_base_url()."&amp;viewaction=viewschool&amp;schoolid=$loc->school\" title=\"".get_string('school_detail','praxe')."\">".s($loc->name)."</a>";		
    			$sch .= "<div class=\"praxe_detail\">".s($loc->street).', '.s($loc->city)."</div>";
    			$row[] = $sch;
    			$row[] = s($loc->subject);
				if(!is_null($loc->teacherid)) {
    				$teacher = (object) array('id' => $loc->teacherid, 'firstname' => s($loc->teacher_name), 'lastname' => s($loc->teacher_lastname));
    				$row[] = praxe_get_user_fullname($teacher);
    			}else {
    				$row[] = '';
    			}
    			$table->data[] = $row; 
    			//$row .= '<label for="praxe_loc_'.$loc->id.'">'.$text.'</label>';
    			//$form .= "<div class=\"tr\">$row</div>";    							
    		}						
			$form .= print_table($table,true);
			$form .= '<div class="fitem center" style="margin: 10px 0;">'
						.'<input type="submit" id="id_submitbutton" value="Submit" name="submitbutton" /> '
						.'<input type="submit" id="id_cancel" onclick="skipClientValidation = true; return true;" value="Cancel" name="cancel" />'
						.'</div>';
			$form .= '</form>';
			$this->content .= "<div>$form</div>";						
			 
    		//$mform->addElement('header', null, get_string('locations', 'praxe'));    		
    		/*
			$options = array();
    		$radioarray = array();    		
    		foreach($locations as $loc) {
    			//print_object($loc);
    			$link = "<a target='_blank' href='view.php?id=$cm->id&amp;praxeaction=viewschool&amp;schoolid=$loc->school' title='".get_string('school_detail','praxe')."'>".get_string('school_detail','praxe')."</a>";    			
    			$text = s($loc->name) . "($link) - " . s($loc->subject);
    			$text .= "<br>".s($loc->street).', '.s($loc->zip).'&nbsp;&nbsp;'.s($loc->city);
    			if(!is_null($loc->teacherid)) {
    				$teacher = (object) array('id' => $loc->teacherid, 'firstname' => s($loc->teacher_name), 'lastname' => s($loc->teacher_lastname));
    				$text .= " (".praxe_get_user_fullname($teacher).")";
    			}
    			    			
    			$radioarray[] = $mform->createElement('radio', 'location', null, $text, $loc->id, array('class'=>'radio location'));				
    		}
    		$mform->addGroup($radioarray, 'location', get_string('locations','praxe').':', '<hr>', false);
    		$mform->addRule('location', get_string('locationisrequired', 'praxe'), 'required', null, 'client');
    		$mform->addRule('location', get_string('locationisrequired', 'praxe'), 'required', null, 'server');
    		//$mform->addElement('select', 'location', get_string('chooselocation', 'praxe'), $options, array('size' => count($options) > 15 ? '15' : count($options)+1));    		  		
    		
    		$mform->addElement('hidden', 'post_form', 'assigntolocation');
    		*/        
    		//$this->add_action_buttons(true, get_string('submit'));			
    	}else{
    		$this->content_before_form .= get_string('nolocationsavailable', 'praxe');    		
    	}    	    	    	 
    }        
}

?>
