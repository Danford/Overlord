<?php

$tier++ ;

$oepc[$tier]['type'] = '%%%%%%%%%%%%%%%PLUGIN%%%%%%%%%%%%' ;

if( ! isset( $PLUGIN_IDENTIFIER ) ){

    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging

    $oepc[$tier]['id'] = $lastplugID ;

} else {
    $oepc[$tier]['id'] = $PLUGIN_IDENTIFIER['id'] ;
}

$oepc[$tier]['plugins'] = [] ;