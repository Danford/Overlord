<?php

class WritingTile extends GridTile {

	function __construct ($writing) {
		parent::__construct("writing", GridOption::None);
		$date = new DateTime($writing['last_updated']);	
		$this->AddField("data-date", $date->getTimestamp());
		$this->OpenBuffer();
?>
		<?php //<div class="grid-item tile writing" data-category="writing" data-date="<?php echo $date->getTimestamp(); ?/>">?>
			<div id="title"><h3><?php echo $writing['title']; ?></h2></div>
			<div id="subtitle"><h4><?php echo $writing['subtitle']; ?></h3></div>
			<div id="main-img"><img class="loading" onload="ImageLoaded(this)" src="/images/writingnoimage.png" /></div>
			<div id="excerpt"><?php echo $this->get_words($writing['copy'], 55); ?></div>
			<div id="full" class="hidden"><?php echo $writing['copy']; ?></div>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}
?>