<?php  //$Id: settings.php,v 1.1.2.2 2010/05/20 17:38:41 jerab Exp $

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/praxe/lib.php');

$praxe_subjects = $DB->get_records('praxe_studyfields', array(), '', 'shortcut, name,id');
if(is_array($praxe_subjects) && count($praxe_subjects)) {
    $settings->add(new admin_setting_heading('praxe_current_studyfields', get_string('current_studyfields', 'praxe'), get_string('current_studyfields_descript', 'praxe')));
    foreach ($praxe_subjects as $subj) {
        //var_dump($subj);
        $field = "<strong>{$subj->shortcut}</strong> - {$subj->name}";
    	$settings->add(new admin_setting_configcheckbox('praxe_current_studyfield_'.$subj->id, $field, null , 'yes', 'yes', 'no'));
    }
}

$settings->add(new admin_setting_heading('praxe_new_studyfield', get_string('addstudyfield', 'praxe'),'blablabla'));

$settings->add(new admin_setting_configtext('praxe_studyfield', get_string('studyfield', 'praxe'),
                   get_string('configstudyfielddescription', 'praxe'), '', PARAM_TEXT));
$settings->add(new admin_setting_configtext('praxe_studyfield_shortcut', get_string('studyfield_shortcut', 'praxe'),
                   get_string('configstudyfielddescription_shortcut', 'praxe'), '', PARAM_TEXT));

/*$options = array();
$options['IT'] = get_string('IT', 'praxe');
$options['TIV']   = get_string('TIV', 'praxe');*/

/*
$options = array();
$options[PRAXE_ISCED_2] = PRAXE_ISCED_2_TEXT;
$options[PRAXE_ISCED_3] = PRAXE_ISCED_3_TEXT;
$settings->add(new admin_setting_configselect('praxe_isced', get_string('iscedlevel', 'praxe'),
                   get_string('configisceddescription', 'praxe'), PRAXE_ISCED_2, $options));
*/
?>