<?php
//$this->content->AddContent("<pre>". print_r($user->$id, true) ."</pre>");
/*
 * /profile - profile editor
 * /profile/upload_image
 * /profile/block_list
 * /profile/write
 *
 * /profile/editphoto/{photoid}
 * /profile/editwriting/{proseid}
 * /profile/editalbum/{proseid} !!!!!!!!!!!!!!!!!!NOT IMPLEMENTED SOMEHOW
 *
 * /profile/{userid}
 * /profile/{userid}/photos
 * /profile/{userid}/albums
 * /profile/{userid}/writing
 *
 * /profile/{userid}/writing/{proseid}
 * /profile/{userid}/photo/{photoid}
 */

/*
 * Group profiles:
 * owner only
 * * moderator or owner
 *
 *
 * /group or /groups - main page of groups section
 * /group/create
 *
 * /group/{groupid}
 * /group/{groupid}/edit
 * * /group/{groupid}/banned
 * * /group/{groupid}/invite_moderation
 * * /group/{groupid}/request_moderation
 *
 *
 * /group/{groupid}/newthread
 * /group/{groupid}/threads
 * /group/{groupid}/thread/{threadid}
 *
 * /group/{groupid}/notifications
 * /group/{groupid}/members
 * /group/{groupid}/invite
 */
		

function PrintMenu($html_minion) {
	global $user;
	
	$pMenu = $html_minion->menu->AddMenuItem(new MenuItem("Home", "/"));
	if ($user->is_logged_in()) {
		global $profile;
		 
		$pMenu = $html_minion->menu->AddMenuList(new MenuItem("Profile", "/profile/". $user->id));
		$pMenu->AddElement(new MenuItem("Edit", "/profile"));
		$pMenu->AddElement(new MenuItem("Block List", "/profile/block_list"));
		 
		$gMenu = $html_minion->menu->AddMenuList(new MenuItem("Photos", "/profile/". $user->id ."/photo/"));
		$gMenu->AddElement(new MenuItem("Upload", "/profile/". $user->id ."/photo/upload/"));
		 
		// loop to add albums to menu will go here.
		 
		$gMenu = $html_minion->menu->AddMenuList(new MenuItem("Writing", "/profile/". $user->id ."/writing/"));
		$gMenu->AddElement(new MenuItem("Write New", "/profile/". $user->id ."/writing/write/"));
		 
		// loop adding writing albums to menu will go here.
		 
		$gMenu = $html_minion->menu->AddMenuList(new MenuItem("Groups", "/group"));
		$gMenu->AddElement(new MenuItem("Create Group", "/group/create"));
		 
		if (count($user->groups) > 0)
		{
			foreach ($user->groups as $group)
			{
				$gMenu->AddElement(new MenuItem($group, "/group/". $group));
			}
		}
		 
		$html_minion->menu->AddMenuItem(new MenuItem("Logout", "/logout/"));
	} else { // user is not logged in.
		$html_minion->menu->AddMenuItem(new MenuItem("Login", "/login/"));
		$html_minion->menu->AddMenuItem(new MenuItem("Register", "/register/"));
	}
}
?>