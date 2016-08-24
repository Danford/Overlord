<?php
    unset( $_SESSION["user"] ) ;
    session_destroy() ;
    
    if( isset( $_COOKIE["id"] ) and isset( $_COOKIE["token"] ) ) {
        

        
$db->update( "DELETE FROM `persistent_tokens` WHERE `user_id`='".$_COOKIE['id']."' AND `token`='".$_COOKIE['token']."'" ) ;

        setcookie( 'id', '', 0, '/', "www.codexfive.net", true ) ;
        setcookie( 'token', '', 0, '/', "www.codexfive.net", true ) ;
        
    }
    
    header( "Location: /" ) ;
    die() ;