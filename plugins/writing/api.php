<?php

switch( $apiCall ){
    
    case "write":
    case "edit":
        
        if( $apiCall == "write" and $oepc[0]['contributor'] != true ){
            $post->json_reply("FAIL") ;
            die() ;
        } elseif ( $apiCall = "edit" ){
        
                if( ! isset( $_POST['writing_id']) or ! verify_number( $_POST['writing_id'])){
                    $post->json_reply("FAIL") ;
                    die() ;
                }
                
                $q = "SELECT `owner` FROM ".$oepc[$tier]['writing']['view']." WHERE ".build_api_where_string()." AND `id`='".$_POST['writing_id']."'" ;
                
                $owner = $db->get_field( $q." AND `id`='".$_POST['writing_id'] );
    
                if( $owner == false or $owner != $user->id ){
                    $post->json_reply("FAIL") ;
                    die() ;
                }
        }

        $post->require_true( strlen( $_POST['title'] ) < 76 , 'title', 'Title cannot be longer than 75 Characters.' ) ;
        $post->require_true( strlen( $_POST['subtitle'] ) < 256 , 'subtitle', 'Subtitle cannot be longer than 255 Characters.' ) ;
        $post->require_true( strlen( $_POST['copy'] ) > 2 , 'copy', 'You actually have to write something.' ) ;
        
        $post->checkpoint() ;

        $_POST['title'] = prevent_html( $_POST['title'] ) ;
        $_POST['subtitle'] = prevent_html( $_POST['subtitle'] ) ;
        $_POST['copy'] = process_user_supplied_html( $_POST['copy'] ) ;
        
        $o['owner'] = $user->id ;
        $o['ip'] = get_client_ip() ;
        $o['timestamp'] = oe_time() ;
            
        $setstring = $db->build_set_string_from_array($o).", ".$db->build_set_string_from_post('title','subtitle','copy' );

        if( $apiCall == 'write' ){
        
            $q = "INSERT INTO `".$oepc[$tier]['writing']['table']."` SET ".build_api_set_string().", ".$setstring ;
            $w = $db->insert( $q ) ;
    
            verify_update( $oepc[$tier]['writing']['view'], $w ) ;
            
            $post->json_reply("SUCCESS", [ 'id' => $w ] ) ;
            
            header( "Location: ".str_replace( 'write', $w, $_SERVER['HTTP_REFERER' ] ) ) ;
            die() ;
        
        } else {
         
            $db->update( "UPDATE ".$oepc[$tier]['writing']['table']." SET ".$setstring." WHERE ".build_api_where_string()." AND `id`='".$_POST['writing_id']."'" ) ;
            
            $post->json_reply("SUCCESS" ) ;
            
            header( "Location: ".str_replace( 'edit', $w, $_SERVER['HTTP_REFERER' ] ) ) ;
            die() ;
        }
        
    case 'delete':
        
        if( ! isset( $_POST['writing_id']) or ! verify_number( $_POST['writing_id'])){
            $post->json_reply("FAIL") ;
            die() ;
        }
        
        $q = "SELECT `owner` FROM ".$oepc[$tier]['writing']['view']." WHERE ".build_api_where_string()." AND `id`='".$_POST['writing_id']."'" ;
        
        $owner = $db->get_field( $q." AND `id`='".$_POST['writing_id'] );
        
        if( $owner == false or ( $owner != $user->id and $oepc[0]['admin'] == false ) ){
            $post->json_reply("FAIL") ;
            die() ;
        }
        
        $db->update( "DELETE FROM ".$oepc[$tier]['writing']['view']." WHERE ".build_api_where_string()." AND `id`='".$_POST['writing_id']."'" ) ;
        
        $post->json_reply( "SUCCESS" ) ;

        header( "Location: ".str_replace( '/'.$_POST["writing_id"], '', $_SERVER['HTTP_REFERER' ] ) ) ;
        die() ;
        
        
}