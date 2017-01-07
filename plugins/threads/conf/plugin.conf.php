<?php

$tier++ ;

$oepc[$tier]['type'] = 'thread' ;

if( ! isset( $thread ) ){

    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging

    $oepc[$tier]['id'] = $lastplugID ;

} else {
    $oepc[$tier]['id'] = $thread['id'] ;
}

$oepc[$tier]['plugins'] = [ 'like', 'comment' ] ; // not strictly necessary, as these are inline plugins.

$oepc[$tier]['comment']['page'] = false ;  // all photo comments appear on a single page
$oepc[$tier]['comment']['table'] = 'comment' ;
$oepc[$tier]['comment']['view'] = 'comment' ;

$oepc[$tier]['like']['table'] = 'like' ;
$oepc[$tier]['like']['view'] = 'like' ;