<?php title('News Archive','none'); tracker('News Archive','');

	$date = $_GET['date']; if(isset($_GET['site'])) $site = $_GET['site']; else $site = '';
	if(!$date) $date = date('m/Y');

	$todaymonth=date("m");
	$todayyear=date("Y");
	$todayym = $todayyear.$todaymonth;
	$startyear="2003";
	$startmonth="08";
?>
<b>Select month:</b>
<table width="99%" border="0" cellpadding="0" cellspacing="1" style="border-collapse:collapse;font-size:10px;" bgcolor="<?=$colors['trmouseover']?>"><tr>
<?php
	$monthspassed=(($todayyear-$startyear)*12 + $todaymonth-$startmonth);
	$x=0; $y=1; $z=0;

	while($y<$startmonth) { echo '<td width="8%" align=center>0'.$y.'/'.$startyear.'</td>'; $y++; }

	while($x<=$monthspassed) {

		$curmonth=$startmonth+$x;
		$curyear=$startyear;

		while($curmonth>12) { $curmonth = $curmonth - 12; $curyear=$curyear+1; }

		if ($curmonth<10) $curmonth="0".$curmonth;
		if ($curmonth=="01" && $curyear!=$startyear) echo "<tr>";
		if($curmonth.'/'.$curyear == $date) $b = '<b>'; else $b='';
		echo '<td align=center width="8%" bgcolor="'.$colors['bg'].'">'.$b.'<a href="?page=archive&amp;date='.$curmonth.'/'.$curyear.'&amp;site='.$site.'">'.$curmonth.'/'.$curyear.'</a></td>';
		if ($curmonth==12) echo "</tr>";

	$x++; }

while($z<12-$todaymonth) { $unf=$z+$todaymonth+1; echo '<td width="8%" align=center>'; if ($unf<10) echo "0"; echo $unf."/$curyear</td>"; ++$z; }

	list($selmonth,$selyear) = explode('/',$date);
	$startdate = mktime(0,0,0,$selmonth,1,$selyear);
	$enddate = mktime(0,0,0,$selmonth+1,1,$selyear);

?>
</table>
</p>

<table width="99%" cellspacing=0 cellpadding=2 style="font-size:10pt">
<?php
	if($site) $plan = "plan > '0'"; else $plan = "plan = '0'";
	$sql = mysql_query("SELECT * FROM news WHERE date > $startdate AND date < $enddate AND $plan ORDER BY id DESC");
	while($narray = mysql_fetch_array($sql)) news_item($narray);
?>
<tr><td width=24><img src="images/null.gif" width=26 height=0></td><td width=100%></td></tr>
</table>
