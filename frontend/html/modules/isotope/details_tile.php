<?php

class DetailsProfileTile extends GridTile {

	function __construct ($profile) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);
		
		$this->OpenBuffer();
		?>
		<?php //<div class="stamp stamp--left tile"> ?>
			<div id="details">
				<p id="age">Age: <?php echo $profile->age; ?></p>
				<?php if ($profile->city_name() == "") : ?>
				<p id="location">City: Not Listed</p>
				<?php else : ?>
				<p id="location">City: <?php echo $profile->city_name() ; ?></p>
				<?php endif; ?>
				<?php if ($gender[$profile->gender]['label'] == "") : ?>
				<p id="gender">Gender: Not Listed</p>
				<?php else : ?>
				<p id="gender">Gender: <?php echo $gender[$profile->gender]['label'] ; ?></p>
				<?php endif; ?>
			</div>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}

class DetailsGroupTile extends GridTile {

	function __construct ($group) {
		parent::__construct(NULL, GridOption::StampLeft | GridOption::IgnoreClick);

		$this->OpenBuffer();
		?>
		<?php //<div class="stamp stamp--left tile"> ?>
			<div id="details">
				<?php if ($group->city_name() == "") : ?>
				<p id="location">City: Not Listed</p>
				<?php else : ?>
				<p id="location">City: <?php echo $group->city_name() ; ?></p>
				<?php endif; ?>
			</div>
		<?php //</div> ?>
<?php
		$this->CloseBuffer();
	}
}

?>