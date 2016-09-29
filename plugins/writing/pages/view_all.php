<?php


/*
 *  include( $oe_plugins['writing']."lib/writing.lib.php" ) ; and use get_writings( $start, $end, $album )
 *  
 *   or writing api getWriting 
 *   
 *   array of:
 *      `id`,
 *      `title`,
 *      `subtitle`,
 *      `privacy`, 
 *      `timestamp`,
 *      `last_updated`
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */
include(oe_frontend."page_minion.php");
include($oe_plugins['writing']."lib/writing.lib.php");

$page = new page_minion("Writings");
$page->header();
?>

<pre><?php print_r(get_writings()); ?></pre>
<p>This is the /photos/pages/view_all.php page</p>
<?php $page->footer(); ?>