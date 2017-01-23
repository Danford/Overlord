<?php

class CommentTile extends GridTile {

	function __construct ($comment) {
		parent::__construct("comment", GridOption::Large);
		$date = new DateTime($comment['edited']);
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
		
?>
		<div id="comment-owner" style="float: left;">
			<p><?php echo $comment['owner']->name; ?></p>
			<img src="<?php echo $comment['owner']->profile_thumbnail(); ?>"/>
			<p>City: <?php echo $comment['owner']->city_name(); ?></p>
			<?php if ($comment['owner']->show_age == 1) : ?>
			<p>Age: <?php echo $comment['owner']->age; ?>
			<?php endif; ?>
		</div>
		<div id="date-updated"><?php echo $comment['edited']; ?></div>
		<div id="comment"><?php echo $comment['comment']; ?></div>
<?php
		$this->CloseBuffer();
	}
}
?>