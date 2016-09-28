<?php


/*
 *   I NEED TO CREATE THE API YOU WILL USE FOR THIS
 * 
 *      urls of thumbnails are "photo/thumb/id.png"
 *      
 *      examples:
 *      
 *          profile/1/photo/thumb.1.png
 *          group/1/photo/thumb.1.png
 *          group/1/event/2/photo/thumb.1.png
 *      
 *          
 * 
 *      get_photos( $start = 0. $end = 9999 ) ;
 *      
 *          returns array
 *          
 *              id
 *              
 *              owner -> profile object
 *              
 *              title
 *              description
 *              privacy
 *              
 *              timestamp
 * 
 * 
 * 
 *      album functionality will be added.  Eventually I would like this page
 *      to let you switch between a view of all photos and a list of albums
 *      and photos that are not in albums.
 * 
 * 
 */
include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");
include($oe_plugins['photo']."lib/photo.lib.php");

$page = new page_minion("Upload Photo");

$page->header();

?>

<p>This is the /photos/pages/view_all.php page</p>
<pre><?php print_r(get_photos()); ?></pre>
<?php $page->footer(); ?>