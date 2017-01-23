<?php

include_once(oe_isotope. "add_comment_tile.php");
include_once(oe_isotope. "comment_tile.php");

include_once($oe_plugins['comment']."conf/conf.php");
include_once($oe_plugins['comment']."lib/comment.lib.php");

class PhotoTile extends GridTile {
	function __construct ($photo, $isThumbnail = TRUE) {
		parent::__construct("photo", GridOption::None);
		$date = new DateTime($photo['timestamp']);
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
		
		global $user;
		global $oepc;
		global $tier;
?>
		<div id="expand-button"></div>
		
		<?php if ($photo['owner']->id == $user->id) : ?>
		<div id="config-button" data-url="/<?php echo $photo['module']; ?>/<?php echo $photo['module_item_id']; ?>/photo/<?php echo $photo['id']; ?>/edit/"></div>
		<?php endif; ?>
		
		<div id="title"><h3><?php echo $photo['title']; ?></h2></div>
		<div id="main-img"><img class="loading" onload="ImageLoaded(this)" src="/<?php echo $photo['module']; ?>/<?php echo $photo['module_item_id']; ?>/photo/<?php echo $photo['id']; ?><?php echo $isThumbnail == true ? ".thumb" : ""; ?>.png" /></div>
		<div id="description"><p><?php echo $photo['description']; ?></p></div>
<?php

		$this->CloseBuffer();
		
		$tier++;
		$oldOepc = $oepc;
		
		$oepc[$tier]['id'] = $photo['id'];
		$oepc[$tier]['type'] = "photo";
		
		$comments = get_comments();
		
		foreach ($comments as $comment) {
			$commentTile = new CommentTile($comment);
			$commentTile->SetNoGrid();
			$this->AddTag("div")->AddElement($commentTile);
		}
			
		$addCommentTile = new AddCommentTile("photo", $photo['id']);
		$addCommentTile->SetNoGrid();
		$this->AddTag("div")->AddElement($addCommentTile);
		
		$oepc = $oldOepc;
		$tier--;
	}
}
?>