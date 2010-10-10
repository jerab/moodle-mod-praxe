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
$strvisi = get_string('visible');
$strisced = get_string('iscedlevel', 'praxe');
$stryear = get_string('year', 'praxe');
$strterm = get_string('term', 'praxe');
$strdate = get_string('dateofpraxe', 'praxe');
$strpart = get_string('participants');

/*if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
    
} else if ($course->format == 'topics') {*/
    $table->head  = array ($strname, $strdesc, $strisced, $stryear, $strterm, $strdate, $strpart, $strvisi);
    $table->align = array ('center', 'left', 'center', 'center', 'center', 'center', 'center', 'center');
/*} else {
   	$table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');	
}*/
    
foreach ($praxes as $praxe) {
    if (!$praxe->visible) {
        //Show dimmed if the mod is hidden
        $link = '<a class="dimmed" href="view.php?id='.$praxe->coursemodule.'">'.format_string($praxe->name).'</a>';
        $visi = get_string('no');
    } else {
        //Show normal if the mod is visible
        $link = '<a href="view.php?id='.$praxe->coursemodule.'">'.format_string($praxe->name).'</a>';
        $visi = get_string('yes');
    }
    $isced = praxe_get_isced_text($praxe->isced);    
    
    $part = '0';
    if(!empty($praxe->groupingid)) {    	
    	$groups = get_records_sql("SELECT g.* from {$CFG->prefix}groupings_groups as gg 
    								left join {$CFG->prefix}groups as g on(groupid = g.id) 
    								where groupingid = $praxe->groupingid");
    	$aPart = array();
    	foreach($groups as $group) {
    		$participants = get_group_students($group->id);
    		$aPart[] = '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'.$group->name.'('.count($participants).')</a>';
    	}
    	$part = implode(',',$aPart);    	
    }
    
    $date = userdate($praxe->datestart, get_string('strftimedateshort'))." - ".userdate($praxe->dateend, get_string('strftimedateshort'));
    //if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($link, $praxe->description, $isced, $praxe->year, $praxe->term, $date, $part, $visi);
    /*} else {
        $table->data[] = array ($link);
    }*/
}

print_heading($strpraxes);
print_table($table);

/// Finish the page

print_footer($course);

?>
