<?php
	function spellcheck($message) {
		
		$words = array();

		$words['because'] = array('becuase');
		$words['you are'] = array('u are');

		while(list($word,$optionarray)=each($words)) {
		
			while(list($key,$xword)=each($optionarray)) {
				$xword .= ' ';
				$message = str_replace($xword,$word.' ',$message);
   			}

  		}

	}
?>
