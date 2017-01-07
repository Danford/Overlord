<?php

$tier++ ;


$oepc[$tier]['type'] = 'invitations' ;

if( ! isset( $invitations ) ){

    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging

    $oepc[$tier]['id'] = $lastplugID ;

} else {
    $oepc[$tier]['id'] = $invitations['id'] ;
}

$oepc[$tier]['plugins'] = array() ; // not strictly necessary, as these are inline plugins.