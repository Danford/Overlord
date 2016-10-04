<?php


/*
 *      should have 
 *          photo itself  url: photo/id.png
 *              examples:   /profile/1/photo/1.png
 *                          /group/1/event/1/photo/1.png
 *          
 *          title of photo (or not)
 *          description of photo (or not)
 *          
 *          userbox of the poster  ( user profile object in $profile['owner'] )
 *          
 *          delete button if $oepc[0]['admin'] == true ;
 *          
 *          We'll need slightly separate designs for profile, event, and group pictures,
 *          mostly in how the page header looks.  For now, worry about user profile, just be aware 
 *          that there will be some shifting around.          
 *          
 *          // place holders for:
 *          
 *          album (if applicable)
 *          
 *          comment list
 *          
 *          Like count
 *          Like button with api functionality
 *          
 *          
 * 
 * 
 * 
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

$page = new page_minion("Upload Photo");

$page->header();

?>
<article id="photo">
	<div id="title"><h2><?php echo $photo['title']; ?></h2></div>
	<div id="photo">
		<img src="/profile/<?php echo $photo['owner']->id; ?>/photo/<?php echo $photo['id']; ?>.png" />
	</div>
	<div id="description"><?php echo $photo['description']; ?></div>
</article>
<?php $page->footer(); ?>