<?php

if( isset( $_POST['thread_id'] ) ){
 
    if( verify_number( $POST['thread_id'] ) ){
        $post->json_reply("FAIL") ;
        die();
    }
    
    $owner = $db->get_field( "SELECT `owner` FROM `".$oepc[$tier]['thread']['view']."`
                                WHERE ".build_api_where_string()." AND `id`='".$_POST['thread_id']."'" );
    
    if( $owner == false ){
        $post->json_reply('FAIL') ;
        die();        
    }
    
}

switch( $apiCall ){
    
    case 'getThreads':

        include( $oe_plugins['thread']."lib/thread.lib.php" ) ;
        
        if( verify_number( $_POST['start'] ) and verify_number( $_POST['limit'] ) ){
        
            $post->json_reply( "SUCCESS", get_threads( $_POST['start'], $_POST['limit'] ) ) ;
            die();
        }
        
        $post->json_reply("FAIL") ;
        die();
        
    case 'create':

        if( ! $oepc[0]['contributor'] ){
            $post->json_reply('FAIL') ;
            die();
        } 

        include $oe_plugins['thread']."api/thread.php" ;
        die();
        
    case 'edit':

        if ( $owner != $user->id ){
            $post->json_reply('FAIL') ;
            die();           
        }

        include $oe_plugins['thread']."api/thread.php" ;
        die();
        
        
    case 'makeSticky':
    case 'makeUnsticky':
        
        if( $apiCall == 'makeSticky' ){ $x = '1' ; } else { $x = '0' ; }
        
        if( $oepc[0]['admin'] ){
            $db->update( "UPDATE `".$oepc[$tier]['thread']['table']."` 
                            SET `sticky`='".$x."' WHERE ".build_api_where_string()." AND `id`='".$_POST['thread_id']."'") ;
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
        }
        
        $post->json_reply('FAIL') ;
        die();
        
    case 'lock':
    case 'unlock':
        
        if( $apiCall == 'lock' ){ $x = '1' ; } else { $x = '0' ; }
        
        if( $oepc[0]['admin'] ){
            $db->update( "UPDATE `".$oepc[$tier]['thread']['table']."` 
                            SET `locked`='".$x."' WHERE ".build_api_where_string()." AND `id`='".$_POST['thread_id']."'") ;
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
        }
        
        $post->json_reply('FAIL') ;
        die();
        
    case 'delete':
        
        if( $oepc[0]['admin'] ){
            $db->update( "DELETE FROM `".$oepc[$tier]['thread']['table']."` 
                            WHERE ".build_api_where_string()." AND `id`='".$_POST['thread_id']."'") ;
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
        }
        
        $post->json_reply('FAIL') ;
        die();
    
    
    
    
    
}