<?php

include($oe_modules['profile']."lib/friends_api.php");
include($oe_modules['group']."lib/group.lib.php");

class InviteTileProfile extends GridTile {

	function __construct ($profile) {
		parent::__construct(NULL, GridOption::StampLeft);

		if ($profile->avatar == 0)
			$this->AddContent("<img class='loading' onload='ImageLoaded(this)' src='/img/nophoto.php'>");
		else
			$this->AddContent("<img class='loading' onload='ImageLoaded(this)' src='/profile/". $profile->id ."/photo/". $profile->avatar .".png'>");
		
		$this->AddTag("div", array("id" => "name"))->AddContent($profile->name);
		
		if ($profile->show_age == 1)
			$this->AddTag("div", array("id" => "age"))->AddContent($profile->age);
		
		$this->AddTag("div", array("id" => "gender"))->AddContent($profile->gender);
		$this->AddTag("p")->AddContent("New Friend Request.");
		$friendInteractions = new FriendInteractions($profile);
		$this->OpenBuffer();
		$friendInteractions->PrintFriendRequestInteractions();
		$this->CloseBuffer();
	}
}

class InviteTileGroup extends GridTile {

	function __construct ($group) {
		parent::__construct(NULL, GridOption::StampLeft);

		if ($group->avatar == 0) {
			$this->AddContent("<img class='loading' onload='ImageLoaded(this)' src='/images/noavatar.png'>");
		} else {
			$this->AddContent("<img class='loading' onload='ImageLoaded(this)' src='/profile/". $profile->id ."/photo/". $profile->avatar .".png'>");
		}

				$this->AddTag("div", array("id" => "name"))->AddContent($group->name);

				$this->AddTag("div", array("id" => "short"))->AddContent($group->short);
				$this->AddTag("p")->AddContent("New Group Invite.");
				$this->OpenBuffer();
				PrintGroupInvitationActions($group);
				$this->CloseBuffer();
	}
}
?>