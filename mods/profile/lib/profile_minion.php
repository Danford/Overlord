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
                        `show_age`,`allow_contact`, `city_id`";
                        
            if( $min != true ){ $q .=  ", `detail`" ; }
            
            $q .= "     FROM `profile`, `user_account`
                        WHERE 
                            `profile`.`user_id`='".$this->id."'
                        AND
                            `profile`.`user_id`=`user_account`.`user_id`
                        AND
                            `user_account`.`status` = '1' ;
                            " ;
         
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
                
                $info['detail'] = process_user_supplied_html( $info['detail'] )  ;
                
                unset( $info['birthdate'] );
                
                foreach( $info as $key => $val ){
                       $this->$key = $val ;
                }
                
                $this->info = $info ;
         }  
    }

    function city_name(){
    
        global $db ;
    
        $r = false ;
    
        if( $this->city_id != 0 and $this->city_id != null ){
             
            $r = $db->get_assoc( "SELECT `city`,`state` FROM `location_city` WHERE `id`='".$this->city_id."'" ) ;
    
            if( $r != false ){
                $r = $r['city'].",".$r['state'] ;
            }
        }
    
        if( $r == false ){ $r = "" ; }
    
        return $r ;
    }
    
    function profile_picture(){
    	if ($this->avatar != NULL)
           	return siteurl."profile/".$this->id."/photo/".$this->avatar.".profile.png" ;
    	else 
    		return siteurl."/images/noavatar.png";
    }
    
    function profile_thumbnail(){
    	if ($this->avatar != NULL)
           	return siteurl."profile/".$this->id."/photo/".$this->avatar.".profileThumb.png" ;
    	else 
    		return siteurl."/images/noavatar.png";
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
        global $user ;
        
        if ( $this->id == $user->id ) {
            return "self" ;
        } elseif( $user->is_friend($this->id ) ) {
            return "friend" ;
        } else {
            
            $a = $db->get_field( "SELECT `requestee` from `profile_friendship_rq` WHERE
                    ( `requestor`='".$this->id."' AND `requestee`='".$user->id."' )
                    OR
                    ( `requestor`='".$user->id."' AND `requestee`='".$this->id."' )") ;
        
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
    
    function get_groups(){
        
        global $db ;
        global $oe_modules ;
                
        $grouplist = array() ;
        
        // why whould we split this up into two queries? We could just set a value for owner with group_membership and load all groups into one array.
        $db->query( "SELECT `id` FROM `group` WHERE `owner`='".$this->id."'" ) ;
        
        while( ( $g = $db->field() ) != false ){
            
            $group = new group_minion($g, true ) ;
            
            if( $group->id != false ){
                $grouplist[] = $group ;
            }
            
        }
        
        $db->query( "SELECT `group` FROM `group_membership` WHERE 
                        `user`='".$this->id."'
                        AND `access` > 0
                        ORDER BY `access` DESC" ) ;
        
        while( ( $g = $db->field() ) != false ){
            
            $group = new group_minion($g, true ) ;
            
            if( $group->id != false ){
                $grouplist[] = $group ;
            }
            
        }
        return $grouplist;
    }
    
    /**
     * Returns a boolen with true if the user belongs to a group
     *
     * @return boolen
     */
    function is_in_group($group_id) {
    	$groups = $this->get_groups();
    	foreach ($groups as $group) {
    		
    		if ($group->id == $group_id)
    			return true;
    	}
    	return false;
    }
}