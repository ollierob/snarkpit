<?php
	$theme = $_GET['theme']; if(!$theme) $theme = 'standard';
	$numthumbs = $_GET['numthumbs']; $map = $_GET['map'];
	$scr = $_GET['scr'];

	if($map) {
		include('config.php');
		$maparray = @mysql_fetch_array(mysql_query("SELECT thumbnails,scr1,scr2,scr3,scr4,scr5 FROM maps WHERE map_id = '$map' LIMIT 1"));
		$numthumbs = $maparray['thumbnails'];
	}
	//if(!$numthumbs||!$_GET['scr']) $map = '';
	if(!include('themes/'.$theme.'.php')) die('Invalid theme selected');
	$img = $_GET['img']; if($img && substr($img,0,4)=='www.') $img = 'http://'.$img;
	
	$filetype = substr($img,-3,3);
	if($filetype=='php' && (!substr_count($img,'http')||substr_count($img,'snarkpit.net'))) die('Nice try Einstein :P');

	if(substr($img,0,5)=='maps/'||substr($img,0,6)=='files/'||substr($img,0,5)=='pits/'||substr_count($img,'http://www.snarkpit.net')) {
		$local = true; $img = str_replace('http://www.snarkpit.net/','',$img);
		if(!file_exists($img)) die('404 - Image not found');
		$size = getimagesize(str_replace(' ','%20',$img));
		echo '<!--//gis hor:'.$size[0].'-->';
	} else {
	       	echo '<!--//not local-->';
		$local = false; 
		$size = array(600,400);
	}
	if($numthumbs>1) {
	        $size[0]+=130;
		$h_alter = 62;
	} else {
	       	$h_alter = 100;
	}
	
	if(!$map||!$numthumbs) $size[0]+=8;
	
	$thumbnailheight = ($numthumbs*120) + 20;
	if($size[1]<$thumbnailheight) $size[1] = $thumbnailheight;
?>
<HTML>
<HEAD>
<link rel="stylesheet" href="themes/<?=$theme?>.css" type="text/css">
<?php

	if(!$img) { echo '</head><body onload="self.close()"></body>'; die; }

	if(substr_count($img,'.htm')==1) { ?>

<title>The SnarkPit</title>
<SCRIPT LANGUAGE="JavaScript">
	window.resizeTo(200,100); 
</script>

</head>
<body bgcolor="<?=$colors['bg']?>" vlink=yellow link=yellow>	
<p align=center><font face=verdana size=2>Please <a href="<?=$img?>" target="_blank" onClick="window.close()">click here</a> to view the screenshots of this map
<br><br>(Doing so will close this window)</p>

<?php } else { ?>

<title>The SnarkPit - Image</title>

<?php if(!$local) { ?>
<script>

	var NS = (navigator.appName=="Netscape")?true:false; 

	function resizewindow() {

		iWidth = (NS)?window.innerWidth:document.body.clientWidth; 
		iHeight = (NS)?window.innerHeight:document.body.clientHeight; 
		iWidth = document.images[0].width - iWidth;
		iHeight = document.images[0].height - iHeight;

		<?php if($scr) echo '		iHeight += 22;'; ?>

		window.resizeBy(iWidth, iHeight);
		self.focus();
	}

</script>

<? } else $size[1]+=$h_alter; ?>

</HEAD>
<BODY bgcolor="<?=$colors['bg']?>"<?=(($local)?'':' onload="resizewindow()"')?>>

<script>
	window.resizeTo(<?=$size[0]?>,<?=($size[1])?>);
</script>

<div style="position:absolute; left:0px; top:0px">
<table width="100%" <?=(($size[1])?'height='.($size[1]-$h_alter).' ':'')?>cellpadding=0 cellspacing=0><tr>
<td valign=top><img src="<?=$img?>" alt="image" style="border:0"><?php
	if($scr) {
	        echo '<div style="text-align:center">';
		if($scr!=1) echo '<a href="?map='.$map.'&amp;scr='.($scr-1).'&amp;img='.$maparray['scr'.($scr-1)].'">« previous</a> ';
		if($maparray['scr'.($scr+1)]) echo (($scr!=1)?'| ':'').' <a href="?map='.$map.'&amp;scr='.($scr+1).'&amp;img='.$maparray['scr'.($scr+1)].'">next »</a>';
		echo '</div>';
	}
?>
</td>
<?php if($map && $numthumbs>1) { ?>
	<td width=120><table width="100%"><tr><td align=center>
	<?php	//'<b>'.$maparray['name'].'
		echo '<p><a href="javascript:void(0)" onclick="opener.location=\'maps.php?map='.$map.'\';opener.focus();self.close();" style="font-size:8pt">map info</a></b></p><p>'."\n";
		$location = str_replace('.jpg','',$img);
		if(substr($location,-6)=='review') { $location = str_replace('_review','',$location); $location = substr_replace($location,'',-1); $ext = '_review'; } 
		else { $location = substr_replace($location,'',-1); $ext = ''; }
		for($i=1;$i<=$numthumbs;$i++) echo "\n".'<p><a href="?map='.$map.'&theme='.$theme.'&numthumbs='.$numthumbs.'&img='.$location.$i.$ext.'.jpg"><img src="'.$location.$i.$ext.'_thumb.jpg" border=0 class="thumb"></a></p>';
	?>
	<a href="javascript:void(0)" onclick="self.close()" style="font-size:8pt"><b>close<br>window</b></a>

	</td></tr></table></td>
<? } ?>
</tr></table>
</div>

<?php } ?>
</BODY>
</HTML>
