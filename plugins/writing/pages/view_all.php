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

include($oe_plugins['writing']."lib/writing.lib.php");

print_r(get_writings());