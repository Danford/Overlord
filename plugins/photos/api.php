<?php

switch( $apiCall ){
    
    case 'uploadPhoto':
        
        if( $plug[0]['contributor']){
        
            include( $oe_plugins['photo'].'/api/photo.php') ;
            die() ;

        }
        
        $post->json_reply("FAIL") ;
        die();
        
    case "editPhoto":
        
        if( ! verify_number( $_POST['photo_id'] ) ){
            $post->json_reply("FAIL") ;
            die();
        }
        
        $original = $db->get_assoc( "SELECT `owner`, `filekey` 
                                    FROM ".$plug[$tier]['photo']['view']." 
                                    WHERE `module`='".$basemodule."'
                                      AND `module_item_id`='".$basemoduleID."'
                                      AND `id`='".$_POST['photo_id']."'" ) ;
        
        if( $original['owner'] != false and ( $plug[0]['admin'] or $original['owner'] == $user->id ) ){
            
            // it's real, they have access to it...
            
            $filekey = $original['filekey'] ;
        
            include( $oe_plugins['photo'].'/api/photo.php') ;
            die() ;
            
        }
        
        $post->json_reply("FAIL") ;
        die();
        
    case 'deletePhoto':
        
    case 'getInfo':
    case 'getPhotoList':
    
    
    
    
    
    
    
}