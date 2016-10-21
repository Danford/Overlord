<?php
/*
 *  $page is provided
 * 
 *  use command get_threads( $start, $limit )
 * 
 *  or API call getThreads
 *  
 *  
 *          either will give you array of:
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
 *              `last_updated`, timestamp
 *              
 *              
 */

echo 'list_threads.php';

$threads = get_threads();

?>

<pre><?php print_r($threads); ?></pre>