<?php

include_once($oe_modules['profile']."lib/friends_api.php");
include_once($oe_modules['group']."lib/group.lib.php");

class InvitableTileGroup extends GridTile {

	function __construct ($invitable, $group) {
		parent::__construct(NULL, GridOption::None);

		$this->OpenBuffer();
		?>
		
		<a href="/profile/<?php echo $invitable->id; ?>/">
			<div class="name">
				<?php echo $invitable->screen_name; ?>
			</div>	
			<img src="<?php echo $invitable->profile_picture(); ?>"/>
		</a>	
		<?php PrintGroupInteractions($group, $invitable); ?>
		
		<?php 
		$this->CloseBuffer();
	}
}
?>