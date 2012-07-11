<?php
require_once ('c_actionform.php');

class praxe_makeschedule extends praxe_actionform {
	function praxe_makeschedule() {
		parent::praxe_actionform(get_class($this));
	}
    public function definition() {
		$mform =& $this->_form;

        $mform->addElement('hidden', 'post_form', 'makeschedule');
    	$options = array(	'startyear' => praxe_record::getData('year'),
                 			'stopyear'  => praxe_record::getData('year'),
                 			'applydst'  => true,
							'optional'	=> false,
                 			'step'      => 5
                );
        $attr = array("onchange"=>"praxe.setscheduletimeend(this.name,this.value)");
        $this->add_to_content(get_string('error_timeschedule','praxe'), true);
        $mform->addElement('date_time_selector', 'timestart', get_string('lesson_start','praxe'), $options, $attr);
        $mform->addElement('date_time_selector', 'timeend', get_string('lesson_end','praxe'), $options);
        $mform->setDefault('timestart', array('day' => date('j'), 'month' => date('n'), 'year' => praxe_record::getData('year'), 'hour' => 8, 'minute' => 0));
        $mform->setDefault('timeend', array('day' => date('j'), 'month' => date('n'), 'year' => praxe_record::getData('year'), 'hour' => 8, 'minute' => 45));
        $options = array('null'=>get_string('choose'),'0'=>'0.', '1'=>'1.', '2'=>'2.', '3'=>'3.', '4'=>'4.', '5'=>'5.', '6'=>'6.', '7'=>'7.', '8'=>'8.', '9'=>'9.',
        				'10'=>'10.', '11'=>'11.', '12'=>'12.', '13'=>'13.', '13'=>'13.', '14'=>'14.', '15'=>'15.');
        $mform->addElement('select', 'lesnumber', get_string('lesson_number','praxe'), $options);
        $mform->addRule('lesnumber',null, 'required',null, 'client');
        $mform->addRule('lesnumber',get_string('choose_lesson_number_info','praxe'), 'numeric', null, 'client');
        $options = array('6'=>PRAXE_ISCED_2_TEXT.' - 6.', '7'=>PRAXE_ISCED_2_TEXT.' - 7.', '8'=>PRAXE_ISCED_2_TEXT.' - 8.', '9'=>PRAXE_ISCED_2_TEXT.' - 9.',
        				 '10'=>PRAXE_ISCED_3_TEXT.' - 1.', '11'=>PRAXE_ISCED_3_TEXT.' - 2.', '12'=>PRAXE_ISCED_3_TEXT.' - 3.', '13' => PRAXE_ISCED_3_TEXT.' - 4.', '14' => PRAXE_ISCED_3_TEXT.' - 5.');
        $mform->addElement('select', 'yearclass', get_string('yearclass','praxe'), $options);
        $mform->addElement('text', 'schoolroom', get_string('schoolroom','praxe'), array('size'=>'12'));
        $mform->addRule('schoolroom', null, 'required', null, 'client');
        $mform->addElement('text', 'lessubject', get_string('subject','praxe'), array('size'=>'64'));
        $mform->setDefault('lessubject', praxe_record::$data->location->subject);
        $mform->addRule('lessubject', null, 'required', null, 'client');

        /*$options = array(
		    'subdirs'=>0,
		    'maxbytes'=>0,
		    'maxfiles'=>0,
		    'changeformat'=>0,
		    'context'=>null,
		    'noclean'=>0,
		    'trusttext'=>0);*/
        $mform->addElement('editor', 'lestheme', get_string('lesson_theme','praxe'));
        $mform->setType('lestheme', PARAM_RAW);
        $this->add_action_buttons();
    }

	/**
     * It is used if the edit data required. Set values of form elements and add hidden values for edit.
     * @param object $data - object with form data - scheduleid,time,lesnumber,yearclass,schoolroom,lessubject,lestheme
     * @return bool - if this item is editable, returns "true", otherwise "false"
     */
    public function set_form_to_edit($data) {
    	// date and time of this schedule to edit has expired ///
    	if($data->timestart-PRAXE_TIME_TO_EDIT_SCHEDULE < mktime()) {
    		return false;
    	}
    	$mform =& $this->_form;
    	foreach((array)$data as $k=>$v) {
    		if($mform->elementExists($k)) {
    			if($k == 'lestheme') {
                    $mform->setDefault($k,array('text'=>$v));
    			}else {
    			    $mform->setDefault($k,s($v));
    			}
    		}
    	}
    	$mform->addElement('hidden', 'edit', 'true');
    	$mform->addElement('hidden', 'scheduleid', $data->id);
    	return true;
    }
}

?>
