<?php

class FriendsTile extends GridTile {

	function __construct ($profile, $friends) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		
		global $oe_modules;
		require_once($oe_modules['profile']."lib/friends_api.php");
		
		$this->OpenBuffer();
		?>
		<?php //<div class="stamp stamp--left tile"> ?>
			<div id="friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Friends - <?php echo $profile->get_friends_count(); ?></div></a>
				<div id="body">
					<?php foreach ($friends as $friend) : ?>
					<a href="/profile/<?php echo $friend->id; ?>/">
						<div class="profile tile">
							<div class="name">
								<?php echo $friend->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $friend->profile_thumbnail(); ?>"/>
							<?php $friendInteractions = new FriendInteractions($friend); ?>
							<?php $friendInteractions->PrintFriendlistInteractions($friend); ?>
							<div id="details" class="tile hidden">
								<div class="name">
									<?php echo $friend->screen_name; ?>
								</div>
								<img class="loading" onload="ImageLoaded(this)" src="<?php echo $friend->thumbnail(); ?>"/>
								<?php printVarible($friend); ?>
							</div>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}
?>