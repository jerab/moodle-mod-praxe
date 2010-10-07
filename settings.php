<?php  //$Id: settings.php,v 1.1.2.2 2010/05/20 17:38:41 jerab Exp $

require_once($CFG->dirroot.'/mod/praxe/lib.php');
/*
if($praxe_subjects = get_records('praxe_studyfield', '', '', 'shortcut')) {                      
	$options = array();
    foreach ($praxe_subjects as $subj) {
    	$options[$subj->shortcut] = get_string($subj->name, 'praxe');
    	if(!isset($subj_default)){
    		$subj_default = $subj->shortcut;
    	}
    }
    
    $settings->add(new admin_setting_configselect('praxe_studyfield', get_string('studyfield', 'praxe'),
                   get_string('configstudyfielddescription', 'praxe'), $subj_default, $options));
}
*/
/*
$options = array();
$options['IT'] = get_string('IT', 'praxe');
$options['TIV']   = get_string('TIV', 'praxe');
*/
/*
$options = array();
$options[PRAXE_ISCED_2] = PRAXE_ISCED_2_TEXT;
$options[PRAXE_ISCED_3] = PRAXE_ISCED_3_TEXT;
$settings->add(new admin_setting_configselect('praxe_isced', get_string('iscedlevel', 'praxe'),
                   get_string('configisceddescription', 'praxe'), PRAXE_ISCED_2, $options));
*/                 


/*$settings->add(new admin_setting_configtext('chat_refresh_userlist', get_string('refreshuserlist', 'chat'),
                   get_string('configrefreshuserlist', 'chat'), 10, PARAM_INT));

$settings->add(new admin_setting_configtext('chat_old_ping', get_string('oldping', 'chat'),
                   get_string('configoldping', 'chat'), 35, PARAM_INT));


$settings->add(new admin_setting_heading('chat_normal_heading', get_string('methodnormal', 'chat'),
                   get_string('explainmethodnormal', 'chat')));

$settings->add(new admin_setting_configtext('chat_refresh_room', get_string('refreshroom', 'chat'),
                   get_string('configrefreshroom', 'chat'), 5, PARAM_INT));

$options = array();
$options['jsupdate']  = get_string('normalkeepalive', 'chat');
$options['jsupdated'] = get_string('normalstream', 'chat');
$settings->add(new admin_setting_configselect('chat_normal_updatemode', get_string('updatemethod', 'chat'),
                   get_string('confignormalupdatemode', 'chat'), 'jsupdate', $options));


$settings->add(new admin_setting_heading('chat_daemon_heading', get_string('methoddaemon', 'chat'),
                   get_string('explainmethoddaemon', 'chat')));

$settings->add(new admin_setting_configtext('chat_serverhost', get_string('serverhost', 'chat'),
                   get_string('configserverhost', 'chat'), $_SERVER['HTTP_HOST']));

$settings->add(new admin_setting_configtext('chat_serverip', get_string('serverip', 'chat'),
                   get_string('configserverip', 'chat'), '127.0.0.1'));

$settings->add(new admin_setting_configtext('chat_serverport', get_string('serverport', 'chat'),
                   get_string('configserverport', 'chat'), 9111, PARAM_INT));

$settings->add(new admin_setting_configtext('chat_servermax', get_string('servermax', 'chat'),
                   get_string('configservermax', 'chat'), 100, PARAM_INT));
*/
?>
