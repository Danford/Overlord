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

$oepc[$tier]['plugins'] = [] ; // comments doesn't need to be declared here

$oepc[$tier]['comment']['page'] = true ;