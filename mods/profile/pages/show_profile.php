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
include($oe_modules['profile']."conf/plugin.conf.php");
include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

$page = new page_minion("Profile");

$page->header();

$page->addcss("https://npmcdn.com/flickity@2.0.4/dist/flickity.css");

$page->addjs("https://npmcdn.com/flickity@2/dist/flickity.pkgd.js");

$friends = $profile->get_friends_as_array(0, 9);
$photos = get_photos();

function UserInteraction($apiCall, $buttonText)
{
	$form = new form_minion($apiCall, 'profile');
	$form->header();
	$form->hidden('user', $profile->id);
	$form->submit_button($buttonText);
	$form->footer();
}

function AddFriend()
{
	UserInteraction('addFriend', 'Request Friend');
}

function RemoveFriend()
{
	UserInteraction('removeFriend', 'Remove Friend');
}

function CancelRequest()
{
	UserInteraction('cancelFriendrq', 'Cancel Request');
}

function ConfirmFriend()
{
	UserInteraction('confirmFriend', 'Confirm Friend');
}

function DenyFriend()
{
	UserInteraction('denyFriend', 'Deny Request');
}

function BlockUser()
{
	UserInteraction('blockUser', 'Block User');
}

function UnblockUser()
{
	UserInteraction('unblockUser', 'Unblock User');
}

function PrintUserInteractions()
{
	global $profile;
	global $user;
	
	if ($profile->id != $user->id)
	{
		if (!$user->is_friend($profile->id))
		{
			if ($profile->friend_request_status() == "outgoing")
				CancelRequest();
			elseif ($profile->friend_request_status() == "incoming")
			{
				ConfirmFriend();
				DenyFriend();
			}
			else
				AddFriend();
		}
		else
			RemoveFriend();
		
		if (!$user->is_blocked($profile->id))
			BlockUser();
		else
			UnblockUser();
	}
}

?>
<article id="profile">
	<div id="left-sidebar">
		<div class="sidebar-container tile">
			<p id="name"><?php echo $profile->screen_name; ?></p>
			<div id="main-image">
				<img src="/profile/<?php echo $profile->id .'/photo/'. $profile->avatar; ?>.png"/>
				<?php PrintUserInteractions(); ?>
			</div>
			
		</div>
		<div class="sidebar-container tile">
			<div id="details">
				<p id="age">Age: <?php echo $profile->age; ?></p>
				<p id="location">City: <?php echo $profile->city_name() ; ?></p>
				<p id="gender">Gender: <?php echo $gender[$profile->gender]['label']; ?></p>
			</div>
		</div>
		<div class="sidebar-container tile">
			<div id="friends">
				<div id="head">Friends - (<?php echo $profile->get_friends_count(); ?>)</div>
				<div id="body">
					<?php foreach ($friends as $friend) : ?>
					<div class="friend">
						<div class="name">
							<?php echo $friend->screen_name; ?>
						</div>
						<div class="profile-img">
							<img src="<?php echo $friend->profile_thumbnail(); ?>"/>
						</div>
						<div class="button request-friend">Add Friend</div>
					</div>						
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div id="main" class="tile">
		<p id="about-me"><?php echo $profile->detail; ?></p>
	</div>
	<div id="photo-albums" class="tile">
		<div class="carousel" data-flickity>
			<?php foreach ($photos as $photo) : ?>
			<div class="carousel-cell tile">
				<div id="photo"><img src="/profile/<?php echo $photo['owner']->id; ?>/photo/thumb/<?php echo $photo['id']; ?>.png" /></div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</article>

<?php $page->footer(); ?>