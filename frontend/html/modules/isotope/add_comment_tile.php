<?php

class AddCommentTile extends GridTile {

	function __construct ($plug_type = NULL, $plug_id = NULL, $mod_type = NULL, $mod_id = NULL) {
		parent::__construct("add-comment", GridOption::Large);
		
		global $oepc;
		global $tier;
		
		$oldOepc = $oepc;
		
		if (isset($mod_type))
			$oepc[0]['type'] = $mod_type;

		if (isset($mod_id))
			$oepc[0]['id'] = $mod_id;
		
		if (isset($plug_type) || isset($plug_id)) {
			$tier++;
			$oepc[$tier]['id'] = $plug_id;
			$oepc[$tier]['type'] = $plug_type;
		}
		
		$this->OpenBuffer();
		

		?>
		<div id="expand-comment">
			<input type="text" value="Write a comment...">
		</div>
				
		<div id="add-comment" class="hidden">
		<?php
				
		$form = new form_minion("addComment", "comment");
		$form->header();
		$form->textarea_field("comment");
		$form->submit_button("Submit Comment");
		$form->footer();
		
		$tier--;
		$oepc = $oldOepc;
		?>
		</div>
		<?php

		$this->CloseBuffer();
		
	}
}
?>