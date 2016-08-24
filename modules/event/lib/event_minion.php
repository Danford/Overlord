<?php

class event_minion{
    
    var $id ;
    var $access ;
    var $title ;
    var $subtitle ;
    var $start ;
    var $end ;
    var $type ;
    var $organiser ;
    var $location ;
    var $address ;
    var $cost ;
    var $dress ;
    var $detail ;
    var $group ;
    var $rsvp ;
    
    
    function __construct( $id ){
        
        global $db ;
        
        verify_number( $id ) ;
        
        $this->id = $id ;
        
        $detail = $db->get_assoc( "SELECT `title`,`subtitle`,`start`,`end`,`type`,`organiser`,`location`,`address`,`cost`,`dress`,`detail`,`group` 
                FROM `event_profile` WHERE `event_id`='".$id."'" ) ;
        
        if( $detail == false ){
            $this->access = false ;
        } else { 

            foreach( $detail as $key => $val ){
                $this->$key = $val ;
            }

            if( $this->group != null ){
                $this->group = new group_minion( $detail['group'] ) ;
            }
            
            if( $this->is_blocked() ) {            
                $this->access == false ;
            } else {
                
                switch( $detail['type'] ){
                    
                    case 3:
                    case 0:
                        
                        // secret or group only
                        
                        if( $this->is_invited() ){
                            $this->access = "full" ;
                        } else {
                            $this->access = false ;
                        }
                            
                    break ;
                    
                    case 2:
                        
                        // closed
                        
                        if( $this->is_invited() ){
                            $this->access = "full" ;
                        } else {
                            $this->access = "view" ;
                        }
                            
                    break ;
                    
                    case 1:
                        
                        // public
                        
                        $this->access = "full" ;
                        $this->is_invited() ; //we don't need the result now, but might need rsvp later, which this sets
                        
                    break ;
                    
                }
            }
        }    
        
    }
    
    function is_blocked(){
        
        global $db ;
        global $user ;
        
        if( $this->organiser != null and $user->is_blocked( $organiser ) ){
            return true ;        
        } elseif ( $this->group != null and $this->type == 0 and $this->group->access == false ) { 
            // it's a group only event and they don't have access to the group
            return true ;
        } else {
            return ( $db->get_field("SELECT COUNT(*) FROM `event_block` WHERE `event_id`='".$this->id."' AND `blocked_user`='".$user->id."'" ) > 0 ) ;
        }
    }
    
    function is_invited(){
        
        global $db ;
        global $user ;
        
        $this->rsvp = $db->get_field("SELECT `rsvp` FROM `event_invite` WHERE `event_id`='".$this->id."' AND `invitee`='".$user->id."' AND `type`='0'" ) ;
        
        if( $this->rsvp == false and $this->type == 0 and $this->group->access == "full" ){
            $db->insert( "INSERT INTO `event_invite` SET `event_id`='".$this->id."', `invitee`='".$user->id."', `rsvp`='0', `type`='0'" ) ;
            $this->rsvp = 0 ;
        } elseif ( $this->rsvp == false and $this->type == 1 ) {
            $this->rsvp = 0 ;
        }
        
        return ( $this->rsvp != false ) ;
        
    }
    
    function set_rsvp( $r ) {
        
        // if rsvp == false, they don't have access to the event unless the event is public
        // if null, it's a public event, and invite hasn't been checked yet
        
        
        if( $this->rsvp != false ){
            global $db ;
            global $user ;
            
            $db->update( "UPDATE `event_invite` SET `rsvp='".$r."' WHERE `event_id`='".$this->id."' AND `invitee`='".$user->id."'") ;
            
        } elseif ( $this->type == 1 ) {
            
                // public event
            
            if( $r == 2 ){
                // public events don't list 'not going' because damn.
                $db->update( "DELETE FROM `event_invite` WHERE `event_id`='".$this->id."' AND `invitee`='".$user->id."'") ;
                
            } else {
                
                if( $this->is_invited() ){
                    // an invite already exists
                    $db->update( "UPDATE `event_invite` SET `rsvp='".$r."' WHERE `event_id`='".$this->id."' AND `invitee`='".$user->id."'") ;
                } else {
                    $db->insert( "INSERT INTO `event_invite` SET `event_id`='".$this->id."', `invitee`='".$user->id."', `rsvp`='".$r."', `type`='0'" ) ;                
                }
            }
        }
    }
    
    function block_user( $id ){
        global $db ;
        global $user ;
        
        $db->insert( "INSERT INTO `event_block` SET `event_id`='".$this->id."', `blocked_user`='".$id."', `blocked_by`='".$user->id."'" ) ;
    }
    
    function unblock_user( $id ){
        global $db ;
        
        $db->update( "DELETE FROM `event_block` WHERE `event_id`='".$this->id."' AND `blocked_user`='".$id."'" ) ;
    }
     
}