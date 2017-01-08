<?php

class AboutMeGroupTile extends GridTile {

	function __construct ($group, $is_mod) {
		parent::__construct(NULL, GridOption::Large);
		$this->AddField("id", "about-me");
		$date = new DateTime($group->edited);		
		$this->OpenBuffer();
?>
		<?php //<div id="about-me" class="grid-item grid-item--large tile flex-main"> ?>
			<?php if ($group->short == "" && $is_mod) : ?>
			<p>This group doesn't yet have an "About Me" section. Add it by editing your group.<p>
			<?php elseif ($group->short == "") : ?>
			<p>This group has not yet provided anything for the "About Me" section of thier profile.</p>
			<?php else : ?>
			<p><?php echo $group->short; ?></p>
			<?php endif; ?>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}

class AboutMeProfileTile extends GridTile {

	function __construct ($profile) {
		parent::__construct(NULL, GridOption::Large);
		$this->OpenBuffer();
		
		global $user;
?>
		<?php //<div id="about-me" class="grid-item grid-item--large tile about-me"> ?>
			<?php if ($profile->detail == "" && $profile->id == $user->id) : ?>
			<p>This profile doesn't yet have an "About Me" section. Add it by editing your profile.<p>
			<a href="/profile/"><div class="button">EditProfile</div></a>
			<?php elseif ($profile->detail == "") : ?>
			<p>This user has not yet provided anything for the "About Me" section of thier profile.</p>
			<?php else : ?>
			<div id="excerpt">
				<?php if (print_words($profile->detail, 200)) : ?>
				<p>Click to read more.</p>
				<?php endif; ?>
			</div>
			<div id="full" class="hidden"><?php echo $profile->detail; ?></div>
			<?php endif; ?>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}

?>