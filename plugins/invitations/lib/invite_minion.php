<?php

include( $oe_plugins['invitations']."conf/conf.php" ) ;

class invite_minion {

    var $view ;
    var $table ;
    var $target ;
    var $invitables ;
    
    function __construct(){

        global $oepc ;
        global $tier ;

        $this->table = $oepc[$tier]['invitations']['table'] ;
        $this->view = $oepc[$tier]['invitations']['view'] ;
        $this->target = $oepc[$tier]['invitations']['parentObject'] ;
    }
    
    function get_invitables() {
        
        global $user;
        
        if (!is_array($this->invitables)) {
        
            $people = $user->get_friends_as_array();
            $groups = array();
            
            foreach($user->groupPermmisions as $id => $groupPermmision) {
            	if ($groupPermmision == 3 /* is 2 admin? */)
            		$groups[] = new group_minion($id, true);
            }   
            $this->invitables = ['people' => $people , 'groups' => $groups];
        }
        return $this->invitables;
    }
    
    function get_invited() {
        
        global $db ;
        
        $response = array() ;
        
        if( $oepc[0]['admin']){
         
            $db->query( "SELECT `invitee` FROM `".$this->table."` WHERE ".build_api_where_string() ) ;
            
            while( ( $i = $db->field() ) != false ){
                $response[] = $i ;
            }            
        }
        
        return $response ;
    }
    
    function get_invited_as_objects() {
        
        global $db ;
        
        $response = array() ;
        
        if( $oepc[0]['admin']){
         	$query = "SELECT `invitee` FROM `".$this->table."` WHERE ".build_api_where_string();
         	
            $db->query($query);
            
            while( ( $i = $db->field() ) != false ){
                $response[] = new profile_minion( $i ) ;
            }            
        }
        
        return $response ;
    }
    
    
    function invite_groups( array $groups ){
        
        global $db ;
        global $oepc ;
        global $user ;
        
        $count = 0 ;
        
        $uninvitable = array_merge( $this->get_invited(), $this->target->get_blocked() ) ;
        $invited = array() ;
                
        if( $oepc[0]['admin'] ){
            
            foreach( $groups as $g ){
                
                if( verify_number( $g ) ){
                    $group = new group_minion($g) ;
                    
                    if( $group->membership == 2 ){
                    
                        if( ! in_array ( $group->owner, $uninvitable ) and ! in_array ( $group->owner, $invited )){
                            $to_invite[] = $group->owner ;
                        }
                        
                        $db->query( "SELECT `owner` FROM `group_membership` WHERE `group`='".$g."' and `access` !='0'" )   ;
                        
                        while( ( $u = $db->field() ) != false ){
                            if( ! in_array ( $u, $uninvitable ) and ! in_array ( $u, $to_invite )){
                                $invited[] = $u ;
                                $db->insert( "INSERT INTO `".$this->table."` SET ".build_api_set_string().",
                                                `invitee`='".$id."', ".$db->build_set_string_from_array($o) ) ;
                                $count++ ;                                
                            }
                        }
                    }
                }                
            }
        }
        
        return $count ;
    }
    
    function invite_users( array $users){
        
        global $db ;
        global $oepc ;
        global $user ;
        
        $o['invitor'] = $user->id ;
        $o['timestamp'] = oe_time() ;
        $o['ip'] = get_client_ip() ;
        
        if( $this->target->privacy == 1 or $oepc[0]['admin'] ){            
            $o['level'] = '0' ;
        } elseif( $oepc[0]['contributor']) {
            $o['level'] = '1' ;
        }
        
        $count = 0 ;
        
        if( isset( $o['level'] ) ){
        	// maybe this should return all possibly invitable people? It doesn't though. Commenting this out.
            //$safe = $this->get_invitable_ids();
            
            foreach( $users as $id ){
            	
            	// related to above verification on allowed users to invite.
                //if( verify_number( $user ) and in_array( $user, $safe )){
                    $db->insert( "INSERT INTO `".$this->table."` SET ".build_api_set_string().",
                                    `invitee`='".$id."', ".$db->build_set_string_from_array($o) ) ;
                    $count++ ;
            
                //}
            }
        }        
        return $count ;
    }

    function get_moderatables(){
        
        global $oepc ;
        global $db ;
        
        $response = array() ;
        
        if( $oepc[0]['admin'] ){
        
            $db->query( "SELECT `invitee`,`invitor`, `timestamp`, `level` FROM `".$this->table."` 
                            WHERE ".build_api_where_string()." AND `level` > 0" ) ;
            
            while( ( $i = $db->assoc() != false ) ) {

                $i['invitee'] = new profile_minion( $i['invitee'], true ) ;
                
                if( $i['level']  == 1 ) {
                    $i['invitor'] = new profile_minion( $i['invitor'], true ) ;
                }
                
                $response[] = $i ;
            }
        }        
        return $response ;
    }
    
    function approve_invites( array $invitees ){
        
        global $oepc ;
        $count = 0 ;
        
        if( $oepc[0]['admin'] ){
            
            foreach( $invitees as $invitee ){
                
                if( verify_number( $invitee ) ){
                    
                    $grab = $db->update( "UPDATE `".$this->table."` SET `level`='0' WHERE
                        ".build_api_where_string()." AND `invitee`='".$invitee."'" ) ;
                    
                    if( $grab != false ){
                        $count++ ;
                    }                    
                }                
            }            
        }        
        return $count ;
    }

    function uninvite( array $users ){
        global $db ;
        global $oepc ;
    
        $count = 0 ;
    
        if( $oepc[0]['admin'] ){
        	
            foreach( $users as $u ){
        
                if( verify_number( $u ) ){
                
                $count += $db->update( "DELETE FROM `".$this->table."` 
                                        WHERE ".build_api_where_string()." AND `invitee`='".$u."'" ) ;
                }
            }
        }
        return $count ;
    }

    function acceptInvite($group) {
    	global $db ;
    	$query = "SELECT * FROM `".$this->table."`
                            WHERE ".build_api_where_string()." AND `level` = 0 AND `module_item_id` = ". $group->id;
    	
    	die("not finished");
    	die($query);
    	$db->query($query) ;

    	if ($db->count("DEFAULT") == 1)
    	{
    		die("yay!");
    	}
    }
    
    function declineInvite($group) {
    	global $db;
    	
    	die("not finished");
    }
}