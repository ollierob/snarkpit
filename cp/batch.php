<?php
	title('Create an HL2 compile batch file','cp');
	if(isset($_GET['mod'])) $mod = $_GET['mod'];
?>

<script language="Javascript">

function trim(val) {
	var w_space = String.fromCharCode(32);
	if(v_length < 1) return "";
	var v_length = val.length;
	var strTemp = "";
	var iTemp = 0;
	while(iTemp < v_length) {
		if(val.charAt(iTemp) == w_space) {}
		else{
			strTemp = val.substring(iTemp,v_length);
			break;
		}
		iTemp = iTemp + 1;
	}
	return strTemp;
}

function make_bat() {

	var mapname = trim(document.getElementById('mapname').value);
	var mapdir = trim(document.getElementById('mapdir').value);
	var steamdir = trim(document.getElementById('steamdir').value);

	var bspoptions = ''; var visoptions = ''; var radoptions = '';
	//all
	if(document.getElementById('o_low').checked==true) { bspoptions += ' -low'; visoptions += ' -low'; radoptions += ' -low'; }
	if(document.getElementById('o_verbose').checked==true) { bspoptions += ' -v'; visoptions += ' -v'; radoptions += ' -v'; }
	//bsp
	if(document.getElementById('o_onlyents').checked==true) bspoptions += ' -onlyents';
	if(document.getElementById('o_glview').checked==true) bspoptions += ' -glview';
	if(document.getElementById('o_nowater').checked==true) bspoptions += ' -nowater';
	//vis
	if(document.getElementById('o_fastvis').checked==true) visoptions += ' -fast';
	//rad
	if(document.getElementById('o_fastrad').checked==true) radoptions += ' -fast';
	if(document.getElementById('o_noextra').checked==true) radoptions += ' -noextra';
	if(document.getElementById('o_nodetail').checked==true) radoptions += ' -nodetaillight';
	if(document.getElementById('o_bounce').value!=100) radoptions += ' -bounce '+document.getElementById('o_bounce').value;
	if(document.getElementById('o_smooth').value!=45) radoptions += ' -smooth '+document.getElementById('o_smooth').value;

	var code = '@ECHO OFF';
	code += '<br>set mapname='+mapname;
	code += '<br>set steamdir='+steamdir;
	code += '<br>set hammerdir=sourcesdk&#92;bin';
	code += '<br>set gamedir='+document.getElementById('mod').options[mod.selectedIndex].value;
	code += '<br>set mapdir='+mapdir;
	code += '<br>@ECHO ON';

	code += '<br>%steamdir%&#92;%hammerdir%&#92;vbsp.exe '+bspoptions+' -game "%steamdir%&#92;%gamedir%" "%steamdir%&#92;%mapdir%&#92;%mapname%"';
	code += '<br>%steamdir%&#92;%hammerdir%&#92;vvis.exe '+visoptions+' -game "%steamdir%&#92;%gamedir%" "%steamdir%&#92;%mapdir%&#92;%mapname%"';
	code += '<br>%steamdir%&#92;%hammerdir%&#92;vrad.exe '+radoptions+' -game "%steamdir%&#92;%gamedir%" "%steamdir%&#92;%mapdir%&#92;%mapname%"';

	code += '<br>if exist "%steamdir%&#92;%mapdir%&#92;%mapname%.bsp" copy "%steamdir%&#92;%mapdir%&#92;%mapname%.bsp" "%steamdir%&#92;%gamedir%&#92;maps&#92;%mapname%.bsp"';

	if(document.getElementById('rungame').checked==true && steamdir!='') {
		//var lastslash = steamdir.lastIndexOf('\\');
		var steamappdir = steamdir.substring(0,steamdir.lastIndexOf('\\'));
		var steamexedir = steamappdir.substring(0,steamappdir.lastIndexOf('\\'));
		code += '<br>'+steamexedir+'&#92;Steam.exe -applaunch '+document.getElementById('mod').options[mod.selectedIndex].name+' -console 1 +map '+mapname+' +sv_cheats 1';
	}

	document.getElementById('bat_code').innerHTML = code;
}
</script>

