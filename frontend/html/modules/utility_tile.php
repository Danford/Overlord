<?php

class UtilityTile extends GridTile {
	function __construct ($categories) {
		global $user;
		global $oepc;
		
		parent::__construct(NULL, GridOption::StampTop | GridOption::IgnoreClick);
		$this->OpenBuffer();
?>
		<div class="ui-group filters">
		<div class="button-group js-radio-button-group" data-filter-group="category">
			<button class="button is-checked" data-filter="">All</button>
			<?php foreach ($categories as $category) : ?>
			<button class="button" data-filter=".<?php echo $category; ?>"><?php echo ucfirst($category); ?><span class="filter-count"></span></button>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="ui-group sortings">
		<div class="button-group sort-by-button-group">
			<button class="button is-checked" data-sort-by="">None</button>
			<button class="button" data-sort-by="date">Date</button>
			<button class="button" data-sort-by="title">Title</button>
		</div>
	</div>
	<div>
		<button class="button" onclick='AddTile("/<?php echo $oepc[0]['type']; ?>/<?php echo $oepc[0]['id']; ?>/photo/upload/");'>Upload Photo</button>
		<button class="button" onclick='AddTile("/<?php echo $oepc[0]['type']; ?>/<?php echo $oepc[0]['id']; ?>/writing/write/");'>Create Writing</button>
		<button class="button" onclick='AddTile("/group/create");'>Create Group</button>
	</div>
<?php
		$this->CloseBuffer();
	}
}
?>
