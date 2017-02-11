<?php

include_once(oe_isotope. "add_comment_tile.php");
include_once(oe_isotope. "comment_tile.php");
include_once(oe_isotope. "member_details.php");

include_once($oe_plugins['comment']."conf/conf.php");
include_once($oe_plugins['comment']."lib/comment.lib.php");

include_once($oe_plugins['like']."conf/conf.php");
include_once($oe_plugins['like']."lib/like.lib.php");

class PhotoTile extends GridTile {
	function __construct ($photo, $isThumbnail = TRUE) {
		parent::__construct("photo", GridOption::None);

		global $user;
		
		$date = new DateTime($photo['timestamp']);
		$this->AddField("data-date", $date->getTimestamp());
		
		$profileDetails = new MemberDetails($photo['owner']);
		
		$this->OpenBuffer();
		
?>
		<div id="expand-button"></div>
		
		<?php if ($photo['owner']->id == $user->id) : ?>
		<div id="config-button" data-url="/<?php echo $photo['module']; ?>/<?php echo $photo['module_item_id']; ?>/photo/<?php echo $photo['id']; ?>/edit/"></div>
		<?php endif; ?>
		<?php ?>
		<?php $profileDetails->Serve(); ?>
		<div id="title"><h3><?php echo $photo['title']; ?></h2></div>
		<div id="main-img"><img class="loading" onload="ImageLoaded(this)" src="/<?php echo $photo['module']; ?>/<?php echo $photo['module_item_id']; ?>/photo/<?php echo $photo['id']; ?><?php echo $isThumbnail == true ? ".thumb" : ""; ?>.png" /></div>
		<div id="description"><p><?php echo $photo['description']; ?></p></div>
		<?php CreateLikeButton($photo['module'], $photo['module_item_id'], "photo", $photo['id']); ?>
		<div id="comment-button">Comment</div>
		<div id="share-button"></div>
		<?php DisplayLikes($photo['module'], $photo['module_item_id'], "photo", $photo['id']); ?>
		
<?php
		$this->CloseBuffer();

		$comments = get_comments(0, 100, $photo['module'], $photo['module_item_id'], "photo", $photo['id']);
		
		if (count($comments) > 0) {		
			foreach ($comments as $comment) {
				$commentTile = new CommentTile($comment);
				$commentTile->SetNoGrid();
				$this->AddTag("div")->AddElement($commentTile);
			}
		}

		$addCommentTile = new AddCommentTile("photo", $photo['id'], $photo['module'], $photo['module_item_id']);
		$addCommentTile->SetNoGrid();
		$this->AddTag("div")->AddElement($addCommentTile);
		
	}
}
?>