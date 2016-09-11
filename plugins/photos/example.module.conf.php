<?php 
die("Don't Copy This.");

    /*
     * 
     * This info may be included in the plugin.conf.php of any plug that calls it.
     * 
     * A module may explicitly user 0 instead of $tier.
     * 
     * 
     * 
     */
    


$plug[$tier]['photo']['folder'] = oe_root.'oe_images/'  ;

    // This dictates where on the server the folder is uploaded.
    // optional; default is defined at the top of plugin.php
        


$plug[$tier]['photo']['use_albums'] = true ;

    // include album functionality?  optional, default is true
    
$plug[$tier]['photo']['table'] = 'photo' ;

    // the table where the info about the photo is kept.  optional, default is photo
    
$plug[$tier]['photo']['table'] = 'photo' ;

    // the table or view from which to retrieve it.  optional, default is photo