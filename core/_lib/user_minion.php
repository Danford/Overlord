<?php


class user_minion {
    
    /*
     *  This module is part of the overlord engine and requires 
     *  the mysqli_minion to be instantiated as $db 
     */
    
    var $id ;
    var $name ;
    var $friends ;
    var $blocked ;
    var $error ;
    var $last_login ;
    var $avatar ;
    var $groups_in ;
    var $groups_owned ;
    var $groups_administered ;
    var $groups_blocked ;
    var $timezone ;
    var $dst ;
    
    function __construct(){
        
        $this->friends = array() ;;
        
        // if there is a valid login token, log user in
        
        if( isset( $_COOKIE["id"] ) and isset( $_COOKIE["token"] ) ) {
        
            global $db ;
            
            if( $db->get_field( "SELECT COUNT(*) FROM `persistent_tokens` WHERE `user_id`='".$db->sanitize( $_COOKIE['id'])."' AND `token`='".$db->sanitize( $_COOKIE['token'] )."'" ) == 1 ) {
        
                $query = "SELECT `user_id`, `status`, `passhash` , `login_fails`, `last_login`
                            FROM
                        `user_account`
                            WHERE
                        `user_id`='".$db->sanitize( $_COOKIE['id'])."'" ;
        
                $a = $db->get_assoc( $query ) ;
        
                $query = "UPDATE `user_account` SET `login_fails`='0', `last_login`='".oe_time()."'" ;
        
                if( $a['status'] == 2 ){
                    $query .= ", `status`='2'" ;
                }
        
                $query .= " WHERE `user_id`='".$a['user_id']."'" ;
        
                $db->update( $query ) ;
        
                $this->id = $a['user_id'] ;
                
                $this->load_profile() ;
                
            } else {
        
                // no such token... eat the cookie
                
                setcookie( 'id', '', 0, '/', "www.codexfive.net", true ) ;
                setcookie( 'token', '', 0, '/', "www.codexfive.net", true ) ;
        
                $this->id = 0 ;
            }
        
        } else {
        
            // no cookie
        
            $this->id = 0  ;
        }
        
    }
    
