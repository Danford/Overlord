<?php

class group_minion {
    
    var $id = false ;
    var $name ;
    var $owner ;
    var $short ;
    var $detail ;
    var $privacy ;
    var $avatar ;
    var $city_id ;
    var $invited ;
    var $membership ;
    var $blocked ;
    var $table = 'groups' ;     // this needs to be customisable
    var $view = 'groups' ;      // but for now it's hardcoded here.
  
    function __construct( $id, $min = false ){
        
        if( verify_number( $id ) ){

            $this->id = $id ;
            
            global $db ;
            global $user ;
            
            $q = "SELECT `name`, `owner`, `privacy`, `short`, `avatar`, `city` as `city_id`" ;
            
            if( $min != false ){ $q .= ", `detail`" ; }
            
            $q .= " FROM `group` WHERE `id`='".$id."'" ;
            
            $g = $db->get_assoc( $q ) ;
            
            $this->name = $g['name'];
            $this->owner = $g['owner'];
            $this->privacy = $g['privacy'];
            $this->short = $g['short'];
            $this->avatar = $g['avatar'];
            $this->city = $g['city'];
            
            // blocked, city, city_id, detail, and invited still appear to be empty in array and require furthure implementation.
            
            if( $g != false ){
                
                /* determine if user is owner, member, admin, or blocked */
                
                if( $user->is_blocked( $g['owner'] ) ){
                    
                    $this->membership = false ; // cannot see a group whose owner is blocked
                    
                } elseif( $g['owner'] == $user->id ){
         
                    $this->membership = 2 ; // admin
                    
                } else {
                	$this->membership = $user->get_group_membership($this->id);
                }
                
                if( $this->membership != false ) {
                    // they aren't blocked
                    
                    if( $this->membership == 0 and $g['privacy'] > 1 ){
                    
                        // if it's not a public group, and they're not a member, 
                        // we need to know if they've been invited.
                        
                        $c = $db->get_field( "SELECT COUNT(*) FROM `invitations` 
                                                WHERE `module`='group' 
                                                AND `module_item_id`='".$id."' 
                                                AND `user`='".$user->id."'" ) ;
                            
                        $this->invited = ( $c > 0 ) ;
                        
                    }
                    
                    if( $this->membership > 0 or $g['privacy'] < 3 or $this->invited == true ){
                        
                        /*
                         *      if they are members, or the group is not secret,
                         *      or they've been invited
                         *      then they can access the basic group information
                         *      this object represents.
                         *      
                         */
                        
                        foreach( $g as $key => $val ){
                            $this->$key = $val ;
                        }
                        
                        $this->owner = new profile_minion( $this->owner, true ) ;
                        
                    } else {
                        
                        $this->id = false ;
                        
                    }
                }
            }
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
    
    
    function get_members( $start =0 , $limit = 9999999 ){
        
        global $db ;
        
        $q = "SELECT `user`,`access` FROM `group_membership` WHERE `group`='".$this->id."'" ;
        
        $output = array();
        
        $output[$this->owner->id]['profile'] = $this->owner;
        $output[$this->owner->id]['access'] = 3;
        
        if( $this->membership > 0 ){
        
            if( $this->membership < 2 ){
                $q .= " AND `access` != 0" ;
            }
            
            $db->query( $q." ORDER BY `access` DESC LIMIT ".$start.", ".$limit ) ;
            
            while( ( $person = $db->assoc() ) != false ){
                
                if( $person['access'] == 0 ){
                    
                	// this isn't needed for getting a list of members but we should make a function for getting banned users.
                    //$output['banned'][] = $db->get_assoc( "SELECT `user_id`,`screen_name` as `name` 
                    //                                        FROM `user_profile` 
                    //                                        WHERE `user_id`='".$person['user']."'" ) ;
                    
                    
                } else {
                    
                    $p = new profile_minion($person['user'], true ) ;
                    
                    if( $p->name != false ){
                        $output[$person['user']]['profile'] = $p ;
                        $output[$person['user']]['access'] = $person['access'] ;
                    }
                }
            }
        }
        
        return $output ;
    }
    
    function get_member_count(){
        
        global $db ;
        
        return $db->get_field( "SELECT COUNT(*) FROM `group_membership` WHERE `group`='".$this->id."' AND `access` > 1" ) ;
        
    }
    
    function check_membership( $id ){
        
        global $db ;
        
        $c = $db->get_field( "SELECT `access` FROM `group_membership` WHERE `group`='".$this->id."' AND `user`='".$id."'" ) ;
        
        return $c ;
        
    }
    
    function join_group(){
        
        if( $this->privacy == 1 or $this->invited == true ){
            
            global $db ;
            global $user ;
            
            $db->insert( "INSERT INTO `group_membership` SET `group`='".$this->id."', `user`='".$user->id."', `access`='1'" ) ;
            $db->update( "DELETE FROM `invitations` WHERE `module`='group' AND `module_item_id`='".$this->id."' AND `invitee`='".$user->id."'" ) ;
            
            return true ;
        } else {
            return false ;
        }
    }
    
    /**
     *   Returns an array of all users that are members, blocked from the group, or blocked from the group owner
     */
    function get_uninvitable(){
        
        global $db ;
        global $user ;
        
        $response = array() ;
        
        $response[] = $this->owner ;
        
        $db->query( "SELECT `user` FROM `group_membership` WHERE `group`='".$this->id."'" ) ;
        
        while( ( $u = $db->field() ) != false ){
             
            $response[] = $u ;
        }
        
        // blocked by the owner/has blocked the owner
        
        $db->query( "SELECT `blocker`, `blockee` from `profile_block`
                        WHERE `blocker` ='".$this->owner->id."'
                           OR `blockee` ='".$this->owner->id."'" ) ;
         
        while( ( $b = $db->assoc() ) != false ){
        
            if( $b['blockee'] == $this->id and ! in_array($b['blocker'], $response) ){
        
                $response[] =  $b['blocker'] ;
                
            } elseif( $b['blocker'] == $this->id and ! in_array($b['blockee'], $response) ) {
        
                $response[] =  $b['blockee'];
            }
        }
        
        // already invited, or has an invite pending approval
        
        $db->query( "SELECT `invitee` FROM `invitations`
                    WHERE `module`='group'
                        AND `module_item_id`='".$this->id."'
                        AND `invitee`='".$user->id."'
                        AND `expired` = '0'" ) ;

        
        while( ( $i = $db->field() ) != false ){
            
            if( ! inarray( $i, $response ) ){
                $response[] = $i ;
            }
        }
        
        return $response ;
    }
    
    function get_blocked(){
        
        if( ! is_array( $this->blocked ) ){
        
            global $db ;
            
            $this->blocked = array() ;
            
            $db->query( "SELECT `user` FROM `group_membership` WHERE `group`='".$this->id."' and `access`='0'" ) ;
 
            while( ( $u = $db->field() ) != false ){
                $this->blocked[] = $u ;
            }
        }
        return $this->blocked ;        
    }
    
    function is_invited($userId){
    	global $db;
    	
    	$query = "SELECT `invitee` FROM `invitations`
		           	WHERE `module`='group'
		        	AND `module_item_id`='". $this->id ."'
		           	AND `invitee`='". $userId ."'
		            AND `expired` = '0'";

    	return $db->get_field($query) == $userId;
    }
    
}