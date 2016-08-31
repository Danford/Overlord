<?php

if( ! isset( $_SESSION['reset']['id'] ) or ! isset( $_SESSION['reset']['key'] ) ) {

    // user has come here without going through the proper channels.
    
    include( $oe_module['login']."pages/passwordresetfail.php" ) ;
    die() ;
        
}

$post->require_true( strlen( $_POST['password'] ) > 7 , 'password', 'Password is too short.') ;
$post->require_true( preg_match('/[A-Z]/', $_POST['password']), 'password', 'Password does not contain an uppercase character.' ) ;
$post->require_true( preg_match('/[a-z]/', $_POST['password']), 'password', 'Password does not contain a lowercase character.' ) ;
$post->require_true( preg_match("/[0-9\\\\! '@#$%^&*\\(\\)\\[\\]\\{\\}`~\\.,\\/<>;:+=\\-_]/", $_POST['password']), 'password', 'Password does not contain a number or special character.' ) ;


$post->checkpoint() ;

$post->require_true( $_POST['password'] == $_POST['confirmpassword'], 'password', 'Password confirmation does not match' ) ;

$post->checkpoint() ;

// they've entered a valid password twice.  Time to rock and roll.  

$a = $db->update( "UPDATE `user_account` SET `passhash`='".hash_hmac( "sha256", $_POST['password'], oe_seed )."' 
                WHERE `user_id`='".$_SESSION['reset']['id']."'" ) ;

$db->update( "DELETE FROM `confirmation_key` 
                WHERE `user_profile`='".$_SESSION['reset']['id']."' AND `confirmation_key`='".$_SESSION['reset']['key']."' AND `type`='1'" ) ;


unset( $_SESSION['reset'] ) ;

$_SESSION["oe_form"]["login"]["error"]["login"] = "Your password has been reset." ;

header( "Location: /login" ) ;
die() ;