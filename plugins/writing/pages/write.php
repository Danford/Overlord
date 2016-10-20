<?php


/*
 *  apiCall - "write"
 *  
 *      title - string 75 chars max
 *      subtitle - string 25 chars max
 *      copy - text/html
 *      privacy - int
 * 
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

if (!isset($_GET['ajax']))
{
	$page = new page_minion("New Writing");
	
	$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
	$page->addjs( '/js/invoketinymce.js') ;
	
	$page->header();
}

$form = new form_minion("write", "writing");

global $privacyoptions;
?>

<div id="upload-photo-form">
	<?php $form->header(); ?>
	<p>Privacy:	<?php $form->select("privacy", $privacyoptions); ?></p>
	<p>Title: <?php $form->text_field("title"); ?></p>
	<p>Subtitle: <?php $form->text_field("subtitle"); ?></p>
	<p>Copy:</p>
	<p><?php $form->textarea_field("copy"); ?></p>
	
	<?php $form->submit_button("Submit Writing"); ?>
</div>

<?php 
	$form->footer(); // it's not just cosmetic, it does session cleanup.
if (!isset($_GET['ajax']))
{
    $page->footer();
}
else
{
?>
<script>
$(function() {
	tinymce.remove();
	tinymce.init({selector:'textarea'});
	
	$('.grid').isotope('layout');
});
</script>
<?php
}
?>