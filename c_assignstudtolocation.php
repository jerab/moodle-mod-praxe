<?php

/**
 *
 */

require_once ('c_actionform.php');

class praxe_assignstudtolocation extends praxe_actionform {
	private $locationid;
	function praxe_assignstudtolocation($locationid) {
		$this->locationid = $locationid;
		parent::praxe_actionform(get_class($this));
	}
    public function definition() {
        $err = false;
    	global $USER, $cm, $course, $DB, $CFG;
        $location = praxe_get_location($this->locationid);
        $users = praxe_get_student_participants($cm->id);

        /// link back to list of locations - we will use it later
        $back = html_writer::link(new moodle_url(praxe_get_base_url(array('mode'=>'locations','schoolid'=>0))),get_string('back'));

        //if(praxe_get_use_status_of_location($location->id))
        //var_dump(praxe_get_use_status_of_location($location->id));
        //var_dump($location);

 		if(!empty($location)) {
 			$this->content_before_form .= html_writer::tag('p',get_string('assignstudtolocation_text', 'praxe'));
    		$table = new html_table();
    		$table->head[] = get_string('location','praxe');

    		$info = $location->name. ' - ' .praxe_get_term_text($location->term). ' ' .$location->year;
    		if(!is_null($location->teacherid)) {
    			$info .= ' - '.s($location->teacher_name). ' ' .s($location->teacher_lastname);
    		}
    		$info = html_writer::tag('strong',$info);

    		// location is occupied
    		$sql = "SELECT rec.* FROM {praxe_records} rec WHERE rec.location = ? AND rec.status != ?";
    		$params = array($this->locationid, PRAXE_STATUS_REFUSED);
    		if($ret = $DB->get_record_sql($sql, $params)) {
    			$table->data[] = array($info);
    			$table->data[] = array(get_string('location_is_not_available', 'praxe'));
    			$table->data[] = array($back);
    			$table->align = array('center');
    			$this->content .= html_writer::table($table,true);

    		/// location and users are available
    		} else if(is_array($users) && count($users)) {
				$table->head[] = get_string('student','praxe');

				$form = '<form class="mform" action="'.praxe_get_base_url(array('locationid'=>$this->locationid),'assigntolocation').'" method="post">';
				$form .= '<input type="hidden" name="post_form" value="assigntolocation" />';
				$form .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
				$form .= '<input type="hidden" name="location" value="'.$this->locationid.'" />';
				$table->align = array('left','center');

				$select = '<select name="student">';
				$select .= '<option value="null">'.get_string('select_student','praxe').'</option>';
				foreach($users as $us) {
					$select .= '<option value="'.$us->id.'">'.s($us->firstname.' '.$us->lastname).'</option>';
	    		}
	    		$select .= '</select>';

	    		$table->data[] = array($info, $select);

	    		/// option to send emails
	    		require_once($CFG->dirroot . "/lib/pear/HTML/QuickForm/checkbox.php");

	    		$cel =  new html_table_cell();
	    		$cel->colspan = 2;
	    		$check = new HTML_QuickForm_checkbox('sendemailtoextteacher','',get_string('sendinfotoextteacher','praxe'));
	    		$cel->text = $check->toHtml();
	    		$table->data[] = new html_table_row(array($cel));

	    		$cel =  new html_table_cell();
	    		$cel->colspan = 2;
	    		$check = new HTML_QuickForm_checkbox('sendemailtostudent','',get_string('sendinfotostudent','praxe'),'checked');
	    		$cel->text = $check->toHtml();
	    		$table->data[] = new html_table_row(array($cel));

	    		/// action buttons
	    		$cel =  new html_table_cell();
				$cel->colspan = 2;
				$cel->style = 'text-align:center';
				$cel->text = '<div class="fitem center" style="margin: 10px 0;">'
							.'<input type="submit" id="id_submitbutton" value="Submit" name="submitbutton" /> '
							.'<input type="submit" id="id_cancel" onclick="skipClientValidation = true; return true;" value="Cancel" name="cancel" />'
							.'</div>';
				$sub = new html_table_row();
				$sub->cells[] = $cel;
				$table->data[] = $sub;
				$form .= html_writer::table($table,true);
				$form .= '</form>';
				$this->content .= "<div>$form</div>";
			/// no students to be assigned to location
    		}else {
				$table->data[] = array(get_string('nostudentsavailable','praxe'));
				$table->data[] = array($back);
				$table->align = array('center');
				$this->content .= html_writer::table($table,true);
			}
    	}else{
    		$this->content_before_form .= html_writer::tag('strong',get_string('notallowedaction', 'praxe'));
    		$this->content .= $back;
    	}
    }
}

?>
