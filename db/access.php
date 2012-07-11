<?php
/**
 * Capability definitions for the newmodule module
 *
 * The capabilities are loaded into the database table when the module is
 * installed or updated. Whenever the capability definitions are updated,
 * the module version number should be bumped up.
 *
 * The system has four possible values for a capability:
 * CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
 *
 * It is important that capability names are unique. The naming convention
 * for capabilities that are specific to modules and blocks is as follows:
 *   [mod/block]/<plugin_name>:<capabilityname>
 *
 * component_name should be the same as the directory name of the mod or block.
 *
 * Core moodle capabilities are defined thus:
 *    moodle/<capabilityclass>:<capabilityname>
 *
 * Examples: mod/forum:viewpost
 *           block/recent_activity:view
 *           moodle/site:deleteuser
 *
 * The variable name for the capability definitions array is $capabilities
 *
 * @package    mod
 * @subpackage praxe
 * @copyright  2012 Tomas Jerabek <t.jerab@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$capabilities = array(	
	'mod/praxe:addnoticetostudentschedule' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),	
	'mod/praxe:addschool' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:assignselftoinspection' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:addstudentschedule' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),    	
	'mod/praxe:assignteachertoanyschool' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),    
    'mod/praxe:assignteachertoownschool' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:assignteachertolocation' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:beexternalteacher' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
    ),    
    'mod/praxe:beheadmaster' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
    ),    
    
    'mod/praxe:confirmlocation' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),        
	'mod/praxe:confirmownlocation' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),    
    'mod/praxe:createownlocation' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    
    'mod/praxe:deleteschool' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    
    'mod/praxe:editanylocation' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:editanyrecord' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),    
    'mod/praxe:editanyschool' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),        
    'mod/praxe:editownlocation' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:editownrecord' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),    
	'mod/praxe:editownschool' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:editstudentschedule' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    
    /// for course creators/editing teachers ///
	'mod/praxe:manageallincourse' => array(
        'riskbitmask' => RISK_PERSONAL,
		'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
    ),
    
    'mod/praxe:viewanyrecorddetail' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,        
    ),    
    'mod/praxe:viewownschools' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
    ),    
    'mod/praxe:viewrecordstoanylocation' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,        
    ),
    'mod/praxe:viewrecordstoownlocation' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,        
    )
    
);

?>
