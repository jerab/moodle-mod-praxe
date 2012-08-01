<?php //$Id: mod_form.php,v 1.2.2.3 2009/03/19 12:23:11 mudrd8mz Exp $

/**
 *
 */

require_once ('c_actionform.php');

class praxe_addschool extends praxe_actionform {

	function praxe_addschool() {
		parent::praxe_actionform();
	}

    function definition() {
    	global $USER, $context;

    	/// Adding fields
    	$mform =& $this->_form;

    	$mform->addElement('hidden', 'post_form', 'addschool');
    	if(optional_param('detail',0,PARAM_INT) == 1) {
    	    $mform->addElement('hidden', 'detail', 1);
    	}
    	$mform->addElement('header', 'praxeaddschoolfieldset');
        $mform->addElement('text', 'name', get_string('schoolname', 'praxe'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $options = array(	PRAXE_SCHOOL_TYPE_1 => PRAXE_SCHOOL_TYPE_1_TEXT,
							PRAXE_SCHOOL_TYPE_2 => PRAXE_SCHOOL_TYPE_2_TEXT,
							PRAXE_SCHOOL_TYPE_3 => PRAXE_SCHOOL_TYPE_3_TEXT,
							PRAXE_SCHOOL_TYPE_4 => PRAXE_SCHOOL_TYPE_4_TEXT,
							PRAXE_SCHOOL_TYPE_5 => PRAXE_SCHOOL_TYPE_5_TEXT,
							PRAXE_SCHOOL_TYPE_6 => PRAXE_SCHOOL_TYPE_6_TEXT);
		$mform->addElement('select', 'type', get_string('schooltype', 'praxe'), $options);

        $mform->addElement('text', 'street', get_string('street', 'praxe'), array('size'=>'64'));
        $mform->setType('street', PARAM_TEXT);
        $mform->addRule('street', null, 'required', null, 'client');
        $mform->addRule('street', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $mform->addElement('text', 'city', get_string('city', 'praxe'), array('size'=>'64'));
        $mform->setType('city', PARAM_TEXT);
        $mform->addRule('city', null, 'required', null, 'client');
        $mform->addRule('city', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $mform->addElement('text', 'zip', get_string('zipcode', 'praxe'), array('size'=>'20'));
        $mform->setType('zip', PARAM_TEXT);
        $mform->addRule('zip', null, 'numeric', null, 'client');
        $mform->addRule('zip', get_string('maximumchars', '', 5), 'maxlength', 5, 'client');

        $mform->addElement('text', 'email', get_string('email', 'praxe'), array('size'=>'20'));
        $mform->setType('email', PARAM_TEXT);
        $mform->addRule('email', null, 'email', null, 'client');
        $mform->addRule('email', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $mform->addElement('text', 'phone', get_string('phone', 'praxe'), array('size'=>'20'));
        $mform->setType('phone', PARAM_TEXT);
        $mform->addRule('phone', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $mform->addElement('text', 'website', get_string('website', 'praxe'), array('size'=>'20'));
        $mform->setType('website', PARAM_TEXT);
        $mform->addRule('website', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        if(has_capability('mod/praxe:manageallincourse', $context)) {
        	//TODO - nacist uzivatele z cohort EXTHEADM
        	praxe_get_cohort_members('EXTHEADM');
        	$headms = praxe_get_cohort_members(PRAXE_COHORT_HEADMASTERS);
        	if($headms) {
        		$options = array(0=>get_string('noselection','praxe'));
        		foreach($headms as $h) {
        			$options[$h->id] = s($h->firstname)." ".s($h->lastname);
        		}
				$mform->addElement('select', 'headmaster', get_string('headmaster', 'praxe'), $options);
        	}else{
        		$mform->addElement('hidden', 'headmaster', 0);
				$mform->addElement('static', 'static_headm', get_string('headmaster', 'praxe'), get_string('noselection','praxe'));
        	}
        }elseif(praxe_has_capability('beheadmaster')){
        	$mform->addElement('hidden', 'headmaster', $USER->id);
        }

        $this->add_action_buttons(true);
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
    	$mform->addElement('hidden', 'edit', 'true');
    	$mform->addElement('hidden', 'schoolid', $data->id);
    }
}

?>
