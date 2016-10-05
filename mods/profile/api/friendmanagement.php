<?php

    if( ! verify_number( $POST['user'] ) ){
        $post->json_reply("FAIL") ;
        die( 'invalid user' ) ;
    }


    switch( $apiCall ){
        
        case 'addFriend' :

            $user->load_friends_list() ;
            
            if( ! $user->is_blocked( $_POST['user'] ) ) {
                
                $test = $db->get_assoc( "SELECT `requestor`, `requestee` FROM `profile_friendship_rq`
                                        WHERE 
                                        ( `requestor`='".$user->id."' and `requestee`='".$_POST['user']."' )
                                        OR
                                        ( `requestee`='".$user->id."' and `requestor`='".$_POST['user']."' )" ) ;
                
                                        
                if( $test == false ){
                
                    $db->insert( "INSERT INTO `profile_friendship_rq` 
                                        SET `requestor`='".$user->id."', `requestee`='".$_POST['user']."'" ) ;
                    $post->json_reply( 'SUCCESS' ) ;
                
                } elseif( $test['requestor'] == $_POST['user'] ) {
                    
                    // remove friend request
                    
                    $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                        `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'" ) ;
                
                    // add friendship
         
                    $db->insert( "INSERT INTO `profile_friendship` SET 
                        `friend1`='".$user->id."', `friend2`='".$_POST['user']."', `timestamp`='".oe_time()."'" ) ;
                    
                    $post->json_reply( 'SUCCESS', 'Friend added' ) ;
                } else {
                    $post->json_reply( 'ERROR', 'Duplicate request' ) ;
                }
                
            }
            
            $post->json_reply( 'FAIL' ) ;
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'removeFriend' :
            
            $result = $db->update( "DELETE FROM `profile_friendship` WHERE  
                ( `friend1`='".$user->id."' AND `friend2`='".$_POST['user']."' )
                    OR
                ( `friend2`='".$user->id."' AND `friend1`='".$_POST['user']."' )" ) ;
            
            
            if( $result == false or $result == 0 ){
                $post->json_reply( 'FAIL' ) ;
            } else {
            
                $user->load_friends_list() ;
                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$_POST['user']."', `type`='3', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                
                $post->json_reply( 'SUCCESS' ) ;
            }
            
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'confirmFriend' :
            
            // verify that there is a request
            
            if( $db->get_field( "SELECT count(*) FROM `profile_friendship_rq` WHERE 
                `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'") == 1 ){
                
                // remove friend request
                
                $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                    `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'" ) ;
            
                
                // add friendship
     
                $db->insert( "INSERT INTO `profile_friendship` SET 
                    `friend1`='".$user->id."', `friend2`='".$_POST['user']."', `timestamp`='".oe_time()."'" ) ;
     
                $user->load_friends_list() ;
                
                $post->json_reply( 'SUCCESS' ) ;
               
            }
            
            $post->json_reply( 'FAIL' ) ;
            
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;

        case 'denyFriend' :
            
            $result = $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                `requestor`='".$user->id."' AND `requestee`='".$_POST['user']."'" ) ;
            
            if( $result == false or $result == 0 ){
                $post->json_reply( 'FAIL' ) ;
            }
            
            $post->json_reply( 'SUCCESS' ) ;
            
            
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'cancelFriendrq' :
            
            $result = $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'" ) ;
            
            if( $result == false ){
                $post->json_reply( 'FAIL' ) ;
            }
        
            $post->json_reply( 'SUCCESS' ) ;
            
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'blockUser' :
            
            // add block entry 
            
            $db->insert( "INSERT INTO `profile_block` SET `blocker`='".$user->id."' AND `blockee`='".$_POST['user']."'" ) ;
            
            // remove any existing friendship
            
            $result = $db->update( "DELETE FROM `profile_friendship` WHERE  
                ( `friend1`='".$user->id."' AND `friend2`='".$_POST['user']."' )
                    OR
                ( `friend2`='".$user->id."' AND `friend1`='".$_POST['user']."' )" ) ;
 
               //TODO Wipe out comments, likes
            
            $user->load_friends_list() ;
         
            $post->json_reply( 'SUCCESS' ) ;
            
            header( 'Location: /profile/block_list' );
            die() ;
            
        case 'unblockUser' :
            
            // add block entry 
            
            $db->update( "DELETE FROM `profile_block` WHERE `blocker`='".$user->id."' AND `blockee`='".$_POST['user']."'" ) ;
                       
            $post->json_reply( 'SUCCESS' ) ;
            
            header( 'Location: /profile/block_list' );
            die() ;
        
    }