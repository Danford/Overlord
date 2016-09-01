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
    var $groups ;
    var $groups_in ;
    var $error ;
    var $last_login ;
    var $avatar ;
    
    function __construct(){
        
        $this->friends = array() ;
        $this->groups = array() ; // will hold the group objects

        $this->groups = array() ;
        $this->groups_in = array() ;
        $this->groups_owned = array() ;
        $this->groups_banned = array() ;
        
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
                
                $a= $db->get_assoc( "SELECT `screen_name`, `avatar` FROM `user_profile` WHERE `user_id` = '".$this->id."'" ) ;
                
                $this->name = $a['screen_name'] ;
                $this->avatar = $a['avatar'] ;
        
                $this->load_friends_list() ;
                $this->load_group_membership() ;
                
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

            $a= $db->get_assoc( "SELECT `screen_name`, `avatar` FROM `user_profile` WHERE `user_id` = '".$this->id."'" ) ;
            
            
            $this->avatar = $a['avatar'] ;
            $this->name = $a['screen_name'] ;
        
            $this->load_friends_list() ;
            $this->load_group_membership() ;
            
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
            
                        $db->update( "DELETE FROM `persistent_tokens` WHERE `id`='".$this->id."' and `timestamp`='".$db->field()."'" ) ;
            
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
        
        $query = "SELECT `group_id` FROM `group_members` WHERE `member_id`='".$this->id."'" ;
        
        $db->query( $query ) ;
        
        while( ( $group_id = $db->field() ) != false ){
            $this->groups_in[] = $group_id ;
        }
    }
    
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
    
    function is_logged_in() {
        return ($this->id != 0 ) ;
    }
    

    function require_login() {
    
        if( $this->id == 0 ) {

            die( "[".$_SESSION["user"]->id."]" ) ;
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
    
}