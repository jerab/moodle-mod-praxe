<?php //$Id: mod_form.php,v 1.2.2.3 2009/03/19 12:23:11 mudrd8mz Exp $

/**
 * This file defines the main newmodule configuration form
 * It uses the standard core Moodle (>1.8) formslib. For
 * more info about them, please visit:
 *
 * http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * The form must provide support for, at least these fields:
 *   - name: text element of 64cc max
 *
 * Also, it's usual to use these fields:
 *   - intro: one htmlarea element to describe the activity
 *            (will be showed in the list of activities of
 *             newmodule type (index.php) and in the header
 *             of the newmodule main page (view.php).
 *   - introformat: The format used to write the contents
 *             of the intro field. It automatically defaults
 *             to HTML when the htmleditor is used and can be
 *             manually selected if the htmleditor is not used
 *             (standard formats are: MOODLE, HTML, PLAIN, MARKDOWN)
 *             See lib/weblib.php Constants and the format_text()
 *             function for more info
 */

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_praxe_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE;
        $mform =& $this->_form;

//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
		
        $mform->addElement('textarea', 'description', get_string('description'), array('rows'=>'5', 'cols'=>'48'));
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', null, 'required', null, 'client');
        $mform->addRule('description', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
    /*
    /// Adding the required "intro" field to hold the description of the instance
        $mform->addElement('htmleditor', 'intro', get_string('praxeintro', 'praxe'));
        $mform->setType('intro', PARAM_RAW);
        $mform->addRule('intro', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('intro', array('writing', 'richtext'), false, 'editorhelpbutton');

    /// Adding "introformat" field
        $mform->addElement('format', 'introformat', get_string('format'));
	*/
//-------------------------------------------------------------------------------
    /// Adding the rest of praxe settings, spreeading all them into this fieldset    
        //$mform->addElement('static', 'label1', 'praxesetting1', 'Your praxe fields go here. Replace me! - mod_form.php');
        $mform->addElement('header', 'praxefieldset', get_string('praxefieldset', 'praxe'));
        
	///	Adding the "study field" select field to choose subject of current course
        if($options = $this->getStudyFields()) {
			$mform->addElement('select', 'studyfield', get_string('studyfield', 'praxe'), $options);
			$mform->addRule('studyfield', null, 'required', null, 'client');			
		}
	
	///	Adding the "isced" select field to choose the isced level for current course		
		$options = array(	PRAXE_ISCED_2 => PRAXE_ISCED_2_TEXT,
							PRAXE_ISCED_3 => PRAXE_ISCED_3_TEXT);
		$mform->addElement('select', 'isced', get_string('iscedlevel', 'praxe'), $options);
		$mform->addRule('isced', null, 'required', null, 'client');
		
	///	Adding the "year" select field to choose the calendar year for current course		
		$actualyear = (int)date('Y',mktime());
		$options = array(	$actualyear => $actualyear,
							($actualyear+1) => ($actualyear+1),
							($actualyear+2) => ($actualyear+2));
		$mform->addElement('select', 'year', get_string('year', 'praxe'), $options);
		$mform->addRule('year', null, 'required', null, 'client');
		
	///	Adding the "term" select field to choose the summer term/winter term for current course		
		$options = array(	PRAXE_TERM_WS => PRAXE_TERM_WS_TEXT,
							PRAXE_TERM_SS => PRAXE_TERM_SS_TEXT);
		$mform->addElement('select', 'term', get_string('term', 'praxe'), $options);
		$mform->addRule('term', null, 'required', null, 'client');
		
		$mform->addElement('date_selector', 'datestart', get_string('praxe_start','praxe'));
		$mform->addElement('date_selector', 'dateend', get_string('praxe_end','praxe'));
		$mform->addRule('datestart', null, 'required', null, 'client');
		$mform->addRule('dateend', null, 'required', null, 'client');                
//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $features->gradecat = false;
        $features->idnumber = false;
        $this->standard_coursemodule_elements($features);        
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

    }
    
    public function getStudyFields() {
	    if($praxe_subjects = get_records('praxe_studyfields', '', '', 'shortcut')) {                      
			$options = array();
		    foreach ($praxe_subjects as $subj) {
		    	$options[$subj->id] = $subj->shortcut;//." - ".get_string($subj->name, 'praxe');		    	
		    }
		    return $options;		    
		}
		return false;
		    	
    }    
}

?>
