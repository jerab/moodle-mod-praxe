<?php  // $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of praxe_record for students
 *
 * @author  Your Name <your@email.address>
 * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
 * @package mod/praxe
 */
/*
if(!has_capability('mod/praxe:editownrecord',$context) && !has_capability('mod/praxe:editanyrecord',$context)) {
	error("You don't have rights for this action!");
}
*/
class praxe_view {
	
	protected $content = '';
	
	function praxe_view() {
		
	}

	public function display_content() {
		if(isset($this->form)) {			
			/// some error that does not allow to use form properly ///
			if($this->form->error) {
				$this->form->add_to_content($this->form->error);
				$this->form->display_content_before();
			}else {
				$this->form->display_content_before();
				$this->form->display();
			}
			$this->form->display_content();
		}
		echo $this->content;
	}
	
	public function get_content() {
		return $this->content;
	}
	
	public function addto_content($str) {
		if(is_string($str)) {
			$this->content .= $str;
		}
	}
	
}
?>