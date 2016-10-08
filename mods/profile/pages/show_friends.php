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
include($oe_modules['profile']."conf/plugin.conf.php");
include($oe_plugins['photo']."conf/conf.php");
include($oe_plugins['photo']."lib/photo.lib.php");

$page = new page_minion("Profile");

$page->header();

$page->addcss("https://npmcdn.com/flickity@2.0.4/dist/flickity.css");

$page->addjs("https://npmcdn.com/flickity@2/dist/flickity.pkgd.js");

function UserInteraction($apiCall, $buttonText)
{
	global $profile;
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

	$friend = $profile;

	if ($user->id != $friend->id)
	{
		if (!$user->is_friend($friend->id))
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

			if (!$user->is_blocked($friend->id))
				BlockUser();
			else
				UnblockUser();
	}
}

function PrintFriendlistInteractions($friend)
{
	global $profile;
	global $user;

	if (!isset($friend))
		$friend = $profile;

		if ($user->id != $friend->id)
		{
			if (!$user->is_friend($friend->id))
			{
				if ($profile->friend_request_status() == "outgoing")
					CancelRequest();
				else
					AddFriend();
			}
		}
}
$friends = $profile->get_friends_as_array(0, 9);

$mutualFriends = array();

foreach ($friends as $friend)
{
	if ($user->is_friend($friend->id))
		$mutualFriends[] = $friend;
}

$photos = get_photos();

?>
<article id="friends">
	<?php if ($user->id != $profile->id) : ?>
	<div id="mutual-friends">
		<div id="head">Mutual Friends - <?php echo count($mutualFriends); ?></div>
		<div id="body">
			<?php foreach ($mutualFriends as $friend) : ?>
			<a href="/profile/<?php echo $friend->id; ?>/">
				<div class="friend tile">
					<div class="name">
						<?php echo $friend->screen_name; ?>
					</div>
					<div class="profile-img">
						<img src="<?php echo $friend->profile_picture(); ?>"/>
					</div>
					<?php PrintFriendlistInteractions($friend); ?>
				</div>
			</a>		
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="sidebar-container tile">
		<div id="friends">
			<div id="head">Friends - <?php echo $profile->get_friends_count(); ?></div>
			<div id="body">
				<?php foreach ($friends as $friend) : ?>
				<a href="/profile/<?php echo $friend->id; ?>/">
					<div class="friend tile">
						<div class="name">
							<?php echo $friend->screen_name; ?>
						</div>
						<div class="profile-img">
							<img src="<?php echo $friend->profile_picture(); ?>"/>
						</div>
						<?php PrintFriendlistInteractions($friend); ?>
					</div>
				</a>		
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</article>

<?php $page->footer(); ?>