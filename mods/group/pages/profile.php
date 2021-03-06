<?php 
/*
 * 
 *  use group object and its properties
 * 
 *  `name`, `owner`, `short`, `detail`, `avatar`, `group`, `city_id`
 *  
 *  `privacy`= 1 - public, 2- closed, 3 - secret
 *  
 *  `membership` - 0 not member 1 - member 2 - admin
 *  
 *  `invited` - boolean, only set if not a member and privacy > 1
 *  
 *  get_city() - returns string of city, state or ""
 *  
 *   get_member_count() -- see tin
 *   
 * 
 *   if( $group->membership > 0 ){
 *   
 *      leave button
 *      
 *    } elseif(  $group->privacy == 1 or $group->invited == true ){ 
 *    
 *      join button     
 *    
 *    } elseif( $group->privacy == 2 ) {
 *    
 *      request to join
 *    
 *    }
 *   
 * 
 */

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");
include(oe_isotope."isotope.php");
include(oe_isotope."head_tile.php");
include(oe_isotope."details_tile.php");
include(oe_isotope."group_members_tile.php");
include(oe_isotope."about_me_tile.php");
include(oe_isotope."thread_tile.php");
include(oe_isotope."photo_tile.php");

include($oe_modules['group']."conf/plugin.conf.php");

include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

include($oe_plugins['writing']."conf/conf.php");
include($oe_plugins['writing']."lib/writing.lib.php");

include($oe_plugins['thread']."conf/conf.php");
include($oe_plugins['thread']."lib/thread.lib.php");

$page = new page_minion("Profile");

$page->header();

$page->addjs(oe_js . "isotope.pkgd.min.js");
$page->addjs(oe_js . "imagesloaded.pkgd.js");
$page->addjs(oe_js . "isotope.js", true);
$page->addjs(oe_js . "tinymce/tinymce.min.js");
$page->addjs(oe_js . "invoketinymce.js");

$memeberAccess = $group->get_members(0, 8);

$photos = get_photos(0, 15);
$photosLen = count($photos);

$writings = get_writings(0, 15);
$writingsLen = count($writings);

$threads = get_threads(0, 15);
$threadsLen = count($threads);

$loopLength = $photosLen;
if ($loopLength < $writingsLen)
	$loopLength = $writingsLen;

if ($loopLength < $threadsLen)
	$loopLength = $threadsLen;

//$article = $content->AddTag("article", array("id" => "group-profile"));
$isotope = new Isotope($page);
$isotope->AddTile(new HeadGroupTile($group));
$isotope->AddTile(new DetailsGroupTile($group));

$isotope->AddTile(new GroupMembersTile($group, $memeberAccess));

$isotope->AddTile(new AboutMeGroupTile($group, $memeberAccess[$user->id]['access'] == 3));

for ($i = 0; $i < $loopLength; $i++) {
	if ($i < $photosLen) {
		$isotope->AddTile(new PhotoTile($photos[$i]));
	}
	
	if ($i < $writingsLen) {
		$isotope->AddTile(new WritingTile($writings[$i]));
	}
	
	if ($i < $threadsLen) {
		$isotope->AddTile(new ThreadTile($threads[$i]));
	}
}

$page->footer();