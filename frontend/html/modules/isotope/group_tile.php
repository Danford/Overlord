<?php

class GroupTile extends GridTile {

	function __construct ($group) {
		parent::__construct("group", GridOption::StampLeft | GridOption::IgnoreClick);
				
		$this->OpenBuffer();
?>
		<a href="/group/<?php echo $group->id; ?>/">
				<div id="title"><h3><?php echo $group->name; ?></h2></div>
				<?php echo $this->PrintPhoto($group->avatar, "group", $group->id); ?>
				<div id="excerpt"><?php echo get_words($group->detail, 55); ?></div>
		</a>
<?php
		$this->CloseBuffer();
	}
}
?>