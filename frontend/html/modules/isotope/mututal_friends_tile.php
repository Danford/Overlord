<?php

class MutualFriendsTile extends GridTile {

	function __construct ($profile, $mutualFriends) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		
		global $oe_modules;
		require_once($oe_modules['profile']."lib/friends_api.php");
		
		$this->OpenBuffer();
?>
			<div id="mutual-friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Mutual Friends - <?php echo count($mutualFriends); ?></div></a>
				<div id="body">
					<?php foreach ($mutualFriends as $friend) : ?>
					<a href="/profile/<?php echo $friend->id; ?>/">
						<div class="friend tile">
							<div class="name">
								<?php echo $friend->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $friend->profile_picture(); ?>"/>
							<?php $friendInteractions = new FriendInteractions($friend); ?>
							<?php $friendInteractions->PrintFriendlistInteractions($friend); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
<?php
		$this->CloseBuffer();
	}
}
?>