<?php
	title('FAQ','index');
	if(!$game = $userdata['game']) $game = $default_game;
	$gamename = mysql_result(mysql_query("SELECT name FROM games WHERE id = '$game' LIMIT 1"),0);
?>

Welcome to the SnarkPit! This site brings together both maps and mapping into one handy intermerweb thingummy. 
Not only are there a number of tutorials and guides on how to create maps for your favourite shooting games, but you can 
setup your 'homepage away from homepage' and show off your maps and prefabs, and let other people know how they're going.
In fact, this site gets updated pretty often code-wise, so don't be surprised if you come back a week later and there
are some more features that give you more power over your profile! <a href="changelog.txt">See here</a> for recent updates.
</p>

<p><img src="themes/<?=$theme?>/help.gif" align=right title="">If at any point you get stuck, or want to know how to use a page,
click the help icon in the top right of the page and there should (hopefully) be relevant info in there, with details
on how to post news on the front page, put images in your forum posts, and much more.</p>

<p>
<?php
	if(!$userdata) echo 'To start off, <b><a href="'.$url_register.'">register</a></b> if you haven\'t already, and then <a href="'.$url_login.'"><b>login</b></a>. ';
?>
This site is split into several sections, detailed below. Most pages have a 'help' link in the top right for if you
get stuck.

<p>

<?php @subtitle('<a href="editing.php">Map Editing</a>'); ?>
	<blockquote style="margin-top:0">This is the place to go for your mapping needs. You can browse the various tutorials, entity guides
	and download files from here for your default game (currently <?=$gamename?>- you can change
	this by logging in and editing your preferences). Or, if you have a specific problem, you can search our articles
	and previous forum posts, and if nothing comes up you'll be allowed to post in the revelant editing board.
	</blockquote>

<?php @subtitle('<a href="maps.php">Maps</a>'); ?>
	<blockquote style="margin-top:0">When someone updates their profile with a map, it'll show up on this page. When you initially
	visit it, you'll see a list of all the maps for your default game. You can then narrow it down to completed or beta
	maps, or search for single player levels/packs, or search by mod. From there you can then click on a map
	and see screenshots, read other peoples comments, and download or review it yourself.
	</blockquote>

<?php @subtitle('<a href="features.php">Features</a>'); ?>
	<blockquote style="margin-top:0">Right now, this is mainly about map reviews we've written over the last few years. There are also
	a few interviews up, and details about past and present map competitions. More is planned for the future.
	</blockquote>

<?php @subtitle('<a href="forums.php">Forums</a>'); ?>
	<blockquote style="margin-top:0">The hub of the site, this is where everyone 'hangs out'. You have to login & register to post in here.
	There's a general discussions board, a maps board (where extra map pimpage can be carried out), and various game
	editing boards (you need to search for your problem before being able to post a new topic in these)
	</blockquote>

<?php @subtitle('<a href="users.php">People</a>'); ?>
	<blockquote style="margin-top:0">This is esentially the member list and news page. From here you can read people's profiles, see
	recent profile updates and view member and website statistics.
	</blockquote>

<?php @subtitle('<a href="cp.php">Control Panel</a>'); ?>
	<blockquote style="margin-top:0">If you are logged in, then visit this page to update your profile, and add/edit
	maps, prefabs, tutorials, reviews and news.
	You can also send private messages and set your site/forum preferences from here such as your timezone, website theme,
	Javascript forum posting, and loads more.
	</blockquote>

<p>Do you have a suggestion- some new feature that you'd like to see us do here, or ideas for a new theme or graphics?
<a href="cp.php?mode=feedback&select=feedback">Let us know</a>!

<p><a name=snarkmarks><font size=4>SnarkMarks?</font></a></p>
	<p>Of course, you don't get anything for doing all of this. So what we've done is make a 'SnarkMark'
	system that tallies up how many tutorials and maps you've made (and their ratings), how many forum posts and comments,
	and looks at your user rating and calculates a score based on them. This might seem a bit sad, but at the end of the
	day it helps show other people both how Good and Cool© you are, and how much you've contributed to the site (being
	more reliable than going by forum post count or member number)- note that writing lots of tutorials is the fastest
	way to rack them up. If you don't care, great, just ignore it.
	Also note that the figure might be wrong as it isn't recalculated very often, and I am a fickle being and might
	randomly feel like changing them...
</p>

<p><a name="snarkpower"><font size=4>SnarkPower?!</font></a></p>
<p>One day, I will hopefully release these PHP forums for general use. I also plan on producing a PHP/database-driven 
	'skeleton' website, which will let mappers update news and maps and be easily customisable. Look out for this
	in the summer.
</p>

<p><a name="donate"><font size=4>Donations</font></a></p>
<p>There is now a donate link up on the <a href="?page=about">about us</a> page where you are welcome to donate towards
	our server costs. If we make too much money from it, then we'll stop donations, or give the excess to charity.
</p>

<p><a name="cookies"><font size=4>Cookies</font></a></p>
<p>We use cookies on this website for session management, some forum functionality and various other features to
"remember" which pages you've visited. This is simply to make the site work better for you: if we wanted to track your
website movements for secret nefarious purposes, there are easier, better ways to do so!
As long as you don't have any spyware on your computer and remember to logout if using a shared computer, there should
be no privacy issues arising through cookies used by this website.
