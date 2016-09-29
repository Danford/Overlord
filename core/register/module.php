<?php

    $pagedir = $oe_modules['register']."pages/" ;
        
    switch( $uri[$pos] ) {
    
        case './final':
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