<?php
    $default['table'] = 'comment' ;
    $default['view'] = 'comment' ;
    
    
    $default['page'] = false ;  
        // if this were a number it would be posts per page.  Default is all comments on one page.
    
    
    foreach( $default as $setting => $value ){
        if( isset( $oepc[$tier]['comment'][$setting] ) ){
            $oepc[$tier]['comment'][$setting] = $value ;
        }
    }