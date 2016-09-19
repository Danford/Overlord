<?php

    $pagedir = $oe_modules['register']."pages/" ;
    
    if( ! isset( $uri[ $pos ] ) ) {
        $uri[ $pos ] = '' ;
    }
    
    switch( $uri[$pos] ) {
    
        case '':
            include( $pagedir."register.php" );
            die() ;
        case 'confirmation_sent':
            include( $pagedir."confirmationsent.php" );
            die() ;
        case 'activate':
            include( $pagedir."activation.php" );
            die() ;
    
    }
    
    ?>