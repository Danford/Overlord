<?php

if( ! isset( $tier ) ){
    $tier = 0 ;
}

if( ! isset( $event ) ){

    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging

    $event = new event_minion( $basemoduleID ) ;

    if( $event->title == false ){

        $post->json_reply("FAIL") ;
        die() ;
    }

}