<table width="100%" cellspacing=2 cellpadding=2>
<tr>
	<td width="20%" align=right>Map file name:</td>
	<td width="80%"><input type="text" size=24 class="textinput" id="mapname">
	<span class="help">e.g. <b>dm_monkey_beta1</b></span></td>
</tr>
<tr>
	<td width="20%" align=right>Steam user directory:</td>
	<td width="80%"><input type="text" name="steamdir" size=32 class="textinput">
	<span class="help">e.g. <b>c:\Steam\Steamapps\username</b></span>
</tr>
<tr>
	<td width="20%" align=right>Map directory:</td>
	<td width="80%"><input type="text" id="mapdir" size=32 class="textinput">
	<span class="help">e.g. <b>sourcesdk_content\hl2mp\mapsrc</b></span>
</tr>
<tr>
	<td width="20%" align=right>Mod:</td>
	<td width="80%"><select id="mod">
		<option value="half-life 2\hl2" name="220">Half-Life 2 single player
		<option value="half-life 2 deathmatch\hl2mp" name="320"<?(($mod=='dm')?' selected':'')?>>Half-Life 2 deathmatch
		<option value="counter-strike source\cstrike" name="240">Counter-Strike: Source
	</select>
</tr>
<tr>
	<td></td>
	<td><fieldset style="width:95%" class="textinput">
		<legend class="submit">compile options</legend>
		<table width="100%" style="font-size:9pt">
		<tr>
		<td width="10%"><b>all:</b></td>
		<td width="30%"><input type="checkbox" id="o_low"><label for="o_low"> low priority process</label></td>
		<td width="30%"><input type="checkbox" id="o_verbose"><label for="o_verbose"> verbose mode</label></td>
		<td width="30%"></td>
		</tr>
		<tr>
		<td><b>VBSP:</b></td>
		<td><input type="checkbox" id="o_onlyents"> <label for="o_onlyents">onlyents</label></td>
		<td><input type="checkbox" id="o_glview"> <label for="o_glview">glview</label></td>
		<td><input type="checkbox" id="o_nowater"> <label for="o_nowater">no water</label></td>
		</tr>
		<tr>
		<td><b>VVIS:</b></td>
		<td><input type="checkbox" id="o_fastvis"> <label for="o_fastvis">fast VIS</label>
		</tr>
		<tr>
		<td><b>VRAD:</b></td>
		<td><input type="checkbox" id="o_fastrad"> <label for="o_fastrad">fast RAD</label></td>
		<td><input type="checkbox" id="o_noextra"> <label for="o_noextra">no extra sampling</label></td>
		<td><input type="checkbox" id="o_nodetail"> <label for="o_nodetail">no prop lighting</label></td>
		</tr>
		<tr>
		<td></td>
		<td>bounce: <input type="text" id="o_bounce" size=4 maxlength=3 class="textinput" value="100"></td>
		<td>smooth: <input type="text" id="o_smooth" size=4 maxlength=3 class="textinput" value="45"></td>
		</tr>
		<tr>
		<td></td>
		<td colspan=2><input type="checkbox" id="rungame"> <label for="rungame">run game when finished</label></td>
		</tr>
		</table>
	</fieldset></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value="Generate!" onclick="make_bat()" class="submit3"></td>
</tr>
</table>

Copy and paste the code given below into a new file called, for example, <i>mapname</i>_compile.bat. You can
do this simply by making a new text file with Notepad, pasting the text below into it, and renaming it to change the
extension from .txt to .bat. Save your map and close Hammer, and run this program, and your map will compile quicker.

<div id="bat_code" style="font-size:10px;margin-left:10px;margin-top:5px;line-height:1.4em">Fill in all the above field and click 'generate'</div></blockquote>
<p>