    function login( $email, $password, $persist ){
        
        global $db ;
        
        $query = "SELECT `user_id`, `status`, `passhash` , `login_fails`, `last_login`
                                FROM
                            `user_account` 
                                WHERE 
                            `email`='".$db->sanitize( $email )."'" ;
        
        $a = $db->get_assoc( $query ) ;
        
        if( $a == false ) {
            
            // no such account
            
           $error="Invalid login." ;
            
        } elseif ( ( $a['status'] == 3 ) ) {
        
            // account has been suspended by admin
        
            $error="This account has been suspended." ;
        
        } elseif ( ( $a['login_fails'] == max_login_fails  ) ) {
        
            // account has been locked for too many attempts
        
            $error="This account has been locked because of too many invalid logins.  Please reset your password." ;
        
        } elseif( $a['passhash'] != hash_hmac( "sha256", $password, oe_seed ) ) {
            
            // good account, bad password
            
            if( ( $a['login_fails'] + 1 ) == max_login_fails ) {
                
                $error="This account has been locked because of too many invalid logins.  Please reset your password." ;            
            
            } else {
            
                $error="Invalid login." ;
            }
            
            $db->update( "UPDATE `user_account` SET `login_fails`='".( $a['login_fails'] + 1 )."' WHERE `user_id`='".$a['user_id']."'" ) ;
        }
        
        if( isset( $error ) ) {
            $this->error = $error ;
            return false ;
            
        } else {

            $this->id = $a['user_id'] ;
            $this->last_login = $a['last_login'] ;

            $a= $db->get_assoc( "SELECT `screen_name`, `avatar` FROM `profile` WHERE `user_id` = '".$this->id."'" ) ;
            
            
            $this->avatar = $a['avatar'] ;
            $this->name = $a['screen_name'] ;

            $this->load_profile() ;
            
            //update last logged in and reset failed logins to zero
            
            $db->update( "UPDATE `user_account` SET `last_login`='".oe_time()."', `login_fails`='0'
                            WHERE `user_id`='".$this->id."'" ) ;
            
            if( $persist == "on" ) {
            
                // user has elected to stay logged in even if the session expires
            
                // first, lets see how many tokens are already in the system
            
                $db->query( "SELECT `timestamp` FROM `persistent_tokens`
                                        WHERE `user_id` = '".$this->id."'
                                        ORDER BY timestamp ASC" ) ;
            
                $tokencount = $db->num_rows() ;
            
                if( $tokencount >= max_login_tokens ) {
            
                    // there are too many tokens-- remove tokens until there are one less than max_login_tokens
            
                    for( $i = ( $tokencount - max_login_tokens + 1 ) ; $i > 0 ; $i-- ){
            
                        $db->update( "DELETE FROM `persistent_tokens` WHERE `user_id`='".$this->id."' and `timestamp`='".$db->field()."'" ) ;
            
                    }
                }
            
                $db->free() ;
            
                $token = hash_hmac( "sha256", $_POST['email'].oe_time(), oe_seed ) ;
            
            
                $q = "INSERT INTO `persistent_tokens` SET `user_id`='".$this->id."', `token`='".$token."', timestamp='".oe_time()."'" ;
            
                $db->insert( $q ) ;
            
                setcookie( "id", $this->id, time() + ( 60 * 60 * 24 * persistent_login_duration ), '/', 'www.codexfive.net', true ) ;
                setcookie( "token", $token, time() + ( 60 * 60 * 24 * persistent_login_duration ), '/', 'www.codexfive.net', true ) ;
            }
            
            return true ;
        }
    }
    
    function load_friends_list() {
    
        global $db ;
        
        $this->friends = array() ;
    
        $db->query( "SELECT `friend1`, `friend2`, `timestamp` from `profile_friendship`
                        WHERE `friend1` ='".$this->id."'
                           OR `friend2` ='".$this->id."'" ) ;
    
        while( ( $f = $db->assoc() ) != false ){
    
            if( $f['friend1'] == $this->id ){
    
                $this->friends[$f['friend2']] = $f['timestamp'] ;
            } else {
    
                $this->friends[$f['friend1']] = $f['timestamp'] ;
            }
    
        }
    
    
        $this->blocked = array() ;
    
        $db->query( "SELECT `blocker`, `blockee` from `profile_block`
                        WHERE `blocker` ='".$this->id."'
                           OR `blockee` ='".$this->id."'" ) ;
    
        while( ( $f = $db->assoc() ) != false ){
    
            if( $f['blockee'] == $this->id ){
    
                $this->blocked[] = $f['blocker'] ;
            } else {
    
                $this->blocked[] = $f['blockee'] ;
            }
    
        }
    
    }
    

    /**
     * Returns a comma-delimited list of the user's friends
     *
     * @return string
     */
    
    function load_group_membership(){
        
        global $db ;
        
        $this->groups_in = array() ;
        $this->groups_owned = array() ;
        $this->groups_administered = array() ;
        
        $db->query ( "SELECT `id` FROM `group` WHERE `owner`='".$this->id."'" ) ;
        
        while( ( $group_id = $db->field() ) != false ){
            $this->groups_in[] = $group_id ;
            $this->groups_owned[] = $group_id ;
            $this->groups_administered[] = $group_id ;
        }
        
        $query = "SELECT `group`,`access` FROM `group_membership` WHERE `user`='".$this->id."'" ;
        
        $db->query( $query ) ;
        
        while( ( $group = $db->assoc() ) != false ){
            
            if( $group['access'] == 0 ){
                
                $this->groups_blocked[] = $group['group'] ;
            
            } else {
            
                $this->groups_in[] = $group['group'] ;
                
                if( $group['access'] > 1 ){
                    $this->groups_administered = $group['group'] ;
                }
            }
        }
    }
        
    function is_logged_in() {
        return ($this->id != 0 ) ;
    }
    

    function require_login() {
    
        if( $this->id == 0 ) {
            include( oe_core."login/pages/requirelogin.php" ) ;
            die() ;
        }
    }
    
    function is_friend( $user_id ) {
        return ( isset( $this->friends[$user_id] )) ;
    }
    
    function is_blocked( $user_id ) {    
        return ( isset( $this->blocked[$user_id] )) ;
    }
    
    function load_profile(){
    	global $db;
    	
        $a= $db->get_assoc( "SELECT `screen_name`, `avatar` FROM `profile` WHERE `user_id` = '".$this->id."'" ) ;
        
        $this->name = $a['screen_name'] ;
        $this->avatar = $a['avatar'] ;

        $this->load_friends_list() ;
        $this->load_group_membership() ;
        $this->get_location_info() ;
    }
    
    function get_location_info(){
        
        global $db ;
        
        $q = "SELECT `timezone`,`dst` FROM `user_location`, `location_zip`
            WHERE `user_id`='".$this->id."' AND `primary`='1' 
              AND `user_location`.`zip` = `location_zip`" ;
        
        $l = $db->get_assoc($q) ;

        $this->timezone = $l['timezone'] ;
        $this->dst = $l['dst'] ;
        
    }
    
    function get_friends_as_array( $offset = 0, $limit = 99999999 ){
    
        $array_list == array() ;
        
        foreach( $this->friends as $friend ){
            $array_list = new profile_minion( $friend );
        }
        
        return $array_list ;
        
    }
    
    function get_blocked_as_array(){
        
        global $db ;
        
        // this is a list of people THIS USER has blocked.  They cannot see who has blocked them.  
        
        $db->query( "SELECT `blocker`, `screen_name` from `profile_block`, `profile`
                        WHERE `blocker` ='".$this->id."' and `blockee`=`profile`.`user_id` " ) ;
        
        if( $db->count() == 0 ){
            return false ;
        } else {
            
            while (( $p = $db->assoc() ) != false ){
                $blocked[] = $p ;
            }
            
            return $blocked ;
        }
    } /**
     * Returns a comma-delimited list of the user's friends
     *
     * @return string
     */
    
    
    function is_in_group( $group_id ){
        return in_array($group_id, $this->groups_in ) ;
    }
    
    /**
     * Returns a comma-delimited list of groups the user is a member of
     * 
     * @return string
     */
    function group_list(){
        
        $list = '' ;
        foreach( $this->groups_in as $group_id ){
            $list .= $group_id.',' ;
        }
        return substr( $list, 0, -1 );
    }
    
    /**
     * Returns a comma-delimited list of user's friends
     * 
     * @return string
     */
    function friends_list(){
        
        $list = '' ;
        foreach( $this->friends as $id => $ignored ){
            $list .= $id.',' ;
        }
        return substr( $list, 0, -1 );
    }
}