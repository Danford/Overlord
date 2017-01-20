<?php

class HeadGroupTile extends GridTile {

	function __construct ($group) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		
		global $user;
		
		$this->OpenBuffer();
?>
		<?php //<div class="stamp stamp--left tile"> ?>
			<p id="name"><?php echo $group->name; ?></p>
			<?php $this->PrintImgHtml($group->id, $group->avatar); ?>
			<?php if ($group->check_membership($user->id) <= 1): ?>
			<a href="edit"><div class="button">Edit Group</div></a>
			<?php endif; ?>
			<?php if ($group->check_membership($user->id) <= 2): ?>
			<a href="/group/<?php echo $group->id; ?>/invitations/"><div class="button">Invite</div></a>
			<?php endif; ?>
		<?php //</div> ?>		
<?php
		$this->CloseBuffer();
	}
}

class HeadProfileTile extends GridTile {

	function __construct ($profile) {
		parent::__construct(NULL, GridOption::StampLeft);

		$this->OpenBuffer();
?>
		<?php //<div class="stamp stamp--left tile"> ?>
			<p id="name"><?php echo $profile->screen_name; ?></p>
			<?php $this->PrintImgHtml($profile->id, $profile->avatar); ?>
			<?php $friendInteractions = new FriendInteractions($profile); ?>
			<?php $friendInteractions->PrintUserInteractions(); ?>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}

?>