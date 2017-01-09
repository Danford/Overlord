<?php

class MututalFriendsTile extends GridTile {

	function __construct ($profile, $mututalFriends) {
		parent::__construct(NULL, GridOption::StampLeft);
		
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
							<?php PrintFriendlistInteractions($friend); ?>
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