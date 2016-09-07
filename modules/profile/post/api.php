<?php

switch( $_POST['oe_formid'] ) {
    
    case 'getFriends':
        
        if( ! verify_number( $_POST['id'] ) ){ $post->json_reply("FAIL") ; }
        if( $user->is_blocked( $_POST['id'] )){ $post->json_reply("FAIL") ; }
 
        $profile = new profile_minion( $_POST['id' ] ) ;
        
        $friends = $profile->get_friends( $_POST['offset'], $_POST['limit'] ) ;
        
        if( $friends == false ){ $post->json_reply("FAIL") ; }
        
        json_reply( "SUCCESS", [ 'friends' => $friends ] ) ;
 
        
        
}