<?php

/*
 * 
 *  
 *  List of blocked users is just screen name and id, get from $user->get_blocked_as_array() ;
 *  
 *  API is 
 *  
 *          oe_api      profile
 *          oe_call     blockUser | unblockUser
 *          user        userID
 *          
 *          reports SUCCESS or FAIL, no content.
 *          
 *          Submit these three values in a form, and it will do the job and bounce you back to 
 *          /profile/block_user, which is, y'know, HERE.
 *  
 *  
 * 
 * 
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

$page = new page_minion("Blocked List");

$page->header();
$form = new form_minion("profile", "blockUser");

?>
<pre><?php print_r($user->get_blocked_as_array()); ?></pre>
<div id="upload-photo-form">
	<?php $form->header(); ?>
	<p>Privacy:	<?php //$form->select("user", $user->get_friends_as_array()); ?></p>
	<?php $form->submit_button("Block User"); ?>
</div>

<?php 
	$form->footer(); // it's not just cosmetic, it does session cleanup.
    $page->footer();
?>

?>