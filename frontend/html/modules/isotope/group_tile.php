<?php

class GroupTile extends GridTile {

	function __construct ($group) {
		parent::__construct("group", GridOption::StampLeft | GridOption::IgnoreClick);
		
		global $db;
		global $oepc;
		global $tier;
		
		$q = "SELECT `id`, `owner`,`privacy`, `title`, `description`, `timestamp`, `module`, `module_item_id` FROM `".$oepc[$tier]['photo']['view']."`
            WHERE `id`='".$group->avatar."'";
		 
		$photo = $db->get_assoc($q) ;
		
		if( $photo == false )
			echo "Error no photo.";
				
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