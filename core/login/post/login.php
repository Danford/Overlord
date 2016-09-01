<?php


    $post->checkbox( 'persist') ;
    
    $post->hold( 'email', 'persist' );
    
    if( $user->login($_POST['email'], $_POST['password'], $_POST['persist'] ) == false ){ 
    
        $post->set_error('login', $user->error ) ;
        $post->checkpoint() ;
    }    
        
    if( $user->last_login == "" ){
     
        // this is their first time logging in.  Send them to the profile editor
        
        header( "Location: /profile" ) ;
        
    }    
    elseif( $_POST["logintype"] == "loginpage" ){
        
        // redirect them to the main page
        
        header( "Location: /" ) ;
        
    } else {
        
        // they were required to login on a restricted page; now we send them back to access it.
        
        header( "Location: ".$_POST["oe_return"] ) ;
    }
    
    die() ;