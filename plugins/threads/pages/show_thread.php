<?php

/*
 *          When you launch this page, you've got the following to work with.
 *          
 *          DATA
 *          
 *          1) the group object, since group is actually the only thing to use threads for now.  
 *          
 *          2) the array $thread, which contains keys for:
 *          
 *              `id` - int
 *              `title` - string
 *              `owner` - profile object
 *              `detail`, text/html
 *              `sticky`, 1 or 0
 *              `locked`, 1 or 0 
 *              `created`, timestamp
 *              `edited`, timestamp of when 'detail' got changed.
 *              `msgcount`, int  
 *              
 *          3) int $page - the page number as requested by the URL.
 *          
 *          4) $oepc[$tier]['thread']['threads_per_page']
 *          
 *          5) $oepc[$tier]['thread']['comments_per_page']
 * 
 *          API
 *          
 *              if( $oepc[0]['admin'] == true ){
 *              
 *                  have buttons corresponding to the api calls:
 *                      lock
 *                      unlock
 *                      makeSticky
 *                      makeUnsticky
 *                      delete            
 *              }
 *              
 *              if( if $thread['owner']->id == $user->id ){
 *                      delete
 *                      edit (okay, not an api call, but an option )
 *              }
 *              

 *          
 *              for comments:
 *          
 *              include( $oe_plugins['thread']."conf/plugin.php" ;
 *              include( $oe_plugins['comment']."lib/comment.lib.php" ;
 *              
 *              IN THAT ORDER
 *              
 *              then use get_comments( $start, $limit ) ;
 *              
 *              
 * 
 * 
 */

include(oe_frontend."page_minion.php");

include($oe_plugins['comment']."conf/conf.php");
include($oe_plugins['comment']."lib/comment.lib.php");

$page = new page_minion("Group Threads");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");
$page->js_minion->addFile(oe_js . "isotope.js");

$comments = get_comments();

?>
<pre><?php print_r($thread); ?></pre>
<pre><?php print_r($comments); ?></pre>

<article id="thread">
	<div class="grid">
		<div class="grid-sizer--full grid-sizer"></div>
		<?php foreach ($comments as $comment) : ?>
		<?php $date = new DateTime($comment['edited']); ?>
		<div class="grid-item--full grid-item tile" data-updated="<?php echo $date->getTimestamp(); ?>">
			<div id="date-updated"><?php echo $comment['edited']; ?></div>
			<div id="excerpt"><?php echo $comment['detail']; ?></div>
		</div>
		<?php endforeach; ?>
	</div>
</article>
<?php $page->footer(); ?>