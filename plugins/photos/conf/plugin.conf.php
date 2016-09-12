<?php

    /*
     *  This configuration file does not configure THIS plugin, but
     *  rather any plugins that come after it.
     * 
     * 
     */

    $tier++ ;
    
    /* plugins must have this statement, and refer to $tier where modules flatly declare 0.
     * 
        
        A module that can be invoked as a plugin will need 
        to check to see if tier exists and either declare or 
        increment it, then use $tier in the settings below.
        
        To allow for different settings to be used when it is running
        as a plugin or a moudle, just check if $tier is 0.
    
        
     */

    $oepc[$tier]['type'] = 'photo' ;
    // $oepc[$tier]['id'] = ??? ;  That hasn't been defined in a plugin context.
    
    $oepc[$tier] = [ 'like', 'comment' ] ;
    
        /* I am pretty sure that 'like' will have no fucks to give regarding
         * this configuration file. 
         * 
         * The same can be said of the first generation of 'comments',
         * though configurable comments has potential.
         * 
         * If it turns out that this file is unneccessary, I will remove 
         * the file and any mechanism that accesses it.
         * 
         * 
         */