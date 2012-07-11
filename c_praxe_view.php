<?php
/**
 * This page prints a particular instance of praxe_record for students
 *
 * @author  Tomas Jerabek <t.jerab@gmail.com>
 * @version
 * @package mod/praxe
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