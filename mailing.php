<?php
class praxe_mailing {
	private $course;
	public $error;
	public $html = '';
	public $plain = '';
	public $subject = '';
	private $linkstofoot = array();
	function praxe_mailing() {
		global $course;
		$this->course =& $course;
	}
	public function setHtml($content) {
		$this->html = format_text($content);
	}
	public function setSubject($content) {
		$this->subject = s($content);
	}
	public function setPlain($content) {
		$this->plain = format_text($content);
	}
	public function addLinkToFoot($link, $text) {
		$this->linkstofoot[] = (object)array('link'=>$link, 'text'=>$text);
	}
	/**
	 *
	 * @param stdClass $user  A {@link $USER} object
	 * @param stdClass $user  A {@link $USER} object
	 */
	public function mailToUser($userto, $userfrom) {
	    $html = $this->makeMailHtml($this->html, $userto);
		$plain = $this->makeMailText($this->plain, $userto);
		$site = get_site();
		$subject = format_string($site->shortname).": ".$this->subject;
		$result = email_to_user($userto, $userfrom, $subject, $plain, $html);
		if($result === true) {
			return true;
		}
		//$this->error .= "<div>".var_dump($result, true)."</div>";
		return false;
	}
	/**
	 * Builds and returns the body of the email notification in plain text.
	 *
	 * @param object $post
	 * @param object $userto
	 * @return string The email body in plain text format.
	 */
	public function makeMailText($post, $userto) {
	    global $CFG, $cm;
		$praxe = praxe_record::getData();
	    if (!isset($userto->viewfullnames[$praxe->id])) {
	        if (!$cm = get_coursemodule_from_instance('praxe', $praxe->id, $this->course->id)) {
	            print_error('Course Module ID was incorrect');
	        }
	        $modcontext = context_module::instance($cm->id);
	        $viewfullnames = has_capability('moodle/site:viewfullnames', $modcontext, $userto->id);
	    } else {
	        $viewfullnames = $userto->viewfullnames[$praxe->id];
	    }
	    //$by = New stdClass;
	    //$by->name = fullname($userfrom, $viewfullnames);
	    //$by->date = userdate($post->modified, "", $userto->timezone);
	    //$strbynameondate = get_string('bynameondate', 'forum', $by);
	    $strpraxes = get_string('modulenameplural', 'praxe');
	    $posttext = '';
	    $posttext  = $this->course->shortname." -> ".$strpraxes." -> ".format_string($praxe->name,true);
	    $posttext .= "\n---------------------------------------------------------------------\n";
	    $posttext .= format_string($this->subject,true);
	    //$posttext .= "\n".$strbynameondate."\n";
	    $posttext .= "\n---------------------------------------------------------------------\n";
	    $posttext .= format_text_email(trusttext_strip($post), FORMAT_PLAIN);
	    $posttext .= "\n\n---------------------------------------------------------------------\n";
	    $site = get_site();
	    foreach($this->linkstofoot as $link) {
	    	$posttext .= $link->text.": ".$link->link."\t";
	    	//$posttext .= get_string('confirmorrefusestudent','praxe').": ".$CFG->wwwroot.'/course/view.php?id='.$cm->id."\n\n";
	    }
	    $posttext .= "\n\n".$site->shortname.": ".$CFG->wwwroot."\n";
	    return $posttext;
	}
	/**
	 * Builds and returns the body of the email notification in html format.
	 *
	 * @param string $post
	 * @param object $userto
	 * @param array $linkstofoot array of objects containing: link, text
	 * @return string The email text in HTML format
	 */
	private function makeMailHtml($post, $userto) {
	    global $CFG, $cm, $OUTPUT;
	    if ($userto->mailformat != 1) {  // Needs to be HTML
	        return '';
	    }
		$praxe = praxe_record::getData();
	    $site = get_site();
	    $posthtml = '<head>';
	    /*foreach ($CFG->stylesheets as $stylesheet) {
		    $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
        }*/
	    $posthtml .= '</head>';
	    $posthtml .= "\n<body id=\"email\">\n\n";
	    $posthtml .= '<div class="navbar">'
	                . $OUTPUT->action_link($CFG->wwwroot.'/course/view.php?id='.$this->course->id, $this->course->shortname)
	    			. " &raquo; ".$OUTPUT->action_link(praxe_get_base_url(), format_string($praxe->name,true))
	    			. '</div>';
		if(strlen($this->subject)) {
	    	$posthtml .= '<div class="subject">'.format_text($this->subject).'</div>';
	    }
	    if(strlen($post)) {
	    	$posthtml .= '<div class="message">'.format_text($post).'</div>';
	    }
	    $posthtml .= "<hr /><div class=\"mdl-align unsubscribelink\">";
	    foreach($this->linkstofoot as $link) {
	    	$posthtml .= $OUTPUT->action_link($link->link,$link->text)."&nbsp;";
	    }
        $posthtml .= "</div><div class=\"mdl-align unsubscribelink\">".$OUTPUT->action_link($CFG->wwwroot,$site->shortname)."</div>";
	    $posthtml .= '</body>';
	    return $posthtml;
	}
}
