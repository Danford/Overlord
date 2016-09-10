<?php

switch( $_POST['oe_formid'] ) {
    
    case 'getFriends':
        
        if( ! verify_number( $_POST['id'] ) ){ $post->json_reply("FAIL") ; }
        if( $user->is_blocked( $_POST['id'] )){ $post->json_reply("FAIL") ; }
 
        $profile = new profile_minion( $_POST['id' ] ) ;

        if( ! isset( $_POST['start'] ) ) 
            { $_POST['start'] == 0 ; }
        elseif( ! verify_number( $_POST['start'] ) )
            { $post->json_reply("FAIL") ; }
        
        if( ! isset( $_POST['end'] ) ) 
            { $_POST['end'] == 999999 ; }
        elseif( ! verify_number( $_POST['start'] ) )
            { $post->json_reply("FAIL") ; }
        
        if( ! isset( $_POST['order'] ) ) { $_POST['order'] == 'screen_name' ; }
        
        $friends = $profile->get_friends_as_array( $_POST['offset'], $_POST['limit'], $_POST['order'] ) ;
        
        if( $friends == false ){ $post->json_reply("FAIL") ; }
        
        json_reply( "SUCCESS", [ 'friends' => $friends ] ) ;
 
        
        
}