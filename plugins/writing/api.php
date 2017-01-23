<?php


switch( $apiCall ){
    
    case "write":
    case "edit":

        if( $apiCall == "write" and $oepc[0]['contributor'] != true ){
            $post->json_reply("FAIL") ;
            die('1') ;
        } elseif ( $apiCall == "edit" ){
                if( ! isset( $_POST['writing_id']) or ! verify_number( $_POST['writing_id'])){
                    $post->json_reply("FAIL") ;
                    die('2') ;
                }
                
                $q = "SELECT `owner` FROM ".$oepc[$tier]['writing']['view']." WHERE ".build_api_where_string()." AND `id`='".$_POST['writing_id']."'" ;
                
                $owner = $db->get_field( $q." AND `id`='".$_POST['writing_id'] ."'");

                if( $owner == false or $owner != $user->id ){
                    $post->json_reply("FAIL") ;
                    die('invalid') ;
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

        if( $apiCall == 'write' ){ 
            
            $o['ip'] = get_client_ip() ;
            $o['timestamp'] = oe_time() ;
            
            $o['updated_ip'] = get_client_ip() ;
            $o['last_updated'] = oe_time() ;
            
            $setstring = $db->build_set_string_from_array($o).", ".$db->build_set_string_from_post('title','subtitle','copy','privacy' );
        
            $q = "INSERT INTO `".$oepc[$tier]['writing']['table']."` SET ".build_api_set_string().", ".$setstring ;
            $w = $db->insert( $q ) ;
    
            verify_update( $oepc[$tier]['writing']['view'], $w ) ;
            
            $post->json_reply("SUCCESS", [ 'id' => $w ] ) ;
            
            header( "Location: ".str_replace( 'write', $w, $_SERVER['HTTP_REFERER' ] ) ) ;
            die() ;
        
        } else {
            
            $o['updated_ip'] = get_client_ip() ;
            $o['last_updated'] = oe_time() ;
                
            $setstring = $db->build_set_string_from_array($o).", ".$db->build_set_string_from_post('title','subtitle','copy','privacy' );
         
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
        
    case 'getWriting':
        
        include_once $oe_plugins['writing'].'lib/writing.lib.php' ;
        
        if( ! isset( $_POST['start'] ) ){
            $_POST['start'] = 0 ;
        }
        if( ! isset( $_POST['limit'] ) ){
            $_POST['limit'] = 999999 ;
        }
        if( ! isset( $_POST['album'] ) ){
            $_POST['album'] = null ;
        }
        
        $w = get_writings() ;
        
        if( $w == false ){
            $post->json_reply("FAIL") ;
            die();
        }
        
        $post->json_reply("SUCCESS", $w );
        die();
}