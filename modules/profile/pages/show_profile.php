<?php
if ($profile->id != $user->id)
	include (oe_lib . "form_minion.php");

require_once (oe_frontend . "page_minion.php");
require_once (oe_frontend . "/html/tags/div.php");
require_once (oe_frontend . "/html/tags/img.php");
require_once (oe_frontend . "/html/tags/alink.php");

$page = new page_minion($profile->name . " - Profile");

$page->header();

$eProfile = $page->html_minion->content->AddElement(new Div("profile"));
$eProfile->AddField("id", "profile");

$eProfileImage = $eProfile->AddElement(new Div("p-img"));

$eProfileImage->AddElement(new Img($profile->profile_picture()));
// print( '<img src="'.$profile->profile_picture().'" /><br />' ) ;

$eAbout = $eProfile->AddElement(new Div("profile-about"));
$eAbout->AddContent("About Me:");
$eAbout->AddTag("p")->AddContent($profile->name);
// print( $profile->name ) ;

$eAbout->AddTag("p")->AddContent($profile->detail);
if ($profile->age != '')
	$eAbout->AddTag("p")->AddContent($profile->age);
	// if( $profile->age != 0 ) { print( " ".$profile->age ) ; }

if ($profile->gender != 0)
	$eAbout->AddTag("p")->AddContent($gender[$profile->gender]['label']);
	// if( $profile->gender != 0 ) { print( " ".$gender[ $profile->gender]['label'] ) ; }

$c = $profile->get_friends_count() ;	
	
if ($c > 0)
{
	$eFriends = $eProfile->AddElement(new Div("p-friends"));
	$eFriends->AddContent("Friends");
	// print( 'Friends: '.$profile->friend_count.'<br /><br />' ) ;
	
	if ($c > 5)
	{
		$offset = $c - 5;
	}
	else
	{
		$offset = 0;
	}
	
	$friends = $profile->get_friends($offset);
	foreach ($friends as $friend)
	{
		$eFriend = $eFriends->AddElement(new Div("p-friend"));
		$efLink = $eFriend->AddElement(new ALink($friend['profile']->name, '/profile/' . $friend['profile']->id));
		$efLink->AddElement(new Img($friend['profile']->profile_thumbnail(), "height: 60px"));
		// print( '<a href="/profile/'.$friend['profile']->id.'" title="'.$friend['profile']->name.'">
		// <img style="height: 60px" src="'.$friend['profile']->profile_thumbnail().'"></a><br />' ) ;
	}
}



switch ($profile->friend_request_status())
{
	case 'self' :
		$eProfile->AddElement(new ALink("Edit Profile", "/profile"));
		//<a href="/profile">Edit Profile</a>
		break;
	
	case 'friend' :
		$eProfile->OpenBuffer();
		$form = new form_minion("removeFriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Remove Friend");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
	
	case 'incoming' :
		$eProfile->OpenBuffer();
		$form = new form_minion("confirmFriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Confirm Friend Request");
		$form->footer();
		
		$form = new form_minion("denyFriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Deny Friend Request");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
	
	case 'outgoing' :
		$eProfile->OpenBuffer();
		$form = new form_minion("cancelFriendrq", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Cancel Friend Request");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
	
	case 'none' :
		$eProfile->OpenBuffer();
		$form = new form_minion("addFriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Add Friend");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
}

$page->footer();

?>