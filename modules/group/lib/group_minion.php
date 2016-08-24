<?php

/*
 * 
 * For compartmentalization of group managment
 * 
 * 
 * 
 * 
 */

class group_minion {
    
    var $id ;
    var $name ;
    var $short_desc ;
    var $detail ;
    var $type ;
    var $owner_id ;
    var $owner_name ;
    var $owner_avatar ;
    var $moderator ;
    var $access ;
    var $invited ;
    
    function __construct( $group_id ) {
        
        /* returns false if group does not exist or user does not have access
                 returns access level otherwise:
                    view - can see profile
                    read - can read, but not post, messages
                    full - can post messages */
        
        global $db ;
        global $user ;
        
        $query = "SELECT `name`, `type`, `short_desc`, `group_profile`.`detail`,
                            `user_profile`.`user_id` as `owner_id`,
                            `user_profile`.`screen_name` as `owner_name`,
                            `user_profile`.`avatar` as `owner_avatar`
                    FROM    `group_profile`, `user_profile`
                    WHERE   `group_profile`.`group_id` = '".$db->sanitize($group_id)."'
                    AND     `group_profile`.`owner` = `user_profile`.`user_id`" ;
        
        if( ( $profile = $db->get_assoc( $query ) ) == false ){
            $this->access = false ;
        } else {
        
            // it's a real group, are they blocked?
                    
            $this->id = $group_id ;
            
            if( $user->is_blocked( $profile['owner_id'] ) ) {
                
                // either the user has blocked the owner, or the owner has blocked the user.
                
                $this->access = false ;
            }
            elseif( $db->get_field( "SELECT COUNT(*) FROM `group_block`
                            WHERE `group_id`='".$group_id."' AND `blocked_user`='".$user->id."'" ) != 0 ){
                
                // user is explicitly blocked FROM THE GROUP
                            
                $this->access = false ;
                
            } else { 
                // check their access level - first see if they are a member
                
                $check = $db->get_field( "SELECT COUNT(*) FROM `group_members` WHERE `group_id`='".$group_id."' AND `member_id`='".$user->id."'" ) != 0 ;
                    
                if( $profile['type'] == '1' ){
                 
                    // public group-- anyone can read, but members can post
                    
                    if( ! $check ){
                        $this->access = 'read' ;
                    } else {
                        $this->access = 'full' ;
                    }
                    
                } elseif( $profile['type'] == '2' ){
                    
                    // closed group, can only see the profile if not a member
                    
                    if( ! $check  ){
                        $this->access = 'view' ;
                    } else {
                        $this->access = 'full' ;
                    }
                    
                } else {
                    
                    // secret group 
                    
                    if( $check ) {
                        
                        // member - full access
                        
                        $this->access = 'full' ;
                        
                    } else {
                        
                        // Not a member.  Have they been invited?
                        
                        if( ! $this->is_invited() ){
                            
                            // No - they can't even see that the group exists
                            
                            $this->access = false ;
                            
                        } else {
                            
                            // Yes - they can see the group profile and decide if they want to join
                            
                            $this->access = 'view' ;
                        }
                    }
                }
            }
            
            if( $this->access != false ){
                
                // Now that we know this is a real group, and they have at least limited access to it
                
                $this->id = $group_id ;
                
                foreach( $profile as $key => $val ) {
                    $this->$key = $val ; 
                }
            }
        }
    }
    
    function is_owner(){
        global $user ;
        return $user->id == $this->owner_id ;
    }
    
    function is_member( $id ) {
     
        $db->query( "SELECT `member_id` from `group_members` WHERE `group_id`='".$this->id." AND `member_id`='".$id."'" ) ;
        
        return $db->count != 0 ;
        
    }
    
    function is_moderator( $user_id = '' ){
        
         if( $user_id == '' ) {
            
            if( $this->is_owner() ){
                return true ;
            } else {
                if( $this->moderator == null ){
                            
                    global $db ;
                    global $user ;
                    
                    $query = "SELECT COUNT(*) FROM `group_moderator`
                                WHERE `group_id`='".$this->id."' AND `mod_id`='".$user->id."'" ;
                    
                    $this->moderator = ( $db->get_field( $query ) != 0 ) ;
            
                }
                
                return $this->moderator ;
            }
        } else {
            
            if( $user_id == $this->owner_id ) {
                return true ;
            } else {
            
                global $db ;
                
                $query = "SELECT COUNT(*) FROM `group_moderator` 
                                WHERE `group_id`='".$this->id."' AND `mod_id`='".$user_id."'" ;
                
                return ( $db->get_field( $query ) != 0 ) ;
            }
        }
    }
    
    function get_thread_list( $limit = 9999999, $offset = 0 ){
        
        global $db ;
        global $user ;
        
        $query = "SELECT `thread_id`, `sticky`, `subject`, `latest_timestamp`,
                        `user_profile`.`user_id`, `user_profile`.`screen_name` as `name`,
                        `user_profile`.`avatar`
                        FROM `group_thread`, `user_profile`
                        WHERE `group_id` = '".$this->id."' 
                        AND `group_thread`.`user_id` = `user_profile`.`user_id`
                            ORDER BY `sticky` DESC, `latest_timestamp` DESC
                            LIMIT ".$offset.",".$limit ;
        
        $list = array();
        
        $db->query( $query ) ;
        
        while( ( $thread = $db->assoc() ) != false ){
            
            if( ! $user->is_blocked( $thread['user_id' ] ) 
                or $this->is_moderator() or $this->is_moderator( $thread['user_id'] )) {
              
                // block does not apply when one of the users is a moderator
                    
                $thread['subject'] = prevent_html($thread['subject']) ;
                $list[] = $thread ;
            } 
        }
        
        return $list ;
    }
    
    function get_thread( $thread_id ) {

        global $db ;
        global $user ;
        
        $query = "SELECT `thread_id`, `sticky`, `subject`, `latest_timestamp`,
                        `user_profile`.`user_id`, `user_profile`.`screen_name` as `name`,
                        `user_profile`.`avatar`
                        FROM `group_thread`, `user_profile`
                        WHERE `thread_id` = '".$thread_id."'
                        AND `group_id` = '".$this->id."'
                        AND `group_thread`.`user_id` = `user_profile`.`user_id`" ;  
        
        $thread = $db->get_assoc( $query ) ;
        
        if( $thread != false and ( ! $user->is_blocked( $thread['user_id' ] ) 
                or $this->is_moderator() or $this->is_moderator( $thread['user_id'] ))){
    
            $thread['subject'] = prevent_html($thread['subject']) ;
        } else {
            // in case they're blocked 
            $thread = false ;
        }
    
        return $thread ;
    }
    
    function get_message_count( $thread ){
        global $db;
        return $db->get_field( "SELECT COUNT(*) FROM `group_message` WHERE `thread_id` = '".$thread."'" ) ;
    }
    
    function get_messages( $thread, $limit = 9999999, $offset = 0 ){
        
        global $db ;
        global $user ;
        
        $query = "SELECT `message_id`, `timestamp`, `message`, 
                        `user_profile`.`user_id`, `user_profile`.`screen_name` as `name`,
                        `user_profile`.`avatar`
                        FROM `group_message`, `user_profile`, `group_thread`
                        WHERE `group_message`.`thread_id` = '".$thread."' 
                        AND `group_message`.`user_id` = `user_profile`.`user_id`
                        AND `group_message`.`thread_id` = `group_thread`.`thread_id`
                        AND `group_thread`.`group_id` = '".$this->id."'
                            ORDER BY `timestamp` 
                            LIMIT ".$offset.",".$limit ;
                
        $list = array();
        
        $db->query( $query ) ;
        
        while( ( $message = $db->assoc() ) != false ){
            
            if( ! $user->is_blocked( $message['user_id' ] )
                or $this->is_moderator() or $this->is_moderator( $thread['user_id'] )) {
                
                $message['message'] = process_user_supplied_html($message['message']) ;
                $list[] = $message ;
            }
        }
        
        return $list ;
    }
    
    function get_members( $offset = 0, $limit = 'default' ){
        
        if( $limit == 'default' ){
            $limit = members_per_column * members_columns ;
        }
        
        if( $this->access == 'full' or $this->access == 'read'  ){
            
            global $db ;
            global $user ;
            
            $db->query( "SELECT `user_id`, `screen_name`, `show_age`, `birthdate`, `gender`, `avatar` FROM `group_members`, `user_profile`
                                WHERE `group_members`.`member_id` = `user_profile`.`user_id`
                                AND `group_members`.`group_id` = '".$this->id."' 
                                ORDER BY `screen_name`
                                LIMIT ".$offset.",".$limit );
            
            $list = array() ;
            
            while( ( $member = $db->assoc()) != false ){
                
                if( ! $user->is_blocked( $member['user_id'] ) or ! $this->is_moderator() or ! $this->is_moderator( $member['user_id'] ) ) {
                    $list[] = $member ;
                }
            }
            
            return $list ;
            
        } else {
            
            return false ;
        }
        
    }
    
    function get_banned( $offset = 0, $limit = 'default' ){
        
        if( $limit == 'default' ){
            $limit = members_per_column * members_columns ;
        }
        
        if( ! $this->moderator  ){
            
            global $db ;
            global $user ;
            
            $db->query( "SELECT `user_id`, `screen_name`, `show_age`, `birthdate`, `gender`, `avatar`, `blocked_by`
                                FROM `group_block`, `user_profile`
                                WHERE `group_block`.`blocked_user` = `user_profile`.`user_id`
                                AND `group_block`.`group_id` = '".$this->id."' 
                                ORDER BY `screen_name`
                                LIMIT ".$offset.",".$limit );
            
            $list = array() ;
            
            while( ( $banned = $db->assoc()) != false ){
                
                if( ! isset( $mods[$member['blocked_by']] ) ){
                    
                    $mods[$banned['blocked_by']] = $db->get_field( "SELECT `screen_name` FROM `user_profile` WHERE `user_id`='".$banned['blocked_by']."'" );
                    
                }
                
                $banned['blocked_by'] = $mods[$banned['blocked_by']] ;
                
                $list[] = $banned ;
                
            }
            
            return $list ;
            
        } else {
            
            return false ;
        }
        
    }
    
    function is_invited(){
        
        global $db ;
        global $user ;
        
        if( $this->invited == null ){

            $query = "SELECT COUNT(*) FROM `group_invite`
                            WHERE `group_id`='".$this->id."' AND `user_id`='".$user->id."'
                            AND `type`='0'" ;
            
            debug_log($query) ;
            
            $this->invited = ( $db->get_field( $query ) == 1 ) ;
            
        } 
        
        return $this->invited ;
        
    }
}