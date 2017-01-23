<?php

require_once(oe_lib."form_minion.php");

class CreateGroupTile extends GridTile {

	function __construct ($group = NULL) {
		parent::__construct(NULL, GridOption::Large | GridOption::Destructable);
		
		
		if(isset($group)){
		
			$pagetitle = 'Edit Group';
			$form = new form_minion('edit', 'group');
			$form->fill_with_values($group);
		} else {
			$pagetitle = 'Create Group';
			$form = new form_minion('create', 'group');
		}

		
		// maybe should have form error handling here? :)
		/*
		$form->has_file();
		$form->if_error('photo', 'photoerror') ;
		$form->if_error('title', 'titleerror') ;
		$form->if_error('description', 'descriptionerrror') ;*/
		
		global $privacyoptions;
		
		$this->OpenBuffer();
		?>
		
		<div id="create-group-form">
			<?php $form->header(); ?>
			<p>Name: <?php $form->text_field("name"); ?></p>
			<p>Description: <?php $form->text_field("short"); ?></p>
			<p>Detail:</p>
			<p><?php $form->textarea_field("detail"); ?></p>
			<p>Privacy:	<?php $form->select("privacy", $privacyoptions); ?></p>
			
			<p>City: Todo</p>
			<?php $form->submit_button("Create Group"); ?>
		</div>

		<?php 
					
		$form->footer();
		
		$this->CloseBuffer();
	}
}
?>