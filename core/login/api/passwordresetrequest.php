<?php

$post->hold( "email" ) ;

$post->require_true( preg_match( "/[\\\\! '#$%^&*\\(\\)\\[\\]\\{\\}`~,\\/<>;:=]/", $_POST['email'] ) == 0, 'email', "E-mail contains invalid characters." ) ;

$post->checkpoint() ;

$post->require_true( preg_match( '/.*@.*\..*/', $_POST['email'] ) != 0, 'email', 'Not a valid e-mail address' ) ;

$post->checkpoint() ;


$a = $db->query( "SELECT `user_id` FROM `user_account` WHERE `email`='".$_POST['email']."'" ) ;


if( $db->num_rows() == 1 ) {

    $confirmation_key = hash_hmac( "sha256", $_POST['email'].oe_time(), oe_seed );

    $id = $db->field() ;
    $db->free() ;
    
    $db->insert( "INSERT INTO `confirmation_key` 
                    SET `profile`='".$id."', `confirmation_key`='".$confirmation_key."', type='1', timestamp='".oe_time."'" ) ;
    

    include( oe_lib."email_minion.php" ) ;
    include( oe_config."email.conf.php" ) ;
    
    $mailer->to( $_POST['email'] ) ;
    $mailer->subject = $subject['reset'] ;
    $mailer->from = $address['reset'] ;
    
    $mailer->body = str_replace( array( '%%USERID%%','%%KEY%%'), array( $id, $confirmation_key ), $message["reset"]) ;
    
    if ( ! $mailer->send() ) {
   
        print( "Failure." ) ;
        die() ;
    }
    
}

// note that we send them to the same screen regardless of whether the email is a match.
// That way, nobody can use this functionality to see if someone has an account.

header( "Location: ".httproot."login/resetpending" ) ;
die() ;