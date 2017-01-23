<?php

include_once(oe_isotope. "add_comment_tile.php");
include_once(oe_isotope. "comment_tile.php");

include_once($oe_plugins['comment']."conf/conf.php");
include_once($oe_plugins['comment']."lib/comment.lib.php");

class WritingTile extends GridTile {

	function __construct ($writing) {
		parent::__construct("writing", GridOption::None);
		$date = new DateTime($writing['last_updated']);	
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
?>
			<div id="expand-button"></div>
			<div id="config-button" data-url="/<?php echo $writing['module']; ?>/<?php echo $writing['module_item_id']; ?>/writing/<?php echo $writing['id']; ?>/edit/"></div>
			<div id="title"><h3><?php echo $writing['title']; ?></h2></div>
			<div id="subtitle"><h4><?php echo $writing['subtitle']; ?></h3></div>
			<div id="main-img"><img class="loading" onload="ImageLoaded(this)" src="/images/writingnoimage.png" /></div>
			<div id="excerpt"><?php echo $this->get_words($writing['copy'], 55); ?></div>
			<div id="full" class="hidden"><?php echo $writing['copy']; ?></div>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();

		global $user;
		global $oepc;
		global $tier;
		
		$tier++;
		$oldOepc = $oepc;
		
		$oepc[$tier]['id'] = $writing['id'];
		$oepc[$tier]['type'] = "writing";
		
		$comments = get_comments();
		
		if (count($comments) > 0) {
			foreach ($comments as $comment) {
				$commentTile = new CommentTile($comment);
				$commentTile->SetNoGrid();
				$this->AddTag("div")->AddElement($commentTile);
			}
		}
			
		$addCommentTile = new AddCommentTile("writing", $writing['id']);
		$addCommentTile->SetNoGrid();
		$this->AddTag("div")->AddElement($addCommentTile);
		
		$oepc = $oldOepc;
		$tier--;
	}
}
?>