<?php

if( ! isset( $group ) ){

    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging

    $group = new group_minion( $basemoduleID ) ;

    if( $group->id == false ){

        $post->json_reply("FAIL") ;
        die() ;
    }
}

$tier = 0 ;

$oepc[0] = [ 'type' => 'group', 'id' => $group->id ] ;

/* at tier 0, this gets referenced at every tier above. */


$oepc[0]['contributor'] = ( $group->membership > 0 ) ;

/*  This establishes who can add content via this module.
 *  This setting is only present-- or at least noticed-- at tier 0 */

$oepc[0]['admin'] = ( $group->membership == 2 ) ;

/* This establishes who can administer content.
 *  This setting is only present-- or at least noticed-- at tier 0
 *
 *  In the case of profile, it's the same as above, but I'm commenting here. */

$oepc[0]['plugins'] = [ 'photo', 'writing', 'threads', 'albums', 'invitations' ] ;

/* What plugins will we try to load??
 * Again, this could vary depending on whether it is being invoked
 * as a plugin or module. */

$oepc[0]['invitations']['parentObject'] = $group ;
