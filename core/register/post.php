<?php

    $pagedir = $oe_modules['register']."post/" ;
    
    $baseurl = httproot.'register/' ;
    
    switch( $_POST["oe_formid"] ) {
    
        case "start":
            include( $pagedir.'register.php' ) ;
            die() ;
    
    
    }