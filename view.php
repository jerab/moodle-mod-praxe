<?php  // $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of praxe
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
 * @package mod/praxe
 */

/// (Replace newmodule with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // praxe instance ID

$mode = optional_param('mode', '', PARAM_ALPHA);           // value depends on $tabview(resp. user role)

if ($id) {
    if (! $cm = get_coursemodule_from_id('praxe', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $praxe = get_record('praxe', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $praxe = get_record('praxe', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $praxe->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('praxe', $praxe->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "praxe", "view", "view.php?id=$cm->id", "$praxe->id");

/// Print the page header
$strpraxes = get_string('modulenameplural', 'praxe');
$strpraxe  = get_string('modulename', 'praxe');

$navlinks = array();
$navlinks[] = array('name' => $strpraxes, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($praxe->name), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);

print_header_simple(format_string($praxe->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, $strpraxe), navmenu($course, $cm));

/// Print the main part of the page
echo "<script type=\"text/javascript\" src=\"praxe.js\"></script>";


$context = get_context_instance(CONTEXT_MODULE, $cm->id);
//print_object($context);
/// user record of praxe - for users as student in praxe_records. set info of praxe in this course ///
$oPraxeRecord = new praxe_record($USER->id);
	
$praxeaction = optional_param('praxeaction', null, PARAM_ALPHAEXT);
/// extra praxe action ///
if(!is_null($praxeaction)) {
	require_once($CFG->dirroot . '/mod/praxe/praxeaction.php');
}

$post_form = optional_param('post_form', null, PARAM_ALPHAEXT);
/// post data sent ///
if(!is_null($post_form)) {
	require_once($CFG->dirroot . '/mod/praxe/post.php');
} 
 
if(has_capability('mod/praxe:manageallincourse',$context)) {
	$viewrole = 'EDITTEACHER';
	$role_title = get_string('teacher','praxe');		
/// rihts to add and edit school - for headmasters ///
}else if(has_capability('mod/praxe:assignselftoinspection',$context)) {
	$viewrole = 'EDITTEACHER';
	$role_title = get_string('teacher','praxe');		
/// rights to create or manage own record of praxe - for students ///
}else if(has_capability('mod/praxe:editownrecord',$context)) {
	$viewrole = 'STUDENT';
	$role_title = get_string('student','praxe');		
/// rihts to add and edit school - for headmasters ///
}else if(has_capability('mod/praxe:addschool',$context)) {
	$viewrole = 'HEADM';
	$role_title = get_string('headmaster','praxe');
/// rihts to add and edit own location - for external teachers ///
}else if(has_capability('mod/praxe:editownlocation',$context)) {
	$viewrole = 'EXTTEACHER';
	$role_title = get_string('extteacher','praxe');	
}


if(isset($viewrole) && is_null($praxeaction)) {
	//echo "<h2>$role_title</h2>";
	/// set type of tabs to view ///
	eval('$tabview = PRAXE_TAB_VIEW_'.$viewrole.';');
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
print_footer($course);

?>
