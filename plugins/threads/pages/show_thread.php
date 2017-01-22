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

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."thread_tile.php");
include(oe_isotope."comment_tile.php");

// without this oepc is wrong and comments show the same for every thread.
// I thought this should be getting automatically called but I have no idea.
// where that code is located.
include($oe_plugins['thread']."conf/plugin.conf.php");

include($oe_plugins['comment']."conf/conf.php");
include($oe_plugins['comment']."lib/comment.lib.php");

$page = new page_minion("Group Threads");

$page->header();

$page->js_minion->addFile(oe_js . "isotope.pkgd.min.js");
$page->js_minion->addFile(oe_js . "imagesloaded.pkgd.js");
$page->js_minion->addFile(oe_js . "isotope.js");

$page->addjs('/js/tinymce/tinymce.min.js');
$page->addjs('/js/invoketinymce.js');

$comments = get_comments();

$isotope = new Isotope($page);

$isotope->AddTile(new ThreadTile($thread));

foreach ($comments as $comment)
	$isotope->AddTile(new CommentTile($comment));

$page->footer();

?>

<script>
tinymce.init({
    setup : function(ed) {
        ed.onInit.add(function(ed) {
        	$('.grid').isotope('layout');
        });
    }
});
</script>