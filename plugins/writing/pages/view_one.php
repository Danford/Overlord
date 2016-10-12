<?php
/*
 *      use
 * 
 *      `id`,
 *      `title`,
 *      `subtitle`,
 *      `privacy`, 
 *      `timestamp`,
 *      `last_updated`
 *      
 *      
 * 
 *  include( $oe_plugins['writing'].'conf/plugin.conf' ) before invoking likes and comments plugins.
 * 
 * 
 * 
 */

include(oe_frontend."page_minion.php");
include(oe_lib."form_minion.php");

$page = new page_minion("Writing - ".$writing['title'].' - '.$writing['subtitle']);

$page->header();

?>
<article id="writing">
	<div id="title"><h2><?php echo $writing['title']; ?></h2></div>
	<div id="subtitle"><h2><?php echo $writing['subtitle']; ?></h2></div>
	<div id="body">
		<?php echo $writing['copy']; ?>
	</div>
	<div id="writen-on">Writen On: <?php echo $writing['timestamp']; ?></div>
</article>
<?php $page->footer(); ?>