<?php
/**
 * Library of functions and constants for module newmodule
 * This file should have two well differenced parts:
 *   - All the core Moodle functions, neeeded to allow
 *     the module to work integrated in Moodle.
 *   - All the newmodule specific functions, needed
 *     to implement all the module logic. Please, note
 *     that, if the module become complex and this lib
 *     grows a lot, it's HIGHLY recommended to move all
 *     these module specific functions to a new php file,
 *     called "locallib.php" (see forum, quiz...). This will
 *     help to save some memory when Moodle is performing
 *     actions across all modules.
 */

define('PRAXE_ISCED_0',0);
define('PRAXE_ISCED_2',2);
define('PRAXE_ISCED_3',3);
define('PRAXE_ISCED_0_TEXT',get_string('all'));
define('PRAXE_ISCED_2_TEXT',get_string('levelofeducation-2nd','praxe'));
define('PRAXE_ISCED_3_TEXT',get_string('levelofeducation-3rd','praxe'));

define('PRAXE_TERM_WS',1);
define('PRAXE_TERM_SS',2);
define('PRAXE_TERM_WS_TEXT',get_string('winterterm','praxe'));
define('PRAXE_TERM_SS_TEXT',get_string('summerterm','praxe'));


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $praxe An object from the form in mod_form.php
 * @return int The id of the newly inserted newmodule record
 */
function praxe_add_instance($praxe) {
    global $DB;	    
	$praxe->timecreated = time();    
    return $DB->insert_record('praxe', $praxe);
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $praxe An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function praxe_update_instance($praxe) {
    global $DB;

    $praxe->timemodified = time();
    $praxe->id = $praxe->instance;

    return $DB->update_record('praxe', $praxe);
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function praxe_delete_instance($id) {
    global $DB;

    if (!$praxe = $DB->get_record('praxe', array('id' => $id))) {
        return false;
    }

    if($records = $DB->get_records('praxe_records',array('praxe' => $praxe->id))) {
    	foreach($records as $rec) {
	    	if($sches = $DB->get_records('praxe_schedules',array('record' => $rec->id))) {
	    		foreach($sches as $sch) {
			    	$DB->delete_records('praxe_schedules_inspections', array('schedule' => $sch->id));		    	
	    			$DB->delete_records('praxe_schedules_notices', array('schedule' => $sch->id));
	    		}
	    		$DB->delete_records('praxe_schedules', array('record' => $rec->id));
	    	}	    	
    	}
    	$DB->delete_records('praxe_records', array('praxe' => $praxe->id));
    }	
    $DB->delete_records('praxe', array('id' => $praxe->id));
    return true;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function praxe_user_outline($course, $user, $mod, $praxe) {
    return null;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function praxe_user_complete($course, $user, $mod, $praxe) {
    return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in praxe activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function praxe_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function praxe_cron() {
    return true;
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of praxe. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $praxeid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function praxe_get_participants($praxeid) {
	//TODO
    return false;
}


/**
 * This function returns if a scale is being used by one newmodule
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $praxeid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function praxe_scale_used($praxeid, $scaleid) {
    return false;
}


/**
 * Checks if scale is being used by any instance of praxe.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any praxe
 */
function praxe_scale_used_anywhere($scaleid) {
    /*global $DB;
    if ($scaleid and $DB->record_exists('praxe', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }*/
    return false;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other praxe functions go here.  Each of them must have a name that
/// starts with praxe_
/// Remember (see note in first lines) that, if this section grows, it's HIGHLY
/// recommended to move all funcions below to a new "localib.php" file.


/**
 * Check if exists any instance of praxe and return its id or return false. This function is also called in lib.php.
 *
 * @param $course int - course id
 * @param $isced int - isced level
 * @param $studyfield int - study field id which is generated in praxe_studyfield table and included in praxe table
 * @return id (int) of existing instance, otherwise false
 */
function praxe_get_instance($course, $isced='', $studyfield='') {
	global $DB;

    $isced_f = ($isced == '')? '' : 'isced';
	$studyfield_f = ($studyfield == '')? '' : 'studyfield';
	if( $inst = $DB->get_record('praxe', array('course' => $course, $isced_f => $isced, $studyfield_f => $studyfield))) {
			return $inst->id;
	}

	return false;
}

/**
 * Indicates API features that the praxe supports.
 *
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function praxe_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}
?>
