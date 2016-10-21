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

echo 'show_thread.php';