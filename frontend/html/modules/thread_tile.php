<?php

class ThreadTile extends GridTile {

	function __construct ($group, $thread) {
		parent::__construct("thread", GridOption::Large);
		$date = new DateTime($thread['edited']);
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
		
?>
		<a href="/group/<? echo $group->id; ?>/thread/<?php echo $thread['id']; ?>/">
			<?php //<div class="grid-item--large grid-item tile" data-updated="<?php echo $date->getTimestamp(); ?/>"> ?>
				<div id="title"><h2><?php echo $thread['title']; ?></h2></div>
				<div id="date-updated"><?php echo $thread['edited']; ?></div>
				<div id="excerpt"><?php echo $thread['detail']; ?></div>
			<?php //</div> ?>
		</a>
<?php
		$this->CloseBuffer();
	}
}
?>