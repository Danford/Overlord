<?php

require_once(oe_lib."form_minion.php");

class UploadWritingTile extends GridTile {

	function __construct ($writing = NULL) {
		parent::__construct(NULL, GridOption::Large | GridOption::Destructable);
		
		
		if(isset($writing)){
		
			$pagetitle = 'Edit Writing';
			$form = new form_minion('edit', 'writing');
			$form->fill_with_values($writing);
		} else {
			$pagetitle = 'Upload Writing';
			$form = new form_minion('write', 'writing');
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
		
		<?php if (isset($writing)) : ?>
		<div id="close-button"></div>
		<div id="expand-button"></div>
		<?php endif; ?>
		<h2><?php echo $pagetitle; ?></h2>
		<?php $form->header(); ?>
		<?php $form->hidden("writing_id", $writing['writing_id']); ?>
		<p>Privacy:	<?php $form->select("privacy", $privacyoptions); ?></p>
		<p>Title: <?php $form->text_field("title"); ?></p>
		<p>Subtitle: <?php $form->text_field("subtitle"); ?></p>
		<p>Copy:</p>
		<p><?php $form->textarea_field("copy"); ?></p>
		
		<?php $form->submit_button("Submit Writing"); ?>
	    <?php $form->footer(); ?>
		<?php 		
		$this->CloseBuffer();
		
		
	}
}
?>