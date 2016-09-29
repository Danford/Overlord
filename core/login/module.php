<?php

$pagedir = $oe_modules['login']."pages/" ;

switch( $uri[$pos] ) {
    
    case './final' :
        include( $pagedir."login.php" ) ;
        die() ;
        
    case 'passwordreset' :
        include( $pagedir."passwordreset.php" ) ;
        die() ;
        
    case 'passwordresetrequest' :
        include( $pagedir."passwordresetrequest.php" ) ;
        die() ;
        
    case 'resetpending' :
        include( $pagedir."resetpending.php" ) ;
        die() ;   
    
}