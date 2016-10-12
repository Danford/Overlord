<?php

class event_minion {
        
        var $id ;
        var $title ;
        var $privacy ;
        var $blocked ;
        var $invited ;
        var $owner ;
        var $subtitle ;
        var $detail ;
        var $start_date ;
        var $end_date ;
        var $timezone ;
        var $membership ;
        
    function __construct( $id, $min = false ){
        
        global $user ;
        global $db ;
        global $eventmode ;
        
        if( verify_number( $id ) ){
            
            $q = "SELECT `privacy`,`owner`,`title`,`subtitle`, `start_date`,`end_date`, `city_id`" ;
            

            if( $min != false ){ $q .= ", `detail`" ; }
            
            $q .= ", `timezone`.`name` as `timezone` 
                            FROM
                            `event`, `timezone`
                            WHERE
                            `event`.`id` = ".$id." AND `event`.`timezone` = `timezone`.`id`
                            AND " ;
            
            if( $eventmode == 'module' ){
                
                $q .= "`module` IS NULL" ;
                
            } else {
                
                $q .= build_api_where_string() ;
            }
                        
            $event = $db->get_assoc( $q ) ;
            
            if( $event != false ){
                
                /* determine if user is owner, member, admin, or blocked */
                    
                    if( $user->is_blocked( $event['owner'] ) ){
                        
                        $membership = false ; // cannot see an event whose owner is blocked
                        
                    } elseif( $event['owner'] == $user->id ){
             
                        $membership = 2 ; // admin
                        
                    } else {
                        
                        if( $mode == "plugin" ){
                        
                            /*
                             *  At the moment, the plugin aspect only applies to groups.
                             *  Since group events automatically invite all members,
                             *  the permissions of the group apply to this event
                             *  
                             */
                            
                            global $group ;
                            
                            $membership = $group->membership ;
                            
                        } else {
                            
                            /*
                             *  This is a standalone event, not dependent on a group.
                             *  "membership" is determined by invitation.
                             *  
                             */
                            
                            
                            
                            
                        }
                    }
                
                
                
                
                
                
                
                
                
                
            } else {
                $this->id = false ; 
            }
            
        }
        
        
    }
    
    function get_rsvp(){
        
        if( ! is_array( $this->invited ) ){
        
            global $db ;
            
            $invited = array() ;
            
            $db->query( "SELECT `user` FROM `event_rsvp` WHERE `event`='".$this->id."' AND `rsvp` > 0" ) ;
        
            while( ( $i = $db->field() ) ){
                $invited[] = new profile_minion($i) ;
            }
            
            $this->invited = $invited ;
            
        }
        
        return $this->invited ;
        
    }
    
    function get_uninvitable(){
        
    }
    
    function get_blocked(){
        
        if( ! is_array( $this->blocked ) ){
        
            global $db ;
            
            $blocked = array() ;
            
            $db->query( "SELECT `user`, `screen_name` 
                            FROM `event_rsvp`,`profile` 
                            WHERE `event`='".$this->id."' AND `rsvp` = 0
                            AND `event_rsvp`.`user` = `profile`.`user_id`" ) ;
        
            while( ( $i = $db->assoc() ) ){
                $blocked[] = $i ;
            }
            
            $this->blocked = $blocked ;
            
        }
        
        return $this->invited ;
        
    }
    
    function is_invited(){
        
        global $user ;
        global $db ;
        global $mode ;
        
        
        /*
         * "SELECT `invitee` FROM `invitations`
                    WHERE `module`='group'
                        AND `module_item_id`='".$this->id."'
                        AND `user`='".$user->id."'
                        AND `expired` = '0'"
         * 
         * 
         */
        $q = "SELECT `level` FROM `invitations` WHERE " ;
        
        if( $mode = 'module'){
            
            $q .= "`module`='event' AND `module_item_id`='".$this->id."'" ;
            
        } else {
            
            $q .= build_api_where_string() ;
        }
        
        $i = $db->get_field( $q ) ;
    }
}