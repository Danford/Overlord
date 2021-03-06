<?php 

/*
 *          group_id - int 
 *          name - string, 75 chars max
 *          short - string, short description, 255 chars max
 *          detail - text/html
 *          privacy - int
 *                  1 - public
 *                  2 - closed (profile is visible, but user must be invited or request to join.
 *                  3 - secret (you can't even see it unless you're invited)
 *                  
 *                  // THIS CANNOT BE LOWERED FROM PREVIOUS LEVEL OF PRIVACY.
 *                  
 *          city    - int, optional
 *                    used for searching, does not limit membership
 *                    
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

if (!isset($_GET['ajax']))
{
	$page = new page_minion("Edit Group");

	$page->addjs('/js/tinymce/tinymce.min.js');
	$page->addjs('/js/invoketinymce.js');

	$page->header();
}

$form = new form_minion("edit", "group");

global $privacyoptions;
?>

<div id="edit-group-form">
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