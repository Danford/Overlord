<?php


    switch( $apiCall ){
        
        case 'addFriend' :

            $user->load_friends_list() ;
            
            if( verify_number($_POST['user']) and ! $user->is_blocked( $_POST['user'] ) ) {
                $db->insert( "INSERT INTO `profile_friendship_rq` SET `requestor`='".$user->id."', `requestee`='".$db->sanitize( $_POST['user'] )."'" ) ;
                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$db->sanitize( $_POST['user'] )."', `type`='1', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                
                $post->json_reply( 'SUCCESS' ) ;

            }
            
            $post->json_reply( 'FAIL' ) ;
              
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'removeFriend' :
            
            $result = $db->update( "DELETE FROM `profile_friendship` WHERE  
                ( `friend1`='".$user->id."' AND `friend2`='".$db->sanitize( $_POST['user'] )."' )
                    OR
                ( `friend2`='".$user->id."' AND `friend1`='".$db->sanitize( $_POST['user'] )."' )" ) ;
            
            
            if( $result == false or $result == 0 ){
                $post->json_reply( 'FAIL' ) ;
            } else {
            
                $user->load_friends_list() ;
                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$db->sanitize( $_POST['user'] )."', `type`='3', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                
                $post->json_reply( 'SUCCESS' ) ;
            }
            
            header( 'Location: /profile/'.$db->sanitize( $_POST['user'] ) );
            die() ;
            
        case 'confirmFriend' :
            
            // verify that there is a request
            
            if( $db->get_field( "SELECT count(*) FROM `profile_friendship_rq` WHERE 
                `requestee`='".$user->id."' AND `requestor`='".$db->sanitize( $_POST['user'] )."'") == 1 ){
                
                // remove friend request
                
                $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                    `requestee`='".$user->id."' AND `requestor`='".$db->sanitize( $_POST['user'] )."'" ) ;
            
                
                // add friendship
     
                $db->insert( "INSERT INTO `profile_friendship` SET 
                    `friend1`='".$user->id."', `friend2`='".$db->sanitize( $_POST['user'] )."', `timestamp`='".oe_time()."'" ) ;
     
                // add activity
                
                $st['user_id'] = $user->id ;
                $st['ref'] = $db->sanitize( $_POST['user'] ) ;
                $st['type'] = 1 ;
                $st['timestamp'] = oe_time() ;
                
                $db->insert( "INSERT INTO `user_activity` SET ".$db->build_set_string_from_array( $st ) ) ;
                
                $st['user_id'] = $db->sanitize( $_POST['user'] ) ;
                $st['ref'] = $user->id ;
                
                $db->insert( "INSERT INTO `user_activity` SET ".$db->build_set_string_from_array( $st ) ) ;

                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$db->sanitize( $_POST['user'] )."', `type`='2', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                
                $user->load_friends_list() ;
                
                $post->json_reply( 'SUCCESS' ) ;
               
            }
            
            $post->json_reply( 'FAIL' ) ;
            
            header( 'Location: /profile/'.$db->sanitize( $_POST['user'] ) );
            die() ;

        case 'denyFriend' :
            
            $result = $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                `requestor`='".$user->id."' AND `requestee`='".$db->sanitize( $_POST['user'] )."'" ) ;
            
            if( $result == false or $result == 0 ){
                $post->json_reply( 'FAIL' ) ;
            }
            
            $post->json_reply( 'SUCCESS' ) ;
            
            
            header( 'Location: /profile/'.$db->sanitize( $_POST['user'] ) );
            die() ;
            
        case 'cancelFriendrq' :
            
            $result = $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                `requestee`='".$user->id."' AND `requestor`='".$db->sanitize( $_POST['user'] )."'" ) ;
            
            if( $result == false ){
                $post->json_reply( 'FAIL' ) ;
            }
        
            $post->json_reply( 'SUCCESS' ) ;
            
            header( 'Location: /profile/'.$db->sanitize( $_POST['user'] ) );
            die() ;
            
        case 'blockUser' :
            
            // add block entry 
            
            $db->insert( "INSERT INTO `profile_block` SET `blocker`='".$user->id."' AND `blockee`='".$db->sanitize( $_POST['user'] )."'" ) ;
            
            // remove any existing friendship
            
            $result = $db->update( "DELETE FROM `profile_friendship` WHERE  
                ( `friend1`='".$user->id."' AND `friend2`='".$db->sanitize( $_POST['user'] )."' )
                    OR
                ( `friend2`='".$user->id."' AND `friend1`='".$db->sanitize( $_POST['user'] )."' )" ) ;
 
               //TODO Wipe out comments, likes
            
            // add activity for BLOCKED user so that they will have their friend/block list updated
                
            $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$db->sanitize( $_POST['user'] )."', `type`='0', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
            $user->load_friends_list() ;
         
            $post->json_reply( 'SUCCESS' ) ;
            
            header( 'Location: /profile/block_list' );
            die() ;
            
        case 'unblockUser' :
            
            // add block entry 
            
            $db->update( "DELETE FROM `profile_block` WHERE `blocker`='".$user->id."' AND `blockee`='".$db->sanitize( $_POST['user'] )."'" ) ;
                       
            $post->json_reply( 'SUCCESS' ) ;
            
            header( 'Location: /profile/block_list' );
            die() ;
        
    }