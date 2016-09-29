<?php
switch( $apiCall ){
    
    case 'uploadPhoto':
        
        if( $oepc[0]['contributor']){
        
            include( $oe_plugins['photo'].'/api/photo.php') ;
            die() ;

        }
        
        $post->json_reply("FAIL") ;
        die( json_encode($oepc));
        
    case "editPhoto":
        
        if( ! verify_number( $_POST['photo_id'] ) ){
            $post->json_reply("FAIL") ;
            die();
        }
        
        $original = $db->get_assoc( "SELECT `owner`, `file_key`, `title`, `description`, `privacy` 
                                    FROM ".$oepc[$tier]['photo']['view']." 
                                    WHERE `module`='".$basemodule."'
                                      AND `module_item_id`='".$basemoduleID."'
                                      AND `id`='".$_POST['photo_id']."'" ) ;
        
        if( $original['owner'] != false and ( $oepc[0]['admin'] or $original['owner'] == $user->id ) ){
            
            // it's real, they have access to it...
            
            $filekey = $original['filekey'] ;
        
            include( $oe_plugins['photo'].'/api/photo.php') ;
            die() ;
            
        }
        
        $post->json_reply("FAIL") ;
        die();
        
    case 'deletePhoto':
        
        if( ! verify_number( $_POST['photo_id'] ) ){
            $post->json_reply("FAIL") ;
            die();
        }
    
        include( $oe_plugins['photo'].'/api/deletephoto.php') ;
        die() ;
        
        
    // json only calls    
        
    case 'getInfo':
        
        if( ! verify_number( $_POST['photo_id'] ) ){
            $post->json_reply("FAIL") ;
            die();
        }
    
        include( $oe_plugins['photo'].'/lib/photo.lib.php') ;
        
        $r = get_photo_info($_POST['photo_id'] ) ;
        
        if( $r == false ){
            $post->json_reply("FAIL") ;
            die();
        }
        
        $post->json_reply( "SUCCESS", $r ) ;
        die() ;
        
    case 'getPhotoList':
        
        if( ! verify_number( $_POST['start'] ) or ! verify_number( $_POST['limit'] ) or ( isset( $_POST["album"] ) and ! verify_number( $_POST['album'] )) ){
            $post->json_reply("FAIL") ;
            die();
        }
        
        if( ! isset( $POST['album'] ) ){
            $_POST['album'] = null ;
        }
        
        
        $r = get_photos( $_POST['start'], $_POST['limit'], $_POST['album'] ) ;
    
        if( $r == false ){
            $post->json_reply("FAIL") ;
            die();
        }
    
        $post->json_reply("SUCCESS", $r ) ;
    
    
    
}