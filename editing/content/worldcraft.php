<?php include('../../themes/standard/header.htm'); include('../../themes/standard.php');

function subtitle($text) { global $colors;
	echo '<b><font color="'.$c_yellow.'">'.$text.'</font></b>';
	echo '<table width="100%" cellspacing=0 cellpadding=0 bgcolor="maroon"><tr><td></td></tr></table>'."\n";
}

echo '<span style="font-size:8pt">'; $i= $_GET['i'];

switch($i) {

case(''):
	echo 'Click on part of the window below for details of what it does and how to use it.:';
break;

case('3dwindow'):
	subtitle('3D window');
	echo 'This window should ideally show the 3D view- what you see here is determined by the direction in which the camera is looking. Press the <b>camera</b> button in the top left to choose the render mode.';
break;
case('2dwindow'):
	subtitle('2D windows');
	echo 'Well, it is on mine anyway. I suggest you make this window display the 2D view from above by clicking on the small button in the top left and selecting "top (x/y)" from the menu';	
break;

case('grid'):
	subtitle('Grid Options');
	echo 'The first button shows/hides the grid lines in the 2D windows.
		<p>The second button will show a grid in the 3D window.
		<p>The last 2 buttons changes the grid size (and the snap-to setting as well)';
break;

case('go'):
	subtitle('Compile');
	echo 'Press this button to start compiling. Of course, we\'d suggest you compile outside of Hammer (see our files
		page for programs like TBCC).';
break;

case('select'):
	subtitle('Selection Tool');
	echo 'This tool can be used to select objects by clicking on them. By holding the LMB down, you can drag out a rectangle and (try to) select objects within it.';
	echo '<p>By clicking on an object, you can change its size or move it.
		<p>By clicking on an object twice, you can rotate it.
		<p>By clicking on an object thrice, you can "shift" the edges.
		<p>By clicking on an object, um, 4 times, you can change its size again.';
break;
case('magnify'):
	subtitle('Magnify Tool');
	echo 'Clicking on one of the editable (2d) windows will zoom in, whereas a right click will zoom out.';
	echo '<p>Too add a new camera, hold SHIFT and click &amp; drag in one of the 2D windows.';
break;
case('camera'):
	subtitle('Camera Tool');
	echo 'This tool allows you to alter the direction in which a camera is facing, and to place new ones.
		<p>To place the first camera, just select this tool and click on one of the 2D windows.
		<p>To create a new camera, hold down SHIFT and click in a 2D window.
		<p>To delete cameras, just press DEL when you have a camera selected.';
break;

} echo '<p><img src="wc_'.$i.'.gif">';

?></span>