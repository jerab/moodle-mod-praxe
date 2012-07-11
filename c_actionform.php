<?php //$Id: mod_form.php,v 1.2.2.3 2009/03/19 12:23:11 mudrd8mz Exp $

/**
 *
 */

require_once ($CFG->libdir.'/formslib.php');

class praxe_actionform extends moodleform {

	protected $content = '';
	protected $content_before_form = '';
	public $error = null;

	function praxe_actionform($action = false) {
		global $cm;
		if($action) {
			parent::moodleform("view.php?id=$cm->id&action=$action");
		}else{
			parent::moodleform("view.php?id=$cm->id");
		}
	}

	protected function definition() {

	}

	public function display_content() {
		echo '<div class="praxe_after_form">'.$this->content.'</div>';
	}

	public function get_content() {
		return $this->content;
	}

	public function display_content_before() {
		echo '<div class="praxe_before_form">'.$this->content_before_form.'</div>';
	}

	public function add_to_content($str, $to_before = true) {
		if($to_before) {
			$this->content_before_form .= $str;
		}else {
			$this->content .= $str;
		}
	}

	public function set_redirect_url($url = null, $params = array()) {
    	global $CFG, $cm;
    	$params['id'] = $cm->id;
    	if(!is_null($url)) {
    		$url = $url;
    	}else {
    		foreach($params as $name=>$val) {
				$par[] = s($name)."=".s($val);
			}
			$url .= $CFG->wwwroot."/mod/praxe/view.php?".implode('&amp;',$par);
    	}
    	$this->_form->addElement('hidden','redurl',$url);
    }
}

?>
