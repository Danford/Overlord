<?php

    $id = $uri[$pos + 1 ] ;
    $key = $uri[$pos + 2 ] ;


// validate that id and key are a number and a 32digit hexadecimal key, respectively

    $keyregexp = '/^[0-9a-f]{64}$/' ;
    
    if( verify_number( $id ) == false or preg_match( $keyregexp, $key) == 0 ) {
        include( $pagedir."activationfail.php" ) ;
        die() ;
        
    }
        
// verify that it's a valid key & id

    $q = "SELECT COUNT(*) FROM `confirmation_key` WHERE `user_profile`='".$id."' and `confirmation_key`='".$key."' and `type`='0'" ;
    
    if( $db->get_field( $q ) == 0 ){
        include( $pagedir."activationfail.php" ) ;
        die() ;
        
    }

// at this point, we have an valid id/key combo    

       
    if( $db->update( "UPDATE `user_account` SET status='1', date_verified='".oe_time()."', ip_verified='".$_SERVER['REMOTE_ADDR']."' WHERE `user_id`='".$id."'" ) == 0 )
    { 

        die("yo") ;include( $pagedir."activationfail.php" ) ; }
    
    $db->update( "DELETE FROM `confirmation_key` WHERE `user_profile`='".$id."' and `confirmation_key`='".$key."' and `type`='0'" ) ;
    
    
include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( "Registration" ) ;

$page->header() ;

?>

	Your account has been activated!  You can now <a href="/login">log in</a>.



<?php $page->footer() ; ?>