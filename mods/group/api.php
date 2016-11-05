<?php

if( $user->id == 0 ){ 
       $post->json_reply("FAIL") ;
       die();
} // THERE IS NO REASON THEY SHOULD BE HERE.

$baseurl = httproot.'group/' ;
$pagedir = $oe_modules['group']."api/" ;

if( isset( $_POST['group_id'] )){
    
   $group = new group_minion($_POST['group_id']) ;
   if( $group->id == false ){
       $post->json_reply("FAIL") ;
       die();
   }
}

switch( $apiCall ) {

    case 'getMyGroups':
    
           include $oe_modules['group']."lib/group.lib.php" ;
           $json->reply( "SUCCESS", get_my_groups() ) ;
           die();

    case 'create':
        
            include( $pagedir."profile.php" ) ;
            die();
            
    case 'edit':
   
        if( $group->membership == 2 ){
        
            include( $pagedir."profile.php" ) ;
            die();
        }
        $post->json_reply("FAIL");
        die();
        
    case 'join':
        
        if( $group->privacy == 1 or $group->invited == true ){
            
            $db->insert( "INSERT INTO `group_membership` SET `group`='".$group->id."', `user`='".$user->id."', `access`='1', `timestamp`='".oe_time()."'" ) ;
            
            $db->update( "DELETE FROM `invitations` 
                                WHERE `module`='group' 
                                  AND `module_item_id`='".$group->id."'
                                  AND `invitee`='".$user->id."'" ) ;

            $user->load_group_membership() ;
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ; // they should have come from the group profile
        }
        $post->json_reply("FAIL") ;
        die();
        
    case 'leave':
        
        if( $group->membership > 0 ){
            
            $db->update( "DELETE FROM `group_membership` WHERE `group`='".$group->id."' AND `user`='".$user->id."'" ) ;

            $user->load_group_membership() ;
            $post->json_reply("SUCCESS") ;
            
            // send them back to their list of groups
            
            header( "Location: ".str_replace( "/".$group->id, '', $_SERVER['HTTP_REFERER'] ) ) ;
            die();
            
        }
        $post->json_reply("FAIL") ;
        die();
        
    case 'ban':
        
        if( $group->membership == 2 and verify_number($_POST['user']) ){
        
            $db->update( "UPDATE `group_membership` SET `access`='0' WHERE `group`='".$group->id."' AND `user`='".$_POST['user']."'" ) ;
            
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
            
        }
        $post->json_reply("FAIL") ;
        die();
        
    case 'unban':
        
        if( $group->membership == 2 and verify_number($_POST['user']) ){
        
            $db->update( "DELETE FROM `group_membership` WHERE `group`='".$group->id."' AND `user`='".$_POST['user']."'" ) ;
            
            // removes the ban, but the user will have to be reinvited and/or rejoin.
            
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
            
        }
        $post->json_reply("FAIL") ;
        die();
        
        
    case 'promoteAdmin' :
        
        if( $group->membership == 2 and verify_number($_POST['user']) ){
        
            $db->update( "UPDATE `group_membership` SET `access`='2' WHERE `group`='".$group->id."' AND `user`='".$_POST['user']."'" ) ;
            
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
            
        }
        $post->json_reply("FAIL") ;
        die();
        
        
    case 'demoteAdmin' :
        
        if( $group->membership == 2 and verify_number($_POST['user']) ){
        
            $db->update( "UPDATE `group_membership` SET `access`='1' WHERE `group`='".$group->id."' AND `user`='".$_POST['user']."'" ) ;
            
            $post->json_reply("SUCCESS") ;
            $post->return_to_form() ;
            
        }
        $post->json_reply("FAIL") ;
        die();
        
    case 'getMembers':
        
        $post->json_reply( "SUCCESS", $group->get_members() );
}
$post->json_reply('FAIL') ;
die( 'FAIL' ) ;