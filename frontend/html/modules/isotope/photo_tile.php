<?php

class PhotoTile extends GridTile {
	function __construct ($photo) {
		parent::__construct("photo", GridOption::None);
		$date = new DateTime($photo['timestamp']);
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
				
?>
		<div id="expand-button"></div>
		<div id="config-button" data-url="/<?php echo $photo['module']; ?>/<?php echo $photo['module_item_id']; ?>/photo/<?php echo $photo['id']; ?>/edit/"></div>
		<div id="title"><h3><?php echo $photo['title']; ?></h2></div>
		<div id="main-img"><img class="loading" onload="ImageLoaded(this)" src="/<?php echo $photo['module']; ?>/<?php echo $photo['module_item_id']; ?>/photo/<?php echo $photo['id']; ?>.thumb.png" /></div>
		<div id="description"><p><?php echo $photo['description']; ?></p></div>
<?php
		$this->CloseBuffer();
	}
}
?>