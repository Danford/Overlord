<?php

/*  this file sets up the settings for plugins that attach to this module.
 *  
 *  it does not actually configure the module in which it is contained
 *  except to set what plugins are attached to it.
 *  
 *  Since this is a module, it will be accessed on both the front end
 *  and back end for any plugin connected to it, whether directly or indirectly.
 *  
 *  A module HAS to have this file, even if it doesn't configure anything,
 *  to establish basic authorization.
 *  
 */


if( ! isset( $profile ) ){
    
    // this is being accessed via a plugin API
    // it goes here to let the plugin ignore what kind of
    // plug it's plugging
    
    $profile = new profile_minion( $basemoduleID, true ) ;
    
    if( $profile->name == false ){
        
        $post->json_reply("FAIL") ;
        die() ;
    }
    
}

$tier = 0 ;

    /*  all modules declare themselves as $tier 0.  
        this level of configuration sets the privleges for
        every plugin that follows.
        
        A module that can be invoked as a plugin will need 
        to check to see if tier exists and either declare or 
        increment it, then use $tier in the settings below.
        
        To allow for different settings to be used when it is running
        as a plugin or a moudle, just check if $tier is 0.
    
        */


$oepc[0] = [ 'type' => 'profile', 'id' => $profile->id ] ;

     /* at tier 0, this gets referenced at every tier above. */


$oepc[0]['contributor'] = ( $profile->id == $user->id ) ;

    /*  This establishes who can add content via this module.   
     *  This setting is only present-- or at least noticed-- at tier 0 */

$oepc[0]['admin'] = ( $profile->id == $user->id ) ;

    /* This establishes who can administer content.    
     *  This setting is only present-- or at least noticed-- at tier 0  
     *  
     *  In the case of profile, it's the same as above, but I'm commenting here. */

$oepc[0]['plugins'] = [ 'photo', 'writing', 'albums'] ;

    /* What plugins will we try to load?? 
     * Again, this could vary depending on whether it is being invoked
     * as a plugin or module. */


    /* individual plugin config */

//$oepc[0]['photo']['folder'] = oe_root.'oe_images/'  ;
$oepc[0]['photo']['useAlbums'] = false ;
//$oepc[0]['photo']['table'] = 'photo' ;
//$oepc[0]['photo']['view'] = 'photo' ;


//$oepc[0]['writing']['use_albums'] = true ;
//$oepc[0]['writing']['view'] = 'writing_profile' ;

//$oepc[0]['album']['view'] = 'album_profile' ;