<?php 

$user->require_login() ;

$pagedir = $oe_modules['group']."pages/" ;

// pages not specific to a group

if( ! isset( $uri[$pos] ) or $uri[$pos] == "" or $uri[ $pos - 1 ] == 'groups' ) {

    // the main page of the groups section

    include( $pagedir."main.php" ) ;
    die() ;
}

switch( $uri[$pos]  ) {
    
    case "create":
        include( $pagedir."create.php" );
        die();

}


// group-specific pages

if( preg_match( '/^[0-9]*$/', $uri[$pos]) != 0 ){    
    
    $group = new group_minion( $uri[$pos] );
    
    if( $group->access != false ){
        
        // at the least, they can SEE the group
        
        $pos++ ;
        
        if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ){

            include( $pagedir."profile.php" ) ;
            die();
        }
        
        if( $group->is_owner() ){
            
            switch( $uri[$pos] ){
                
                case "edit":
                    
                    $info = array();
                    $info['name'] = $group->name ;
                    $info['short_desc'] = $group->short_desc ;
                    $info['detail'] = $group->detail ;
                    $info['type'] = $group->type ;
                    
                    include( $pagedir."editprofile.php" );
                    die() ;
            }
            
            
        }
        
        
        if( $group->is_moderator() ){
            
            switch( $uri[$pos] ){
                
                case 'banned':
                    
                    $pos++ ;
                    
                    if( ! isset( $uri[$pos] ) or $uri[$pos] == '' ) {

                        $page = 1 ;
                        include( $pagedir.'banned_members.php' ) ;
                        die() ;
                        
                    } elseif( $uri[$pos] == "page" ) { 
                        
                        $pos++ ;
                        
                        if( isset( $uri[$pos] ) and preg_match( '/^[0-9]*$/', $uri[$pos]) != 0  ){
                    
                            $page = $uri[$pos] ;
                            include( $pagedir.'banned_members.php' ) ;
                            die() ;
                       }
                    }
                
                break;
                
                case 'invite_moderation':
                    
                    include( $pagedir.'invite_moderation.php') ;
                    die();
                
                case 'request_moderation':
                    
                    include( $pagedir.'request_moderation.php') ;
                    die();
                    
            }
        }
        
        if( $group->access == "full" or $group->access == "read" ){
            
            // they can access member lists, thread lists, and messages
            // note that they will not be able to post messsages unless
            // they have full access
            
            switch( $uri[$pos] ){
                
                case "threads":
                
                    $pos++ ;
                    
                    //TODO This should be keyed off "page" like other things, and move the 'get_thread_list' to the page itself.
                    
                    if( ! isset( $uri[$pos] ) or $uri[$pos] == ''  ){
                        $threads = $group->get_thread_list( threads_per_page, 0 ) ;
                        include( $pagedir.'thread_list.php' ) ;
                    } elseif( preg_match( '/^[0-9]*$/', $uri[$pos]) != 0  ){
                        $threads = $group->get_thread_list( threads_per_page, ( $uri[$pos] - 1 ) * threads_per_page ) ;
                        
                        if( count( $threads != 0 ) ) {
                            include( $pagedir.'thread_list.php' ) ;
                        }
                    }
                
                break ;
                    
                case "thread":
                    
                    $pos++ ;
                    
                    if( isset( $uri[$pos] ) and preg_match( '/^[0-9]*$/', $uri[$pos]) != 0  ){
                        
                        // thread id
                        
                        $thread = $group->get_thread( $uri[$pos] ) ;
                        
                        if( $thread != false ){
                            
                            // it's a valid thread
                            
                            $pos++ ;
                            
                            if( ! isset( $uri[$pos] ) or $uri[$pos] == '' ){
                                
                                $messages = $group->get_messages( $thread['thread_id'], messages_per_page ) ;
                                
                                if( $messages != false and count( $messages ) != 0 ){
                                    $allow_reply = ( count( $messages ) == $group->get_message_count($thread['thread_id']) ) ;
                                    $page_number = 1;
                                    include( $pagedir.'show_thread.php' ) ;
                                    die() ;
                                }
                            }
                            elseif( $uri[$pos] == 'page' ) {
                                
                                $pos++ ;
                                
                                if( preg_match( '/^[0-9]*$/', $uri[$pos]) != 0 ){
                                    $page_number = $uri[$pos] ;
                                    $messages = $group->get_messages( $thread['thread_id'], messages_per_page, ( $uri[$pos] - 1) * messages_per_page ) ;
                                    
                                    if( $messages != false and count( $messages ) != 0 ){
                                        
                                        $allow_reply = (( $uri[$pos] - 1) * messages_per_page ) + count( $messages ) == $group->get_message_count($thread['thread_id']) ;
                                        include( $pagedir.'show_thread.php' ) ;
                                        die() ;                                  
                                    }
                                }
                            }
                        } // end valid thread if
                    } // end numeric thread id if                
                break ;
                
                case 'members':
                    
                    $pos++ ;
                    
                    if( ! isset( $uri[$pos] ) or $uri[$pos] == '' ) {

                        $page = 1 ;
                        include( $pagedir.'member_list.php' ) ;
                        die() ;
                        
                        
                    } elseif( $uri[$pos] == "page" ) { 
                        
                        $pos++ ;
                        
                        if( isset( $uri[$pos] ) and preg_match( '/^[0-9]*$/', $uri[$pos]) != 0  ){
                    
                            $page = $uri[$pos] ;
                            include( $pagedir.'member_list.php' ) ;
                            die() ;
                       }
                    }
                    
                break;    
                
            } // end switch user has full/read access to threads   
        } // end if user has full/read access to threads 
        
        if( $group->access == "full" ){
            
            switch( $uri[$pos] ){
                
                case "newthread":
            
                    include( $pagedir."newthread.php" ) ;
                    die();
                    
                case "notifications":
                    
                    $notifications = $db->get_assoc( "SELECT `notify_thread`, `notify_message` FROM `group_members` 
                        WHERE `group_id`='".$group->id."' AND `member_id`='".$user->id."'" ) ;
                    
                    include( $pagedir."notifications.php" ) ;
                    die();
                    
                case "invite" :
                    
                    include( $pagedir.'invite.php' ) ;
                    die() ;
            }
            
            // event display and management
            
            if( $uri[$pos] == "event" ) {
            
                $pos++ ;
            
                if( ! isset( $uri[$pos] ) or $uri[$pos] == "" or $uri[ $pos - 1 ] == 'events' ) {
            
                    include( $pagedir.'group_events.php' ) ;
                    die() ;
                }
            
                if( $uri[$pos] == "create" and $group->is_moderator( $user->id )) {
            
                        include( $oe_modules["event"]."pages/create.php" ) ;
                        die() ;                  
                    
                }
                
                include( $oe_modules["event"]."lib/event_minion.php" ) ;
                
                $event = new event_minion( $uri[$pos] ) ;
            
                if( $event->group == $group->id ){  // can't use one group to get access to another's event!
            
                    $pos++ ;
            
                    if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ) {
            
                        include( $oe_modules["event"]."pages/profile.php" ) ;
                        die() ;
                    }
                }
            }
        }
    } // end if group->access != false
} // end numeric group id if 