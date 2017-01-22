<?php
/*
 * 
 * 
 *  $profile->get_friends_as_array() ;
 *  
 *   Note, results include "friend" which is 0 or 1 depending on if the friend being shown is 
 *   friends with, y'know, the person viewing the page.
 *   
 *   Note that the results currently sort by screen name. Later we can do like FB and show mutual friends first.
 *   
 *   Also will want the ability to unfriend or block friends if $profile->id == $user->id
 *   
 *   
 * 
 * 
 * 
 */


include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."friends_tile.php");

include($oe_modules['profile']."lib/friends_api.php");
include($oe_modules['profile']."conf/plugin.conf.php");

$page = new page_minion("Profile");

$page->header();

$page->addjs(oe_js . "isotope.pkgd.min.js");
$page->addjs(oe_js . "imagesloaded.pkgd.js");
$page->addjs(oe_js . "isotope.js", true);

$friends = $profile->get_friends_as_array(0, 9);

$mutualFriends = array();

foreach ($friends as $friend)
{
	if ($user->is_friend($friend->id))
		$mutualFriends[] = $friend;
}

$friendsTile = new FriendsTile($profile, $friends);
$friendsTile->RemoveStamp();
$friendsTile->SetXLarge();
$friendsTile->Serve();

$page->addjs("/js/overlord.js", true);
$page->footer();

?>