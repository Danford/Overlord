<?php
	   
include(oe_frontend."page_minion.php");
include(oe_frontend."html/modules/isotope.php");
include(oe_frontend."html/modules/invite_tile.php");
include(oe_frontend."html/modules/utility_tile.php");

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
	
	$isotope->AddTile(new UtilityTile($isotope->gridCategories));
	
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
		$date = new DateTime($photo['timestamp']);
		$phototile = new GridTile("photo", GridOption::None, array("data-date" => $date->getTimestamp()));
		$phototile->AddTag("div", array("id" => "title"))->AddTag("h3")->AddContent($photo['title']);
		$phototile->AddElement(new Img("/profile/". $photo['owner'] ."/photo/". $photo['id'] .".png", "loading", NULL, NULL, array("onload" => "ImageLoaded(this)")));
		$phototile->AddTag("p")->AddContent($photo['description']);
		$isotope->AddTile($phototile, "photo");
	}
	
	foreach ($wall_writings as $writing) {
		$date = new DateTime($photo['timestamp']);
		$writingtile = new GridTile("writing", GridOption::None, array("data-date" => $date->getTimestamp()));
		$writingtile->AddTag("div", array("id" => "title"))->AddTag("h3")->AddContent($writing['title']);
		$writingtile->AddTag("div", array("id" => "subtitle"))->AddTag("h4")->AddContent($writing['subtitle']);
		$writingtile->AddElement(new Img("/images/noavatar.png", "loading", NULL, NULL, array("onload" => "ImageLoaded(this)")));
	
		$excerpt = get_words($writing['copy'], 55);
		$writingtile->AddTag("div", array("id" => "excerpt"))->AddContent($excerpt);
		$writingtile->AddTag("div", array("id" => "full", "class" => "hidden"))->AddContent($writing['copy']);
		$isotope->AddTile($writingtile, "writing");
	}

	//$tile->SetStampLeft();

	print( 'Hello '.$user->name.'!  <a href="/logout">Log Out</a>' ) ;
}
else {
	// add welcome register / login tile.
	$welcome = new ElementTag("div");
	$welcome->AddTag("t1", array("class" => ""))->AddContent("Welcome to Overlord. You've found the right website. Just keep looking.");
	$welcome->AddTag("div", array("class" => "button", "style" => "width:50%;"))->AddTag("p")->AddContent("Get started!");
	$welcome->AddTag("div", array("class" => "button", "style" => "width:50%"))->AddTag("p")->AddContent("Sign In");
	$tile = $isotope->AddTile($welcome, "welcome")->SetLarge();
}


$page->html_minion->content->AddElement($isotope);

$page->footer(); 
?>