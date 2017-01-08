<?php

class FriendsTile extends GridTile {

	function __construct ($profile, $friends) {
		parent::__construct(NULL, GridOption::StampLeft);
		
		$this->OpenBuffer();
		?>
		<?php //<div class="stamp stamp--left tile"> ?>
			<div id="friends">
				<a href="/profile/<?php echo $profile->id; ?>/friends/"><div id="head">Friends - <?php echo $profile->get_friends_count(); ?></div></a>
				<div id="body">
					<?php foreach ($friends as $friend) : ?>
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
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}
?>