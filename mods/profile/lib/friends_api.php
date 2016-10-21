<?php

include_once(oe_lib."form_minion.php");

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

?>