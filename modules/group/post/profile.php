<?php

switch( $_POST["oe_formid"] ) {

    case 'create':
    case 'edit':
        
        $post->hold( 'name', 'short_desc', 'detail', 'privacy' ) ;
        
        if( ! is_numeric( $_POST["type"] ) or $_POST["type"] < 1 or  $_POST["type"] > 3 ){
            $post->json_reply('FAIL') ;
            die() ;
        }
        
        $post->require_true( $_POST['name'] != "", 'name' , "Name is a required field."  ) ;
        
        $post->require_true( $_POST['short_desc'] != "", 'short_desc' , "Description is a required field."  ) ;
        
        $post->require_true( $_POST['name']  < 56 , 'name', "Name may not be longer than 55 characters." ) ;
        
        $post->require_true( $_POST['short_desc'] < 256 , 'short_desc', "Short description may not be longer than 255 characters." ) ;
        
        $post->checkpoint() ;
        
        if( $_POST['oe_formid'] == 'create' ) {
            
            $q = "SELECT COUNT(*) FROM `group_profile` WHERE `name` = '".$db->sanitize( $_POST['name'] )."'" ;
            
            $post->require_true( $db->get_field( $q ) == 0 , 'name', 'There is already a group named "'.$_POST['name'].'"' ) ;
            
            $post->checkpoint() ;
        
            $_POST['owner'] = $user->id ;
            
            $group_id = $db->insert( "INSERT INTO `group_profile` SET ".$db->build_set_string_from_post( 'name','type', 'short_desc', 'detail', 'owner' ));
            
            $db->insert( "INSERT INTO `group_members` SET `group_id` = '".$group_id."', `member_id`='".$user->id."', `timestamp`='".oe_time()."', ".$db->build_set_string_from_post( 'notify_thread', 'notify_message' ) );
            
            $user->load_group_membership() ;

            $post->json_reply('SUCCESS', $group_id ) ;
            
        } else {
            
            include( $oe_modules['group']."lib/group_minion.php" ) ;
            
            $group = new group_minion($db->sanitize( $_POST['group_id'] )) ;
            
            if( ! $group->is_owner() ){
                
                $post->json_reply( 'FAIL' ) ;
                die() ;
            }
                        
            $post->require_true( $group->privacy <= $_POST['privacy'], 'privacy', "You cannot make a group less private." ) ;
            
            $post->checkpoint() ;
            
            $db->update("UPDATE `group_profile` SET ".$db->build_set_string_from_post( 'name','type', 'short_desc', 'detail' )." 
                WHERE `group_id`='".$db->sanitize( $_POST['group_id'] )."'" ) ;

            $post->json_reply('SUCCESS') ;
            
            $group_id = $db->sanitize( $_POST['group_id'] ) ;
        }
        
        header( 'Location: /group/'.$group_id ) ;
        die() ;
        
        
        
}
