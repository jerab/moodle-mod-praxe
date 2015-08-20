<?php
require_once ('c_actionform.php');

class praxe_makeschedule extends praxe_actionform {
	function praxe_makeschedule() {
		parent::praxe_actionform(get_class($this), 'addschedule');
	}
    public function definition() {
		$mform =& $this->_form;

        $mform->addElement('hidden', 'post_form', 'makeschedule');
    	$mform->setType('post_form', PARAM_ALPHA);
        $attr = array("onchange"=>"praxe.setscheduletimeend(this.name,this.value)");
        $this->add_to_content(get_string('error_timeschedule','praxe'), true);

        $options = array('0'=>'0.', '1'=>'1.', '2'=>'2.', '3'=>'3.', '4'=>'4.', '5'=>'5.', '6'=>'6.', '7'=>'7.', '8'=>'8.', '9'=>'9.',
        		'10'=>'10.', '11'=>'11.', '12'=>'12.', '13'=>'13.', '13'=>'13.', '14'=>'14.', '15'=>'15.', '-1'=>'---');
        $mform->addElement('select', 'lesnumber', get_string('lesson_number','praxe'), $options);

        $options = array(	'startyear' => praxe_record::getData('year'),
        		'stopyear'  => praxe_record::getData('year'),
        		'applydst'  => true,
        		'optional'	=> false,
        		'step'      => 5
        );
        $fields= array(
        		$mform->createElement('date_time_selector', 'timestart', get_string('lesson_start','praxe'), $options, $attr),
        		$mform->createElement('date_time_selector', 'timeend', get_string('lesson_end','praxe'), $options)
        );
        $group = $mform->createElement('group', 'time', get_string('lesson_start','praxe').'<br>'.get_string('lesson_end','praxe'), $fields, '<br>', false);
        $mform->addElement($group);
        $mform->addHelpButton('time', 'schedule-lessontime', 'praxe');
        $starttime = time()+24*60*60;
        $mform->setDefault('timestart', array('day' => date('j',$starttime), 'month' => date('n',$starttime), 'year' => praxe_record::getData('year'), 'hour' => 8, 'minute' => 0));
        $mform->setDefault('timeend', array('day' => date('j',$starttime), 'month' => date('n',$starttime), 'year' => praxe_record::getData('year'), 'hour' => 8, 'minute' => 45));

		switch (praxe_record::getData('isced')) {
			case PRAXE_ISCED_1:
				$options = array('1'=>PRAXE_ISCED_1_TEXT.' - 1.', '2'=>PRAXE_ISCED_1_TEXT.' - 2.', '3'=>PRAXE_ISCED_1_TEXT.' - 3.', '4'=>PRAXE_ISCED_1_TEXT.' - 4.', '5'=>PRAXE_ISCED_1_TEXT.' - 5.');
				break;
			case PRAXE_ISCED_2:
				$options = array('6'=>PRAXE_ISCED_2_TEXT.' - 6.', '7'=>PRAXE_ISCED_2_TEXT.' - 7.', '8'=>PRAXE_ISCED_2_TEXT.' - 8.', '9'=>PRAXE_ISCED_2_TEXT.' - 9.');
				break;
			case PRAXE_ISCED_1:
				$options = array('10'=>PRAXE_ISCED_3_TEXT.' - 1.', '11'=>PRAXE_ISCED_3_TEXT.' - 2.', '12'=>PRAXE_ISCED_3_TEXT.' - 3.', '13' => PRAXE_ISCED_3_TEXT.' - 4.', '14' => PRAXE_ISCED_3_TEXT.' - 5.');
				break;
			default:
				$options = array(	'1'=>PRAXE_ISCED_1_TEXT.' - 1.', '2'=>PRAXE_ISCED_1_TEXT.' - 2.', '3'=>PRAXE_ISCED_1_TEXT.' - 3.', '4'=>PRAXE_ISCED_1_TEXT.' - 4.', '5'=>PRAXE_ISCED_1_TEXT.' - 5.',
						'6'=>PRAXE_ISCED_2_TEXT.' - 6.', '7'=>PRAXE_ISCED_2_TEXT.' - 7.', '8'=>PRAXE_ISCED_2_TEXT.' - 8.', '9'=>PRAXE_ISCED_2_TEXT.' - 9.',
						'10'=>PRAXE_ISCED_3_TEXT.' - 1.', '11'=>PRAXE_ISCED_3_TEXT.' - 2.', '12'=>PRAXE_ISCED_3_TEXT.' - 3.', '13' => PRAXE_ISCED_3_TEXT.' - 4.', '14' => PRAXE_ISCED_3_TEXT.' - 5.');
				break;
		}
		$mform->addElement('select', 'yearclass', get_string('yearclass','praxe'), $options);
        $mform->addElement('text', 'schoolroom', get_string('schoolroom','praxe'), array('size'=>'12'));
        $mform->addRule('schoolroom', get_string('error_schoolroom','praxe'), 'required', null, 'client');
        $mform->setType('schoolroom', PARAM_ALPHANUMEXT);
        $mform->addElement('text', 'lessubject', get_string('subject','praxe'), array('size'=>'64'));
        $mform->setDefault('lessubject', praxe_record::$data->location->subject);
        $mform->addRule('lessubject', null, 'required', null, 'client');
        $mform->setType('lessubject', PARAM_TEXT);
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
    	if(isset($data->timestart)) {
	    	if($data->timestart-PRAXE_TIME_TO_EDIT_SCHEDULE < time()) {
	    		return false;
	    	}
    	}
    	$def = array();
    	foreach((array)$data as $k=>$v) {
    		//if($mform->elementExists($k)) {
    			if($k == 'lestheme') {
                    $def[$k] = array('text'=>$v);
                }elseif($k == 'timestart' || $k == 'timeend') {
                  	$def[$k] = array('day' => date('j',$v), 'month' => date('n',$v), 'year' => date('Y',$v), 'hour' => date('G',$v), 'minute' => date('i',$v));
                   	//$mform->setDefault("time[$k]", $def);
                }elseif(is_numeric($v)) {
    			    $def[$k] = $v;
    			}else {
    				$def[$k] = s($v);
    			}
    	}
    	$this->set_data($def);
    	$mform =& $this->_form;
    	$mform->addElement('hidden', 'edit', 'true');
    	$mform->addElement('hidden', 'scheduleid', $data->id);
    	return true;
    }

    /**
     * (non-PHPdoc)     *
     * @see praxe_actionform::validation()
     * @param array $data - Requires timestart, timeend, schoolroom
     */
    public function validation($data, $files = array()) {
    	$aErrors = parent::validation($data, $files);
    	if($data['timestart'] > $data['timeend'] || $data['timestart'] < time()+60*60*4) {
			$aErrors['time'] = get_string('error_timeschedule','praxe');
		}

    	if(trim($data['schoolroom']) == '') {
    		$aErrors['schoolroom'] = get_string('error_schoolroom','praxe');
    	}

    	if(trim($data['lessubject']) == '') {
    		$aErrors['lessubject'] = get_string('err_required','form');
    	}
    	return $aErrors;
    }
}

?>
