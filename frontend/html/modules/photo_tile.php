<?php

class PhotoTile extends GridTile {
	function __construct ($photo) {
		parent::__construct("photo", GridOption::None);
		$date = new DateTime($photo['timestamp']);
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
?>
		<?php //<div class="grid-item tile photo" data-category="photo" data-date="<?php echo $date->getTimestamp(); ?/>"> ?>
			<div id="title"><h3><?php echo $photo['title']; ?></h2></div>
			<img class="loading" onload="ImageLoaded(this)" src="/profile/<?php echo $photo['owner']->id; ?>/photo/<?php echo $photo['id']; ?>.png" />
			<div id="description"><p><?php echo $photo['description']; ?></p></div>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}
?>