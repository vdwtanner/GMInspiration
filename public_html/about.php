<?php
	// Define variables for SEO
	$pageTitle = "About Us - The GM's Inspiration";
	$pageDescription = "Get to know The GM's Inspiration";
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
</head>

<body>
<div id='container'>
	<h2>Welcome to The Game Master's Inspiration!</h2>
	<p>GMinspiration.com is an online repository for homebrew or custom content for all your favorite table top roleplaying games.
	 Need an terrifying new monster to bring your players to tears? Perhaps you need a small artifact of the god of light to give them the slightest chance of surviving your plans?
	 Search through our online database of user-submitted contributions for something that matches your maniacal tastes.</p>
	<p>When you feel it's time to give back to the community, or just want to yell at some guy who thought you can use a d20 as a damage die,
	 <a href='sign_up.php'>sign up</a> for an account with us. You'll be able to contribute homebrew items, spells, monsters, and other content and get feedback on your work, as well as give
	 feedback to those other plebians whose content offended your fragile nerd heart.</p>
	<blockquote style='max-width: 20em;'>This sword gave me and all my players cancer.<br><span style='display:inline-block; padding-left: 6em;'>-One of our amazing users.<span></blockquote>
	<p>Enjoy our site! It is still fairly new, so make sure to <a href='contact.php'>contact us</a> if you find any issues.</p>
	<p>Look forward to our companion app on the Android market! It is currently under development, but should be available for download before the end of summer 2015!</p>
<!--
	<h4 style='font-size: 120%'>Your Profile</h4>
	<p>After signing up, you should take a second to check out your profile page. From here you can,</p>
		<ul>
		<li>Update your profile description.</li>
		<li>Update your profile picture.</li>
		<li>Find a list of all of the contributions you've submitted.</li>
		<li>Change your password.</li>
		</ul>

	<h4 style='font-size: 120%'>Your Inbox</h4>
	<p>Your inbox contains all your private messages from other users. Use this service to discreetly tell a user his/her contribution is terrible to save them from public embarassment.
	  What a nice person you are!</p>
	<h4 style='font-size: 120%'>Contribution Privacy</h4>
	<p>There are three settings for privacy options on contributions,</p>
		<ul>
		<li>Private: only the creator may view or edit this contribution.</li>
		<li>Public: all visitors to the site may view this contribution. Users may comment and rate.</li>
		<li>Protected: This contribution will not show up in any search results, however it may be shared via direct link or through the collections system.</li>
		</ul>
-->

</div>
</body>
</html>
<?php include 'footer.php';?>