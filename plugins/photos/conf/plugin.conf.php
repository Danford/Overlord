<?php

    /*
     *  This configuration file does not configure THIS plugin, but
     *  rather any plugins that come after it.
     * 
     * 
     */

    $tier++ ;
    
    /* plugins must have this statement, where modules flatly declare 0.
     * 
        
        A module that can be invoked as a plugin will need 
        to check to see if tier exists and either declare or 
        increment it, then use $tier in the settings below.
        
        To allow for different settings to be used when it is running
        as a plugin or a moudle, just check if $tier is 0.
    
        
     */

    $oepc[$tier]['type'] = 'photo' ;
    
    if( ! isset( $photo ) ){
    
        // this is being accessed via a plugin API
        // it goes here to let the plugin ignore what kind of
        // plug it's plugging
    
        $oepc[$tier]['id'] = $lastplugID ;
    
    } else {
        $oepc[$tier]['id'] = $photo['id'] ;
    }
    
    $oepc[$tier]['plugins'] = [ 'like', 'comment' ] ; // not strictly necessary, as these are inline plugins.

    $oepc[$tier]['comment']['page'] = false ;  // all photo comments appear on a single page
    $oepc[$tier]['comment']['table'] = 'comment' ;  
    $oepc[$tier]['comment']['view'] = 'comment' ;  
    
    $oepc[$tier]['like']['table'] = 'like' ;  
    $oepc[$tier]['like']['view'] = 'like' ;  