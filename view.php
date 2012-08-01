<?php
/**
 * This page prints a particular instance of praxe
 *
 * @author  Your Name <your@email.address>
 * @version
 * @package mod/praxe
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // praxe instance ID

$mode = optional_param('mode', '', PARAM_ALPHA);           // value depends on $tabview(resp. user role)

if ($id) {
    if (! $cm = get_coursemodule_from_id('praxe', $id)) {
        print_error('Course Module ID was incorrect');
    }

    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('Course is misconfigured');
    }

    if (! $praxe = $DB->get_record('praxe', array('id' => $cm->instance))) {
        print_error('Course module is incorrect');
    }

} else if ($a) {
    if (! $praxe = $DB->get_record('praxe', array('id' => $a))) {
        print_error('Course module is incorrect');
    }
    if (! $course = $DB->get_record('course', array('id' => $praxe->course))) {
        print_error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('praxe', $praxe->id, $course->id)) {
        print_error('Course Module ID was incorrect');
    }

} else {
    echo('you must specify ID of course');
}

require_login($course, true, $cm);

/// Print the page header
$strpraxes = get_string('modulenameplural', 'praxe');
$strpraxe  = get_string('modulename', 'praxe');

$PAGE->set_url('/mod/praxe/view.php', array('id'=>$cm->id));
$PAGE->set_title(format_string($praxe->name));
$PAGE->set_heading(format_string($course->fullname));

$context = context_module::instance($cm->id);
/// user record of praxe - for users as student in praxe_records. set info of praxe in this course ///
$oPraxeRecord = new praxe_record($USER->id);
$praxeaction = optional_param('praxeaction', null, PARAM_ALPHAEXT);
/// extra praxe action ///
if(!is_null($praxeaction)) {
	require_once($CFG->dirroot . '/mod/praxe/praxeaction.php');
}

/// rights to all actions in this module ///
if(has_capability('mod/praxe:manageallincourse',$context) || has_capability('mod/praxe:assignselftoinspection',$context)) {
	$viewrole = 'EDITTEACHER';
	$role_title = get_string('teacher','praxe');
/// rights to create or manage own record of praxe - for students ///
}else if(has_capability('mod/praxe:editownrecord',$context)) {
	$viewrole = 'STUDENT';
	$role_title = get_string('student','praxe');
}else {
    foreach(get_roles_used_in_context($context) as $role) {
        if(user_has_role_assignment($USER->id, $role->id, $context->id)) {
			if($role->shortname == 'extheadmaster') {
				$viewrole = 'HEADM';
				$role_title = get_string('headmaster','praxe');
			}else if($role->shortname == 'extteacher') {
				$viewrole = 'EXTTEACHER';
				$role_title = get_string('extteacher','praxe');
			}
            break;
        }
    }
}

$post_form = optional_param('post_form', null, PARAM_ALPHAEXT);
/// post data sent ///
if(!is_null($post_form)) {
	require_once($CFG->dirroot . '/mod/praxe/post.php');
}

echo $OUTPUT->header();
/// Print the main part of the page
echo "<script type=\"text/javascript\" src=\"praxe.js\"></script>";

if(isset($viewrole) && is_null($praxeaction)) {
	/// set type of tabs to view ///
	$tabview = constant('PRAXE_TAB_VIEW_'.$viewrole);
	if(false === ($tab = array_search($mode,$tab_modes[strtolower($viewrole)]))) {
		if(count($tab_modes[strtolower($viewrole)])) {
			$tab = 0;
			$mode = $tab_modes[strtolower($viewrole)][0];	/// string value for/from url
		}
	}
	if($tab !== false) {
		require_once('tabs.php');
	}
	require_once($CFG->dirroot . '/mod/praxe/view_'.strtolower($viewrole).'.php');
	$class = 'praxe_view_'.strtolower($viewrole);
	$praxe_view_role = new $class;
	/// display inner content of tab///
	$praxe_view_role->display_content();
	if($tab !== false) {
		praxe_print_tab_footer();
	}
}
/// Finish the page
echo $OUTPUT->footer($course);

?>
