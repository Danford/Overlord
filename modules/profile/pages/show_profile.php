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
if ($profile->age != 0)
	$eAbout->AddTag("p")->AddContent($profile->age);
	// if( $profile->age != 0 ) { print( " ".$profile->age ) ; }

if ($profile->gender != 0)
	$eAbout->AddTag("p")->AddContent($gender[$profile->gender]['label']);
	// if( $profile->gender != 0 ) { print( " ".$gender[ $profile->gender]['label'] ) ; }

if ($profile->friend_count > 0)
{
	$eFriends = $eProfile->AddElement(new Div("p-friends"));
	$eFriends->AddContent("Friends");
	// print( 'Friends: '.$profile->friend_count.'<br /><br />' ) ;
	
	if ($profile->friend_count > 5)
	{
		$offset = $profile->friend_count - 5;
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

if ($profile->photo_count > 0)
{
	$ePhotos = $eProfile->AddElement(new Div("p-photos"));
	//print('<a href="/profile/' . $profile->id . '/photos">Photo');
	
	$secName = "Photo";
	if ($profile->photo_count > 1)
		$secName .= "s";
		//print('s');
	
	$secName .= ': ' . $profile->photo_count;
	$ePhoto = $ePhotos->AddElement(new Div("p-photo"));
	$ePhoto->AddElement(new ALink($secName, "/profile/". $profile->id ."/photos"));
	//print(': ' . $profile->photo_count . '</a><br /><br />');
}

if ($profile->video_count > 0)
{
	$eVideos = $eProfile->AddElement(new Div("p-videos"));
	//print('<a href="/profile/' . $profile->id . '/videos">Video');
	
	$secName = "Video";
	if ($profile->video_count > 1)
		$secName .= "s";
		//print('s');
	
	$secName .= ': ' . $profile->video_count;
	$eVideo = $eVideos->AddElement(new Div("p-video"));
	$eVideo->AddElement(new ALink($secName, "/profile/". $profile->id ."/videos"));
	//print(': ' . $profile->video_count . '</a><br /><br />');
}

if ($profile->prose_count > 0)
{
	$eProses = $eProfile->AddElement(new Div("p-proses"));
	//print('<a href="/profile/' . $profile->id . '/writing">Writing');
	
	$secName = "Writing";
	if ($profile->prose_count > 1)
		$secName .= "s";
		//print('s');
	
	$secName .= ': ' . $profile->prose_count;
	$eProse = $eProses->AddElement(new Div("p-prose"));
	$eProse->AddElement(new ALink($secName, "/profile/". $profile->id ."/writing"));
	//print(': ' . $profile->prose_count . '</a><br /><br />');
}

if ($profile->album_count > 0)
{
	$eAlbums = $eProfile->AddElement(new Div("p-albums"));
	//print('<a href="/profile/' . $profile->id . '/albums">Album');
	
	$secName = "Album";
	if ($profile->album_count > 1)
		$secName .= "s";
		//print('s');
	
	
	$secName .= ': ' . $profile->album_count;
	$eAlbum = $eAlbums->AddElement(new Div("p-album"));
	$eAlbum->AddElement(new ALink($secName, "/profile/". $profile->id ."/albums"));
	//print(': ' . $profile->album_count . '</a><br /><br />');
}

switch ($profile->friend_request_status())
{
	case 'self' :
		$eProfile->AddElement(new ALink("Edit Profile", "/profile"));
		//<a href="/profile">Edit Profile</a>
		break;
	
	case 'friend' :
		$eProfile->OpenBuffer();
		$form = new form_minion("removefriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Remove Friend");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
	
	case 'incoming' :
		$eProfile->OpenBuffer();
		$form = new form_minion("confirmfriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Confirm Friend Request");
		$form->footer();
		
		$form = new form_minion("denyfriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Deny Friend Request");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
	
	case 'outgoing' :
		$eProfile->OpenBuffer();
		$form = new form_minion("cancelfriendrq", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Cancel Friend Request");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
	
	case 'none' :
		$eProfile->OpenBuffer();
		$form = new form_minion("addfriend", "profile");
		$form->header();
		$form->hidden("user", $profile->id);
		$form->submit_button("Add Friend");
		$form->footer();
		$eProfile->CloseBuffer();
		break;
}

$page->footer();

?>