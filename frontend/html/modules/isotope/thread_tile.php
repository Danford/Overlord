<?php

class ThreadTile extends GridTile {

	function __construct ($thread) {
		parent::__construct("thread", GridOption::Large);
		$date = new DateTime($thread['edited']);
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
?>
		<div id="comment-owner" style="float: left;">
			<h2><?php echo $thread['title']; ?></h2>
			<p><?php echo $thread['owner']->name; ?></p>
			<img src="<?php echo $thread['owner']->profile_thumbnail(); ?>"/>
			<p>City: <?php echo $thread['owner']->city_name(); ?></p>
			<?php if ($thread['owner']->show_age == 1) : ?>
			<p>Age: <?php echo $thread['owner']->age; ?>
			<?php endif; ?>
		</div>
		<div id="date-updated"><?php echo $thread['edited']; ?></div>
		<div id="comment"><?php echo $thread['detail']; ?></div>
<?php
		$this->CloseBuffer();
	}
}
?>