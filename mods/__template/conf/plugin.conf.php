<?php

if( ! isset( $MODULEOBJECT ) ){

    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging

    $MODULEOBJECT = new MODULE_MINON( $basemoduleID ) ;

    if( $MODULEOBJECT_IS_INVALID ){

        $post->json_reply("FAIL") ;
        die() ;
    }

}

$tier = 0 ;