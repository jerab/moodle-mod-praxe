<?php // $Id: index.php,v 1.7.2.3 2009/08/31 22:00:00 mudrd8mz Exp $

/**
 * This page lists all the instances of praxe in a particular course
 *
 * @author  Your Name <your@email.address>
 * @version $Id: index.php,v 1.7.2.3 2009/08/31 22:00:00 mudrd8mz Exp $
 * @package mod/praxe
 */

/// Replace newmodule with the name of your module and remove this line

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = required_param('id', PARAM_INT);   // course

if (! $course = get_record('course', 'id', $id)) {
    error('Course ID is incorrect');
}

require_course_login($course);

add_to_log($course->id, 'praxe', 'view all', "index.php?id=$course->id", '');


/// Get all required stringspraxe

$strpraxes = get_string('modulenameplural', 'praxe');
$strpraxe  = get_string('modulename', 'praxe');


/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strpraxes, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple($strpraxes, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

if (! $praxes = get_all_instances_in_course('praxe', $course)) {
    notice('There are no instances of praxe', "../../course/view.php?id=$course->id");
    die;
}

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strdesc = get_string('description');
$strisced = get_string('iscedlevel', 'praxe');
$stryear = get_string('year', 'praxe');
$strterm = get_string('term', 'praxe');
$strdate = get_string('dateofpraxe', 'praxe');
$strnumofrec = get_string('numberofrecords', 'praxe');
$strpart = get_string('participants')." (".get_string('groups').")";

/*if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
    
} else if ($course->format == 'topics') {*/
    $table->head  = array ($strname, $strdesc, $strisced, $stryear, $strterm, $strdate, $strnumofrec, $strpart);
    $table->align = array ('center', 'left', 'center', 'center', 'center', 'center', 'center', 'center');
/*} else {
   	$table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');	
}*/   
foreach ($praxes as $praxe) {
	if(! $records = get_records('praxe_records','praxe',$praxe->id)) {
		$numofrecords = 0;
	}else {
		$numofrecords = count($records);
	}    
	if (!$praxe->visible) {
        //Show dimmed if the mod is hidden
        $link = '<a class="dimmed" href="view.php?id='.$praxe->coursemodule.'">'.format_string($praxe->name).'</a>';        
    } else {
        //Show normal if the mod is visible
        $link = '<a href="view.php?id='.$praxe->coursemodule.'">'.format_string($praxe->name).'</a>';        
    }
    $isced = praxe_get_isced_text($praxe->isced);    
    
    $part = '0';
    if(!empty($praxe->groupingid)) {    	
    	$groups = get_records_sql("SELECT g.* 
    								FROM {$CFG->prefix}groupings_groups gg 
    								LEFT JOIN {$CFG->prefix}groups g on(groupid = g.id) 
    								WHERE groupingid = $praxe->groupingid");
    	$aPart = array();
    	foreach($groups as $group) {    		    		
    		$aPart[] = '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'.$group->name.'</a>';
    	}
    	$part = implode(', ',$aPart);    	
    }
    
    $date = userdate($praxe->datestart, get_string('strftimedateshort'))." - ".userdate($praxe->dateend, get_string('strftimedateshort'));
    $table->data[] = array ($link, s($praxe->description), $isced, s($praxe->year), praxe_get_term_text($praxe->term), $date, $numofrecords, $part);
}

print_heading($strpraxes);
print_table($table);

/// Finish the page

print_footer($course);

?>
