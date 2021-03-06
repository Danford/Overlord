<?php 

/* 
 *          name - string, 75 chars max
 *          short - string, short description, 255 chars max
 *          detail - text/html
 *          privacy - int
 *                  1 - public
 *                  2 - closed (profile is visible, but user must be invited or request to join.
 *                  3 - secret (you can't even see it unless you're invited)
 *                  
 *                  
 *          city    - int, optional
 *                    used for searching, does not limit membership
 *                    
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."create_group_tile.php");

if (!isset($_GET['ajax']))
{
	$page = new page_minion("Create Group");
	
	$page->addjs('/js/tinymce/tinymce.min.js');
	$page->addjs('/js/invoketinymce.js');
	
	$page->header();
}

$form = new form_minion("create", "group");

global $privacyoptions;

$createGroupTile = new CreateGroupTile();
$createGroupTile->Serve();

$form->footer();

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