<?php

/*
 *  title - 75 chars max
 *  detail  - text/html
 *  
 *  if admin, include:
 *  
 *  sticky 0 or 1
 *  locked 0 or 1
 * 
 * 
 * 
 * 
 * 
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

if (!isset($_GET['ajax']))
{
	$page = new page_minion("Create Thread");
	
	$page->addjs('/js/tinymce/tinymce.min.js');
	$page->addjs('/js/invoketinymce.js');
	
	$page->header();
}

$form = new form_minion("create", "thread");

global $privacyoptions;
?>

<div id="create-group-form">
	<?php $form->header(); ?>
	<p>Name: <?php $form->text_field("title"); ?></p>
	<p><?php $form->textarea_field("detail"); ?></p>
	<?php if ($group->membership == 2) : ?>
	<p>Sticky: <?php $form->checkbox("sticky"); ?></p>
	<p>Locked: <?php $form->checkbox("locked"); ?></p>
	<?php endif; ?>
	
	<?php $form->submit_button("Create Thread"); ?>
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
