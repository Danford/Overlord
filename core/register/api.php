<?php

    /*
     *  This particular core module doesn't use $apiCall, since it only does one thing.
     *  It's still required to get here, but it doesn't matter what it's set to 
     * 
     */

    $pagedir = $oe_modules['register']."post/" ;
    
    $baseurl = httproot.'register/' ;
    
    $post->checkbox( 'tos_agree' ) ;
    $post->hold( 'screen_name', 'email', 'confirmemail', 'birth_month', 'birth_day', 'birth_year', 'tos_agree',  
            'show_age', 'email_notification', 'invite_notification', 'allow_contact', 'zip' ) ;

    
        $post->require_set( 'screen_name', 'email', 'tos_agree','zip' );
        $post->require_date( 'birth', "You must enter your birthdate." );
        $post->require_checked('tos_agree', 'You must agree to the terms of service' ) ;
         
        $post->require_true( preg_match( "/[\\\\! '@#$%^&*\\(\\)\\[\\]\\{\\}`~\\.,\\/<>;:+=]/", $_POST['screen_name']) == 0 , 
                                    'screen_name', "Screen name contains invalid characters." ) ;
        
        $post->require_true( preg_match( "/[\\\\! '#$%^&*\\(\\)\\[\\]\\{\\}`~,\\/<>;:=]/", $_POST['email'] ) == 0, 'email', "E-mail contains invalid characters." ) ;

        $post->require_true( strlen( $_POST['password'] ) > 7 , 'password', 'Password is too short.') ;
        $post->require_true( preg_match('/[A-Z]/', $_POST['password']), 'password', 'Password does not contain an uppercase character.' ) ;
        $post->require_true( preg_match('/[a-z]/', $_POST['password']), 'password', 'Password does not contain a lowercase character.' ) ;
        $post->require_true( preg_match("/[0-9\\\\! '@#$%^&*\\(\\)\\[\\]\\{\\}`~\\.,\\/<>;:+=\\-_]/", $_POST['password']), 'password', 'Password does not contain a number or special character.' ) ;
        $post->require_true( verify_number( $_POST['zip'] ), 'zip', 'Invalid Zip Code' ) ;
        
    $post->checkpoint() ;

        $post->require_true( $_POST['password'] == $_POST['confirmpassword'], 'password', 'Password confirmation does not match' ) ;
    
        $post->require_true( preg_match( '/.*@.*\..*/', $_POST['email'] ), 'email', 'Not a valid e-mail address' ) ;
    
    $post->checkpoint() ;
    
        $post->require_true( $_POST['email'] == $_POST['confirmemail'] , 'email', 'E-mail confirmation does not match.' ) ;

    $post->checkpoint() ;
    
        if( strlen($_POST['birth_day']) == 1 ){ $_POST['birth_day'] = "0".$_POST["birth_day"] ; }
    
        $_POST["birthdate"] = $_POST['birth_year']."-".$_POST['birth_month']."-".$_POST['birth_day'] ;
        
        $date = new DateTime();
        $date->sub(new DateInterval('P18Y'));
    
        $post->require_true( $_POST["birthdate"] <= $date->format('Y-m-d'), "birth",  "You must be at least 18 years old to register." ) ;
        $post->require_true( preg_match( '/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $_POST['birthdate'] ) == 1, 'birth', 'There is a serious problem with your date submission.' ) ;        
        
    $post->checkpoint() ;
    
        $usercheck = "SELECT COUNT(*) FROM `profile` WHERE `screen_name`='".$_POST['screen_name']."'" ;
    
        $post->require_true( $db->get_field( $usercheck ) == 0 , 'screen_name', 'Screen Name is already in use.' ) ;
    
    $post->checkpoint() ;
    
        $usercheck = "SELECT COUNT(*) FROM `user_account` WHERE `email`='".$_POST['email']."'" ;
    
        $post->require_true( $db->get_field( $usercheck ) == 0 , 'email', 'There is already an account with this e-mail.' ) ;
    
    $post->checkpoint() ;
    
        $locationcheck = "SELECT COUNT(*) FROM `location_zip` WHERE `zip`='".$_POST['zip']."'" ;
        
        $post->require_true( $db->get_field( $locationcheck ), 'zip', 'Invalid Zip Code.' ) ;
        
    $post->checkpoint() ;
    
        /*
         *  The following options shouldn't be a problem, actually, but these checks are to avoid
         *  sql injection smartasses bypassing the form.
         */
    
        foreach( array( 'show_age', 'email_notification', 'invite_notification', 'allow_contact' ) as $field ) {
            
            if( $_POST[$field] != 0 and $_POST[$field] != 1 ) {
                
                die( "Stop That." ) ;
            }
            
        }
        
        /*
         *  Data has passed verification
         */

    $a['status'] = '0' ;
    $a['passhash'] = hash_hmac( "sha256", $_POST['password'], oe_seed ) ;
    
    $set1a = $db->build_set_string_from_post( 'email', 'email_notification', 'invite_notification' ) ;
    $set1p = $db->build_set_string_from_post( 'screen_name', 'birthdate', 'show_age', 'allow_contact' ) ;
    $set2 = $db->build_set_string_from_array( $a ) ;
    
    $b['user_id'] = $db->insert( "INSERT INTO `user_account` SET ".$set1a.", ".$set2 ) ;
    $b['confirmation_key'] = hash_hmac( "sha256", $_POST['email'].oe_time(), oe_seed );
    $b['type'] = 0 ;
    $b['timestamp'] = oe_time() ;
    
    $db->insert( "INSERT INTO `profile` SET ".$set1p.", `user_id`='".$b['user_id']."'" ) ;
    $db->insert( "INSERT INTO `confirmation_key` SET ".$db->build_set_string_from_array($b) ) ;
    $db->insert( "INSERT INTO `user_location` SET `user_id`='".$b["user_id"]."', `zip`='".$db->sanitize($_POST['zip'])."', `primary`='1'" ) ;

    include( oe_lib."email_minion.php" ) ;
    include( oe_config."email.conf.php" ) ;
    
    $mailer->to( $_POST['email'] ) ;
    $mailer->subject = $oe_mail['reg']['subject'] ;  // this is in /core/_conf/email.conf
    $mailer->from = $oe_mail['reg']['address'] ;
    
    $mailer->body = str_replace( array( '%%USERID%%','%%KEY%%'), array( $b['user_id'], $b['confirmation_key']), $oe_mail['reg']['message']) ;
    
    if ( $mailer->send() ) {
    
        header( "Location: ".$baseurl."confirmation_sent" ) ;
        die() ;
    } else {
        
        print( "Failure." ) ;
        die() ;
    }