<?php


    switch( $_POST["oe_formid"] ){
        
        case 'addfriend' :

            $user->load_friends_list() ;
            
            if( ! $user->is_blocked( $uri[$pos] ) ) {
                $db->insert( "INSERT INTO `profile_friendship_rq` SET `requestor`='".$user->id."', `requestee`='".$_POST['user']."'" ) ;
                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$_POST['user']."', `type`='1', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                
                if( $post->is_a_json_request ){
                    $post->json_reply( 'SUCCESS' ) ;
                }
            }
            
            if( $post->is_a_json_request ){
                $post->json_reply( 'FAIL' ) ;
            }
                
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
            
        case 'removefriend' :
            
            $db->update( "DELETE FROM `profile_friendship` WHERE  
                ( `friend1`='".$user->id."' AND `friend2`='".$_POST['user']."' )
                    OR
                ( `friend2`='".$user->id."' AND `friend1`='".$_POST['user']."' )" ) ;
            
            $user->load_friends_list() ;
            

            $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$_POST['user']."', `type`='3', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
            
            if( $post->is_a_json_request ){
                $post->json_reply( 'SUCCESS' ) ;
            }
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'confirmfriend' :
            
            // verify that there is a request
            
            if( $db->get_field( "SELECT count(*) FROM `profile_friendship_rq` WHERE 
                `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'") == 1 ){
                
                // remove friend request
                
                $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                    `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'" ) ;
            
                
                // add friendship
     
                $db->insert( "INSERT INTO `profile_friendship` SET 
                    `friend1`='".$user->id."', `friend2`='".$_POST['user']."', `timestamp`='".oe_time()."'" ) ;
     
                // add activity
                
                $st['user_id'] = $user->id ;
                $st['ref'] = $_POST['user'] ;
                $st['type'] = 1 ;
                $st['timestamp'] = oe_time() ;
                
                $db->insert( "INSERT INTO `user_activity` SET ".$db->build_set_string_from_array( $st ) ) ;
                
                $st['user_id'] = $_POST['user'] ;
                $st['ref'] = $user->id ;
                
                $db->insert( "INSERT INTO `user_activity` SET ".$db->build_set_string_from_array( $st ) ) ;

                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$_POST['user']."', `type`='2', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                
                $user->load_friends_list() ;
                
                if( $post->is_a_json_request ){
                    $post->json_reply( 'SUCCESS' ) ;
                }
            }
            
            if( $post->is_a_json_request ){
                $post->json_reply( 'FAIL' ) ;
            }
            
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'denyfriend' :
            
            $result = $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                `requestor`='".$user->id."' AND `requestee`='".$_POST['user']."'" ) ;
            
            if( $post->is_a_json_request ){
                if( $result == false ){
                    $post->json_reply( 'FAIL' ) ;
                }
                
                $post->json_reply( 'SUCCESS' ) ;
            }
            
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'cancelfriendrq' :
            
            $result = $db->update( "DELETE FROM `profile_friendship_rq` WHERE
                `requestee`='".$user->id."' AND `requestor`='".$_POST['user']."'" ) ;
            if( $post->is_a_json_request ){
                if( $result == false ){
                    $post->json_reply( 'FAIL' ) ;
                }
            
                $post->json_reply( 'SUCCESS' ) ;
            }
            header( 'Location: /profile/'.$_POST['user'] );
            die() ;
            
        case 'blockuser' :
            
            // add block entry 
            
            $db->insert( "INSERT INTO `profile_block` SET `blocker`='".$user->id."' AND `blockee`='".$_POST['user']."'" ) ;
            
            // remove any existing friendship
            
            $result = $db->update( "DELETE FROM `profile_friendship` WHERE  
                ( `friend1`='".$user->id."' AND `friend2`='".$_POST['user']."' )
                    OR
                ( `friend2`='".$user->id."' AND `friend1`='".$_POST['user']."' )" ) ;
 
            
            if( $result != false ){
                
                // add activity for BLOCKED user so that they will have their friend/block list updated
                
                $db->insert( "INSERT INTO `user_notification` SET `user_id`='".$_POST['user']."', `type`='0', ref='".$user->id."', `timestamp`='".oe_time()."'") ;
                $user->load_friends_list() ;
            
                if( $post->is_a_json_request ){
                    $post->json_reply( 'SUCCESS' ) ;
                }
            }
            
            if( $post->is_a_json_request ){
                $post->json_reply( 'FAIL' ) ;
            }
            header( 'Location: /profile/block_list' );
            die() ;
        
    }