<?php

class GroupMembersTile extends GridTile {

	function __construct ($group, $members) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		
		$this->OpenBuffer();
		?>
			<div id="members">
				<div id="body">
				
					<?php foreach ($members as $member) : ?>
					<a href="/profile/<?php echo $member['profile']->id; ?>/">
						<div class="member tile">
							<div class="name">
								<?php echo $member['profile']->screen_name; ?>
							</div>
							<img class="loading" onload="ImageLoaded(this)" src="<?php echo $member['profile']->profile_thumbnail(); ?>"/>
							<?php //PrintFriendlistInteractions($member); ?>
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