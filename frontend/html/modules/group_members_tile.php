<?php

class GroupMembersTile extends GridTile {

	function __construct ($group, $memberGroups) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		$this->OpenBuffer();
		?>
		<?php //<div class="stamp stamp--left tile"> ?>
		<?php foreach ($memberGroups as $groupName => $members) : ?>
			<?php if (count($members) == 0) continue; ?>
			<div id="members">
				<a href="/group/<?php echo $group->id; ?>/members/"><div id="head"><?php echo $groupName; ?> - <?php echo count($members); ?></div></a>
				<div id="body">
				
					<?php foreach ($members as $member) : ?>
					<a href="/profile/<?php echo $member->id; ?>/">
						<div class="member tile">
							<div class="name">
								<?php echo $member->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $member->profile_picture(); ?>"/>
							<?php //PrintFriendlistInteractions($member); ?>
						</div>
					</a>		
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}
?>