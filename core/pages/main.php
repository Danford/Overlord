<?php

include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."invite_tile.php");
include(oe_isotope."photo_tile.php");
include(oe_isotope."writing_tile.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

include($oe_plugins['writing']."conf/conf.php");
include($oe_plugins['writing']."lib/writing.lib.php");

include($oe_plugins['invitations']."api.php");

function get_words($sentence, $count = 150) {
	return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
}

$wall_photos = $user->get_wall_content_photos();
$wall_writings = $user->get_wall_content_writing();
$invintations = $invite->get_invited_as_objects();

$page= new page_minion("Main Page");

$page->header();

$isotope = new Isotope($page);

if( $user->is_logged_in() ) {
	
	$requestProfiles = $user->get_friend_request();
	
	foreach ($requestProfiles as $profile) {
		$tileContent = new InviteTileProfile($profile);
		
		$isotope->AddTile($tileContent)->SetStampLeft();
	}
	
	$groupInvites = $user->get_group_request();
	 
	foreach ($groupInvites as $groupInvite) {
		$profile = $groupInvite['invitor'];
		$group = $groupInvite['group'];
		
		$tileContent = new InviteTileGroup($group, $profile);
		
		$isotope->AddTile($tileContent)->SetStampLeft();
	}
	
	foreach ($wall_photos as $photo) {
		printVarible($photo);
		$isotope->AddTile(new PhotoTile($photo), "photo");
	}
	
	foreach ($wall_writings as $writing) {
		$isotope->AddTile(new WritingTile($writing), "writing");
	}

	//$tile->SetStampLeft();

	//print( 'Hello '.$user->name.'!  <a href="/logout">Log Out</a>' ) ;
}
else {
	// add welcome register / login tile.
	$welcome = new ElementTag("div");
	$welcome->AddTag("t1", array("class" => ""))->AddContent("Welcome to Overlord. You've found the right website. Just keep looking.");
	$welcome->AddTag("a", array("href" => "/register"))->AddTag("div", array("class" => "button", "style" => "width:50%;"))->AddTag("p")->AddContent("Get started!");
	$welcome->AddTag("a", array("href" => "/login"))->AddTag("div", array("class" => "button", "style" => "width:50%"))->AddTag("p")->AddContent("Sign In");
	$tile = $isotope->AddTile($welcome, "welcome")->SetLarge();
}

$page->footer(); 
?>