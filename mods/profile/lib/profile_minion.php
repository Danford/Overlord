<?php


class profile_minion {
    
    var $user ;
    var $db ;
    var $id ;
    var $screen_name ;
    var $gender ;
    var $age ;
    var $avatar ;
    var $detail ;
    var $info ;
    
    function __construct( $id, $min = false ) {
        
        $this->id = $id ;
        
        global $db ;
        $this->db = $db ;

        global $user ;
        $this->user = $user ;
        
        if( $user->is_blocked( $id ) ){
            $this->name = false ;
        } else {
            $q = "SELECT `screen_name`, `gender`, `avatar`, `birthdate`, 
                        `show_age`,`allow_contact`,
                        `city_id`, `city`, `state`" ;
                        
            if( $min != false ){ $q .=  ", `detail`" ; }
            
            $q .= "     FROM `profile`,`location_city`
                        WHERE 
                            `profile`.`user_id`='".$this->id."'
                        AND
                          `profile`.`city_id` = `location_city`.`id`" ;
         
            $info = $this->db->get_assoc( $q );
            
            if( $info == false ){
                $this->name = false ;
            }
         }
         
         if( $this->name != false ){
                $info['allow_contact'] = ( $info['allow_contact'] == 1 ) ;
                
                if( $info['show_age'] == 1 ){
                    $info['age'] = user_age( $info['birthdate'] ) ;
                } else {
                    $info['age'] = '' ;
                }

                $info['detail'] = prevent_html( $info['detail'] )  ;
                $info['screen_name'] = process_user_supplied_html( $info['screen_name'] ) ;
                
                unset( $info['birthdate'] );
                
                foreach( $info as $key => $val ){
                       $this->$key = val ;
                }
                
                $this->info = $info ;
         }  
    }

    function profile_picture(){
            return image_link('avatar', $this->avatar ) ;
    }
    
    function profile_thumbnail(){
            return image_link('profilethumb', $this->avatar ) ;
    }

    function get_friends_as_array( $offset = 0, $limit = 99999999, $order='screen_name' ){
        
            // minimal info, just sn, avatar, age, contact pref, city, and state
            // for userboxes and the friends page
        
        global $user ;
        
        if( $this->name == false ){
            return false ;
        } else {
            
            $friend_list = array() ;
            
            $this->db->query( "SELECT `profile`.`user_id`, 
                                      `screen_name`, `avatar`,`show_age`,`allow_contact`,`birthdate`,
                                      `city_id`, `city`, `state`   
                                FROM `profile_friendship`, `profile`,`location_city`
                                WHERE 
                                 ( ( `profile_friendship`.`friend1` ='".$this->id."' AND
                                    `profile_friendship`.`friend2` =`profile`.`user_id` )
                                    OR
                                  ( `profile_friendship`.`friend2` ='".$this->id."' AND
                                    `profile_friendship`.`friend1` =`profile`.`user_id` ) )
                                AND
                                  `profile`.`city_id` = `location_city`.`id`
                
                                ORDER BY `'.$this->db->sanitize( $order ).'`

                                LIMIT ".$offset.", ".$limit ) ;
            
            while( ( $p = $this->db->assoc() ) != false ){
                
                if( ! $user->is_blocked( $p['user_id'] ) ) {
                    
                    if( $p['show_age'] != 0 )
                        { $p['age'] = user_age( $p['birthdate'] ) ; } 
                    else
                        { $p['age'] = 0 ; }

                    unset( $p['birthdate'] );
                    unset( $p['show_age'] ); 
                    
                    if( $user->is_friend($p['user_id']) ){
                        $p['friend'] = 1 ;
                    } else { 
                        $p['friend'] = 0 ;
                    }
                    
                    $p['avatar'] = image_link('userthumb', $p['avatar'] ) ;
                    
                    $friend_list[] = $p ;
                }
            }
        return $friend_list ;
        }
        
    }
    
    
    function friend_request_status() {
        
        if ( $this->id == $this->user->id ) {
            return "self" ;
        } elseif( $this->user->is_friend($this->id ) ) {
            return "friend" ;
        } else {
            
            $a = $this->db->get_field( "SELECT `requestee` from `profile_friendship_rq` WHERE
                    ( `requestor`='".$this->id."' AND `requestee`='".$this->user->id."' )
                    OR
                    ( `requestor`='".$this->user->id."' AND `requestee`='".$this->id."' )") ;
        
            if( $a == false ){
                return "none" ;        
            } elseif( $a == $this->id ){
                // request has been made of this profile by the user
                return "outgoing" ;
            } else {
                // this profile has requested friendship with the user
                return "incoming" ;
            }
        }
    }
    
    function get_friends_count(){
        
        return $this->db->get_field( "SELECT COUNT(*) FROM `profile_friendship` WHERE 
                                 ( ( `profile_friendship`.`friend1` ='".$this->id."' AND
                                    `profile_friendship`.`friend2` =`profile`.`user_id` )
                                    OR
                                  ( `profile_friendship`.`friend2` ='".$this->id."' AND
                                    `profile_friendship`.`friend1` =`profile`.`user_id` ) ) ") ;
    }    
}