<?php

    $pagedir = $oe_modules['register']."post/" ;
    
    $baseurl = httproot.'register/' ;
    
    switch( $apiCall ) {
    
        case "start":
            include( $pagedir.'register.php' ) ;
            die() ;
    
    
    }