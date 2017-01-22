<?php 

    /*
     * 
     *      $profile->screen_name
     *      $profile->age
     *      $profile->city_id
     *      $profile->city
     *      $profile->state
     *      $profile->gender - int, corresponds to $gender in /core/_conf/oe_config.php
     *      $profile->detail 
     *      $profile->avatar - the URL to be included in the img tag
     *      $profile->allow_contact - boolean, if no don't show a contact box unless they're a friend
     *      
     *      $profile->get_friends_as_array()
     *      $profile->get_friends_count()     
     * 
     */
include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."head_tile.php");
include(oe_isotope."details_tile.php");
include(oe_isotope."about_me_tile.php");
include(oe_isotope."writing_tile.php");
include(oe_isotope."photo_tile.php");
include(oe_isotope."group_tile.php");
include(oe_isotope."friends_tile.php");
include(oe_isotope."mututal_friends_tile.php");

include($oe_modules['profile']."lib/friends_api.php");
include($oe_modules['profile']."conf/plugin.conf.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

include($oe_plugins['writing']."conf/conf.php");
include($oe_plugins['writing']."lib/writing.lib.php");

function get_words($sentence, $count = 150) {
	return implode(' ', array_slice(explode(' ', $sentence), 0, $count));
}

function print_words($sentence, $count = 150) {
	$words = explode(' ', $sentence);
	
	$limited_words = array_slice($words, 0, $count);
	echo implode(' ', $limited_words);
	
	if (count($words > $count))
		return true;
	return false;
}

$page = new page_minion("Profile");

$page->header();

$page->addjs(oe_js . "tinymce/tinymce.min.js");
$page->addjs(oe_js . "invoketinymce.js");

$friends = $profile->get_friends_as_array(0, 9);

$mutualFriends = array();

foreach ($friends as $friend)
{
	if ($user->is_friend($friend->id))
		$mutualFriends[] = $friend;
}



$photos = get_photos(0, 15);
$photosLen = count($photos);

$writings = get_writings(0, 15);
$writingsLen = count($writings);

$groups = $profile->get_groups();
$groupsLen = count($groups);

$loopLength = $photosLen;

if ($loopLength < $writingsLen)
	$loopLength = $writingsLen;

if ($loopLength < $groupsLen)
	$loopLength = $groupsLen;

$isotope = new Isotope($page);

$isotope->AddTile(new HeadProfileTile($profile));
$isotope->AddTile(new DetailsProfileTile($profile));

if ($user->id != $profile->id && count($mutualFriends) > 0) {
	$isotope->AddTile(new MutualFriendsTile($profile, $mutualFriends));
}

$isotope->AddTile(new FriendsTile($profile, $friends));
$isotope->AddTile(new AboutMeProfileTile($profile));

for ($i = 0; $i < $loopLength; $i++) {
	if ($i < $photosLen) {
		$isotope->AddTile(new PhotoTile($photos[$i]));
	}
	
	if ($i < $writingsLen) {
		$isotope->AddTile(new WritingTile($writings[$i]));
	}
	
	if ($i < $groupsLen) {
		$isotope->AddTile(new GroupTile($groups[$i]));
	}
}

$page->footer();

?>