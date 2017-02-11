<?php

class FriendsTile extends GridTile {

	function __construct ($profile, $friends) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		
		global $oe_modules;
		require_once($oe_modules['profile']."lib/friends_api.php");
		require_once(oe_isotope."member_details.php");
		
		$this->OpenBuffer();
?>
<div id="friends">
	<a href="/profile/<?php echo $profile->id; ?>/friends/">
		<div id="head">Friends - <?php echo $profile->get_friends_count(); ?></div>
	</a>
	<div id="body">
		<?php foreach ($friends as $friend) : ?>
		<?php $memberDetails = new MemberDetails($friend); ?>
		<?php $memberDetails->Serve(); ?>
		<?php endforeach; ?>
	</div>
</div>
<?php
		$this->CloseBuffer();
	}
}
?>