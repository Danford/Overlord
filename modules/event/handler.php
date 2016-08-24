<?php


$user->require_login() ;

$pagedir = $oe_modules['event']."pages/" ;

// pages not specific to a group

if( ! isset( $uri[$pos] ) or $uri[$pos] == "" or $uri[ $pos - 1 ] == 'events' ) {

    // the main page of the events section

    
    // include( $pagedir."main.php" ) ;
    die() ;
} 

if( $uri[$pos] == "create" ) {
    include( $pagedir."create.php" ) ;
    die() ;
}

verify_number( $uri[$pos] ) ;

include( $oe_modules['event']."lib/event_minion.php" ) ;

$event = new event_minion( $uri[$pos] ) ;

if( $event->group == null ) {
 
    // group events go through the group module handler !!
    
    if( $event->access != false ){
    
        // at the least, they can SEE the event
    
        $pos++ ;
    
        if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ){
    
            include( $pagedir."profile.php" ) ;
            die();
        }
        
        if( $user->id == $event->organiser ) {
            
            switch( $uri[$pos] ){
                
                case 'edit':
                    
                    include( $pagedir."edit.php" ) ;
                    die() ;
                    
                break ;
                
                case 'invite_moderation':
                    
                    include( $pagedir."invite_moderation.php" );
                    die() ;
                    
                break ;
                
            }
        }
        
        switch( $uri[$pos] ){
            
            case 'invite':
                
                include( $pagedir."invite.php" ) ;
                die() ;
        }
        
    }
}