<?php

class MemberDetails extends GridTile {

	function __construct ($profile) {
		parent::__construct(NULL, GridOption::IgnoreClick | GridOption::NoGrid);
		
		$this->AddField("class", "profile tile ignore-click");
		global $oe_modules;
		require_once($oe_modules['profile']."lib/friends_api.php");
		
		$this->OpenBuffer();
?>
	<a href="/profile/<?php echo $profile->id; ?>/">
		<img class="loading" onload="ImageLoaded(this)" src="<?php echo $profile->profile_thumbnail(); ?>"/>
		<?php $memberInteractions = new FriendInteractions($profile); ?>
		<?php $memberInteractions->PrintFriendlistInteractions($profile); ?>
		<div id="details" class="tile ignore-click hidden">
			<div class="name">
				<?php echo $profile->screen_name; ?>
			</div>
			<img class="loading" onload="ImageLoaded(this)" src="<?php echo $profile->profile_thumbnail(); ?>"/>
			<p>City: <?php echo $profile->city_name(); ?></p>
			<?php if ($profile->show_age == 1) : ?>
			<p>Age: <?php echo $profile->age; ?>
			<?php endif; ?>
		</div>
	</a>
<?php
		$this->CloseBuffer();
	}
}
?>