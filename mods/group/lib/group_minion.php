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
        var $table = 'groups' ;     // this needs to be customisable
        var $view = 'groups' ;      // but for now it's hardcoded here.
        var $admins = null ;
    
        function __construct( $id, $min = false ){
            
            if( verify_number( $id ) ){
                
                global $db ;
                global $user ;
                
                $q = "SELECT `name`, `owner`, `privacy`, `short`, `avatar`, `group`, `city` as `city_id`" ;
                
                if( $min != false ){ $q .= ", `detail`" ; }
                
                $q .= " FROM `group` WHERE `id`='".$id."'" ;
                
                $g = $db->get_assoc( $q ) ;
                
                if( $g != false ){
                    
                    /* determine if user is owner, member, admin, or blocked */
                    
                    if( $user->is_blocked( $g['owner'] ) ){
                        
                        $membership = false ; // cannot see a group whose owner is blocked
                        
                    } elseif( $g['owner'] == $user->id ){
             
                        $membership = 2 ; // admin
                        
                    } else {
                        
                        $c = $db->get_field( "SELECT `access` FROM `group_membership` 
                                                WHERE `group`='".$id."' AND `user`='".$user->id."'") ;
                        
                        if( $c == false ){
                            $membership = 0 ; // non member
                        } elseif( $c == 0 ){
                            $membership = false ; // blocked
                        } else {
                            $membership = $c ;  // 1 for member 2 for admin
                        }
                    }
                    
                    if( $membership != false )
                    {
                        // they aren't blocked
                        
                        $this->membership = $membership ;
                        
                        if( $membership == 0 and $g['privacy'] > 1 ){
                        
                            // if it's not a public group, and they're not a member, 
                            // we need to know if they've been invited.
                            
                            $c = $db->get_field( "SELECT COUNT(*) FROM `invitations` 
                                                    WHERE `module`='group' 
                                                    AND `module_item_id`='".$id."' 
                                                    AND `user`='".$user->id."'
                                                    AND `type`" ) ;
                                
                            $this->invited = ( $c > 0 ) ;
                            
                        }
                        
                        if( $membership > 0 or $g['privacy'] < 3 or $this->invited == true ){
                            
                            /*
                             *      if they are members, or the group is not secret,
                             *      or they've been invited
                             *      then they can access the basic group information
                             *      this object represents.
                             *      
                             */
                            
                            $view = true ;
                            
                        } else {
                            
                            $view = false ;
                            
                        }
                        
                        if( $view ){
                         
                            // if we didn't make it all the way here,
                            // then no data will be populated and
                            // the engine will know it's a bad match.
                            
                            foreach( $g as $key => $val ){
                                $this->$key = $val ;
                            }
                            
                            $this->owner = new profile_minion( $this->owner, true ) ;

                        }
                    }
                }
            }
        }
    
    
    function city_name(){
        
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
        
        $output['owner'] = $this->owner  ;
        $output['admins'] = array() ;
        $output['members'] = array() ;
        
        if( $this->membership > 0 ){
        
            if( $this->membership == 2 ){
                $output['banned'] = array() ;
            } else {
                $q .= " AND `access` != 0" ;
            }
            
            $db->query( $q." ORDER BY `access` DESC LIMIT ".$start.", ".$limit ) ;
            
            while( ( $person = $db->assoc() ) != false ){
                
                if( $person['access'] == 0 ){
                    
                    $output['banned'][] = $db->get_assoc( "SELECT `user_id`,`screen_name` as `name` 
                                                            FROM `user_profile` 
                                                            WHERE `user_id`='".$person['user']."'" ) ;
                    
                    
                } else {
                    
                    $p = new profile_minion($person['user'], true ) ;
                    
                    if( $p->name != false ){
                        
                        if( $person['access'] = 1 ){
                            $output['members'][] = $p ;
                        } else {
                            $output['admins'][] = $p ;
                        }
                    }
                }
            }
        }
        
        return $output ;
    }
    
    function get_member_count(){
        
        return $db->get_field( "SELECT COUNT(*) FROM `group_membership` WHERE `group`='".$this->id."' AND `access` > 1" ) ;
        
    }
    
}