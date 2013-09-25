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

$PAGE->set_url('/mod/praxe/assigntolocation.php', array('id'=>$cm->id));
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

require_capability('mod/praxe:assignstudenttolocation', $context);

$locationid = required_param('locationid', PARAM_INT);

$role_title = get_string('teacher','praxe');

$post_form = optional_param('post_form', null, PARAM_ALPHAEXT);

/// post data sent ///
if(!is_null($post_form)) {
	require_once($CFG->dirroot . '/mod/praxe/post.php');
	echo $OUTPUT->header();
/// form to assign users to location
}else {
	echo $OUTPUT->header();
	require_once($CFG->dirroot . '/mod/praxe/c_praxe_view.php');
	$view = new praxe_view();
	require_once($CFG->dirroot . '/mod/praxe/c_assignstudtolocation.php');
	$view->form = new praxe_assignstudtolocation($locationid);
	$view->display_content();
}

/// Print the main part of the page
echo "<script type=\"text/javascript\" src=\"praxe.js\"></script>";

/// Finish the page
echo $OUTPUT->footer($course);
?>