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

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

add_to_log($course->id, 'praxe', 'view all', "index.php?id=$course->id", '');

$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/praxe/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);
if ($cm = get_coursemodule_from_id('praxe', $id)) {
    $PAGE->set_headingmenu(navmenu($course, $cm));
}


echo $OUTPUT->header();
if (! $praxes = get_all_instances_in_course('praxe', $course)) {
    notice(get_string('nopraxes', 'praxe'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

/// Print the list of instances (your module will probably extend this)
$timenow  = time();
$table = new html_table();
$table->head  = array (get_string('name'),
                        get_string('description'),
                        get_string('iscedlevel', 'praxe'),
                        get_string('year', 'praxe'),
                        get_string('term', 'praxe'),
                        get_string('dateofpraxe', 'praxe'),
                        get_string('numberofrecords', 'praxe'),
                        get_string('participants')." (".get_string('groups').")"
                        );
$table->align = array ('center', 'left', 'center', 'center', 'center', 'center', 'center', 'center');

foreach ($praxes as $praxe) {
    $records = $DB->get_records('praxe_records', array('praxe' => $praxe->id));
    $numofrecords = count($records);

    if (!$praxe->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/praxe/view.php', array('id' => $praxe->coursemodule)),
            format_string($praxe->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/praxe/view.php', array('id' => $praxe->coursemodule)),
            format_string($praxe->name, true));
    }
    $part = '0';
    if(!empty($praxe->groupingid)) {
        $groups = $DB->get_records_sql("SELECT g.*
    									FROM {groupings_groups} gg
    									LEFT JOIN {groups} g on(groupid = g.id)
    									WHERE groupingid = ?", array($praxe->groupingid));
        $aPart = array();
    	foreach($groups as $group) {
    		$aPart[] = '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$course->id.'&amp;group='.$group->id.'">'.$group->name.'</a>';
    	}
    	$part = implode(', ',$aPart);
    }
    $date = userdate($praxe->datestart, get_string('strftimedateshort'))." - ".userdate($praxe->dateend, get_string('strftimedateshort'));
    $table->data[] = array ($link, s($praxe->description), praxe_get_isced_text($praxe->isced), s($praxe->year), praxe_get_term_text($praxe->term), $date, $numofrecords, $part);
    /*
    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($praxe->section, $link);
    } else {
        $table->data[] = array($link);
    }*/
}

echo $OUTPUT->heading(get_string('modulenameplural', 'praxe'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
?>
