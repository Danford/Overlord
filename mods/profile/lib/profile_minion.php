<?php


class profile_minion {
    
    var $name ;
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

        global $user ;
        
        if( $user->is_blocked( $id ) ){
            $this->name = false ;
        } else {
            $q = "SELECT `screen_name`, `gender`, `avatar`, `birthdate`, 
                        `show_age`,`allow_contact`,`city` as `city_id`";
                        
            if( $min != true ){ $q .=  ", `detail`" ; }
            
            $q .= "     FROM `profile`
                        WHERE 
                            `profile`.`user_id`='".$this->id."'" ;
         
            $info = $db->get_assoc( $q );
            
            if( $info == false ){
                $this->name = false ; 
            } else {
                $this->name = $info['screen_name'] ;
            }
         }
         
         if( $this->name != false ){
         
                $info['allow_contact'] = ( $info['allow_contact'] == 1 ) ;
                
                if( $info['show_age'] == 1 ){
                    $info['age'] = user_age( $info['birthdate'] ) ;
                } else {
                    $info['age'] = '' ;
                }
                
                if( $info['city_id'] != null and $info['city_id'] != 0 ){
                    
                    $loc = $db->get_assoc( "SELECT `city`,`state` FROM `location_city` WHERE `id`='".$info['city_id']."'" ) ;
                    
                    $info['city'] = $loc['city'] ;
                    $info['state'] = $loc['state'] ;
                    
                } else {
                    $info['city'] = "" ;   
                    $info['state'] = "" ;
                }

                $info['detail'] = process_user_supplied_html( $info['detail'] )  ;
                
                unset( $info['birthdate'] );
                
                foreach( $info as $key => $val ){
                       $this->$key = $val ;
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
        global $db ;
        
        if( $this->name == false ){
            return false ;
        } else {
            
            $friend_list = array() ;
            
            $db->query( "SELECT `friend1`, `friend2`, `timestamp` from `profile_friendship`
                            WHERE `friend1` ='".$this->id."'
                               OR `friend2` ='".$this->id."'" ) ;
        
            while( ( $f = $db->assoc() ) != false ){
        
                if( $f['friend1'] == $this->id ){
        
                    $p = new profile_minion( $f['friend2'] , true );
                } else {
        
                    $p = new profile_minion( $f['friend1'] , true ); ;
                }
            
                if( $p->name != false ) {
                    
                    $friend_list[] = $p ;
                }
            }
        
            return $friend_list ;
        }
        
    } 
    
    
    function friend_request_status() {
        
        global $db ;
        
        if ( $this->id == $this->user->id ) {
            return "self" ;
        } elseif( $this->user->is_friend($this->id ) ) {
            return "friend" ;
        } else {
            
            $a = $db->get_field( "SELECT `requestee` from `profile_friendship_rq` WHERE
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
        
        global $db ;
        
        return $db->get_field( "SELECT COUNT(*) FROM `profile_friendship` WHERE 
                                        `profile_friendship`.`friend1` ='".$this->id."' OR
                                        `profile_friendship`.`friend2` ='".$this->id."'" ) ;
    }    
}