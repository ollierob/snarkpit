<?php

function post_poll($polldata) { global $mode,$farray;
	echo "\n<TR><TD colspan=2>"; subtitle('poll options',''); echo '</td></tr>';
	echo '<tr><td colspan=2>Note: you do not need to use all 6 options for your poll</td></tr>';

	if($farray['support']==1) echo '<tr><td colspan=2 height=40 valign=top>Sorry, you can\'t post polls in this forum.</td></tr>'; else {

		for($i=1;$i<=6;$i++) {
			echo "\n".'<tr><td align=right><b>Option '.$i.':</b></td>';
			echo '<td><input type="text" name="polloption'.$i.'" value="'.$polldata['option'.$i].'" size=48 class="textinput"></td></tr>';
		}

		echo '</td></tr>';
		echo "\n".'<input type="hidden" name="poll" value="1">';
	}
}

?>
