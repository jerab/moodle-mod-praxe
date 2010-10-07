<?php

$mod_praxe_capabilities = array(
	
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
