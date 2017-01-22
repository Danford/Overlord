<?php

include_once(oe_lib."form_minion.php");

class FriendInteractions {
	
	private $profile;
	
	function __construct($profile) {
		$this->profile = $profile;
	}
	
	function UserInteraction($apiCall, $buttonText) {
		$form = new form_minion($apiCall, 'profile');
		$form->header();
		$form->hidden('user', $this->profile->id);
		$form->submit_button($buttonText);
		$form->footer();
	}
	
	function AddFriend() {
		$this->UserInteraction('addFriend', 'Request Friend');
	}
	
	function RemoveFriend() {
		$this->UserInteraction('removeFriend', 'Remove Friend');
	}
	
	function CancelRequest() {
		$this->UserInteraction('cancelFriendrq', 'Cancel Request');
	}
	
	function ConfirmFriend() {
		$this->UserInteraction('confirmFriend', 'Confirm Friend');
	}
	
	function DenyFriend() {
		$this->UserInteraction('denyFriend', 'Deny Request');
	}
	
	function BlockUser() {
		$this->UserInteraction('blockUser', 'Block User');
	}
	
	function UnblockUser() {
		$this->UserInteraction('unblockUser', 'Unblock User');
	}
	
	function PrintFriendRequestInteractions() {
		$this->ConfirmFriend();
		$this->DenyFriend();
	}
	
	function PrintUserInteractions() {
		global $user;
		
		$friend = $this->profile;
	
		if ($user->id != $friend->id) {
			if (!$user->is_friend($friend->id)) {
				if ($this->profile->friend_request_status() == "outgoing") {
					$this->CancelRequest();
				} elseif ($this->profile->friend_request_status() == "incoming") {
					$this->ConfirmFriend();
					$this->DenyFriend();
				}
				else {
					$this->AddFriend();
				}
			} else {
				$this->RemoveFriend();
			}
	
			if (!$user->is_blocked($friend->id)) {
				$this->BlockUser();
			} else {
				$this->UnblockUser();
			}
		}
	}
	
	function PrintFriendlistInteractions($friend)
	{
		global $user;
	
		if (!isset($friend))
			$friend = $this->profile;
	
		if ($user->id != $friend->id) {
			if (!$user->is_friend($friend->id)) {
				if ($this->profile->friend_request_status() == "outgoing") {
					$this->CancelRequest();
				} else {
					$this->AddFriend();
				}
			}
		}
	}
}

?>