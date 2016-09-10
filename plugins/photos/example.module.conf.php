<?php 
die("Don't Copy This.");
    // the info should be in the plugin.conf.php of any module that calls it



$pluginconf[]['photo']['use_albums'] = true ;

    // include album functionality?  optional, default is true
    
$pluginconf[]['photo']['table'] = 'photo' ;

    // the table where the info about the photo is kept.  optional, default is photo
    
$pluginconf[]['photo']['folder'] = ul_img_dir ;

    // the path on the server where images are stored. 