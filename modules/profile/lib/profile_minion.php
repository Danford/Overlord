<?php


class profile_minion {
    
    var $user ;
    var $db ;
    var $id ;
    var $name ;
    var $gender ;
    var $age ;
    var $avatar ;
    var $detail ;
    var $albums ;
    var $friend_count ;
    var $photo_count ;
    var $video_count ;
    var $prose_count ;
    var $album_count ;
    
    function __construct( $id ) {
        
        $this->id = $id ;
        
        global $db ;
        $this->db = $db ;

        global $user ;
        $this->user = $user ;
        
        if( $user->is_blocked( $id ) ){
            $this->name = false ;
        } else {
            $q = "SELECT `screen_name`, `gender`, `avatar`, `birthdate`, `detail`, `friend_count`, 
                        `total_public_photo`, `total_public_prose`, `total_public_video`, `total_public_albums`,
                        `total_private_photo`, `total_private_prose`, `total_private_video`, `total_private_albums`, 
                        `show_age`,`allow_contact`
                        FROM `user_profile`
                        WHERE 
                            `user_profile`.`user_id`='".$this->id."'" ;
         
            $info = $this->db->get_assoc( $q );
            
            if( $info == false ){
                
                $this->name = false ;
                
            } else {
                
                $this->name = $info['screen_name'] ;
                $this->gender = $info['gender'] ;
                $this->detail = $info['detail'] ;
                $this->friend_count = $info['friend_count'] ;
                $this->allow_contact = ( $info['allow_contact'] == 1 ) ;
                $this->avatar = $info['avatar'] ;
                
                if( $info['show_age'] == 1 ){
                    $this->age = user_age( $info['birthdate'] ) ;
                } else {
                    $this->age = 0 ;
                }
            
                if( $user->is_friend( $this->id ) or $user->id == $this->id ){
                    $this->photo_count = $info['total_private_photo'] ;
                    $this->prose_count = $info['total_private_prose'] ;
                    $this->video_count = $info['total_private_video'] ;
                    $this->album_count = $info['total_private_albums'] ;
                } else {
                    $this->photo_count = $info['total_public_photo'] ;
                    $this->prose_count = $info['total_public_prose'] ;
                    $this->video_count = $info['total_public_video'] ;
                    $this->album_count = $info['total_public_albums'] ;
                }
            }
        }
    }

    function profile_picture(){
            return image_link('avatar', $this->avatar ) ;
    }
    
    function profile_thumbnail(){
            return image_link('profilethumb', $this->avatar ) ;
    }

    function get_friends( $offset = 0, $limit = 99999999 ){
        
        global $user ;
        
        if( $this->name == false ){
            return false ;
        } else {
            
            $friend_list = array() ;
            
            $this->db->query( "SELECT `user_profile`.`user_id`, 
                                      `screen_name`, `avatar`,`show_age`,`allow_contact`,`birthdate`,
                                      `friend_count`, `city_id`, `city`, `state`  
                                      `profile_friendship`.`timestamp` 
                                FROM `profile_friendship`, `user_profile`,`location_city`
                                WHERE 
                                 ( ( `profile_friendship`.`friend1` ='".$this->id."' AND
                                    `profile_friendship`.`friend2` =`user_profile`.`user_id` )
                                    OR
                                  ( `profile_friendship`.`friend2` ='".$this->id."' AND
                                    `profile_friendship`.`friend1` =`user_profile`.`user_id` ) )
                                AND
                                  `user_profile`.`city_id` = `location_city`.`id`

                                LIMIT ".$offset.", ".$limit ) ;
            
            while( ( $p = $this->db->assoc() ) != false ){
                
                if( ! $user->is_blocked( $p['user_id'] ) ) {
                    
                    if( $p['show_age'] != 0 )
                        { $p['age'] = user_age( $p['birthdate'] ) ; } 
                    else
                        { $p['age'] = 0 ; }
                    
                    unset( $p['birthdate'] ); 
                    
                    if( $user->is_friend($p['user_id']) ){
                        $p['friend'] = 1 ;
                    } else { 
                        $p['friend'] = 0 ;
                    }
                    
                    $friend_list[] = $p ;
                }
            }
        return $friend_list ;
        }
        
    }
    
    function get_album_list( $offset = 0, $limit = 99999999 ){
        
        if( $this->name == false ){
            return false ;
        } else {
            
            if( $this->user->is_friend( $this->id ) or $this->user->id == $this->id ) {
                $q = "SELECT `album_id`, `title`, `description`, `last_updated`,
                      `private_photo` as `photos`, `private_prose` as `prose`, `private_video` as `videos` 
                    FROM `profile_albums`
                    WHERE `owner` = '".$this->id."' 
                        AND `private_photo` + `private_video` + `private_prose` != 0 
                    ORDER BY `last_updated` DESC
                    LIMIT ".$offset.", ".$limit;
            } else {
                $q = "SELECT `album_id`, `title`, `description`, `last_updated`,
                      `public_photo` as `photos`, `public_prose` as `prose`, `public_video` as `videos` 
                    FROM `profile_albums`
                    WHERE `owner` = '".$this->id."'
                        AND `public_photo` + `public_video` + `public_prose` != 0 
                    ORDER BY `last_updated` DESC
                    LIMIT ".$offset.", ".$limit;
            }
            
            $list = array() ;
            
            $this->db->query( $q ) ;
            
            while( ($a = $this->db->assoc() ) != false ){
                $list[] = $a ;
            }
            
            return $list ;
        }
        
    }
    
    function get_photo_list( $offset = 0, $limit = 99999999, $album = 0 ) {
        
        if( $this->name == false ){ 
            return false ;
        } else {
            
            if( $this->user->is_friend( $this->id ) or $this->user->id == $this->id ) {
                $q = "SELECT `photo_id`, `title`, `description`, `timestamp`, `likes`, `comments`
                    FROM `profile_photo`
                    WHERE `owner` = '".$this->id."'" ;
            } else {
                $q = "SELECT `photo_id`, `title`, `description`, `timestamp`, `likes`, `comments`
                    FROM `profile_photo`
                    WHERE `owner` = '".$this->id."' and `private`='0'" ;
            }
            
            if( $album != 0 ) {
                $q .= " AND `album`='".$album."' " ;
            }
            
            $q .= " ORDER BY `timestamp` DESC LIMIT ".$offset.", ".$limit ;
            
            $list = array() ;
            
            $this->db->query( $q ) ;
            
            while( ($a = $this->db->assoc() ) != false ){

                $a['title'] = prevent_html($a['title'] );
                $a['description'] = process_user_supplied_html( $a['description'] );
                $a['thumbnail'] = image_link('user_thumb', $a['photo_id'] ) ;
                
                $list[] = $a ;
            }
            
            return $list ;
        }
        
    }
    
    function get_album( $id ){
        
        if( $this->name == false ) {
            return false ;
        } else {
            
            if( $this->user->is_friend($this->id ) OR $this->id == $this->user->id ) {
            
                $q = "SELECT `album_id`, `title`,`description`,`last_updated`,
                        `private_photo` as `photos`, `private_video` as `videos`, `private_prose` as `prose`
                        FROM `profile_albums` WHERE `album_id`='".$id."' AND `owner`='".$this->id."'" ;
            } else {
            
                $q = "SELECT `album_id`, `title`,`description`,`last_updated`,
                        `public_photo` as `photos`, `public_video` as `videos`, `public_prose` as `prose`
                        FROM `profile_albums` WHERE `album_id`='".$id."' AND `owner`='".$this->id."'" ;
            }
            
            if( ( $album = $this->db->get_assoc($q) ) == false  ){
                return false ;
            } else {

                $album['title'] = prevent_html($album['title']) ;
                $album['description'] = process_user_supplied_html($album['description']) ;
                               
                return $album ;
            }
        }
    }
    
    function get_photo( $id ){
        
        if( $this->name == false ) {
            return false ;
        } else {
            
            $q = "SELECT `photo_id`, `title`,`description`,`timestamp`,`comments`,`likes`, `private`, `album` 
                    FROM `profile_photo` WHERE `photo_id`='".$id."' AND `owner`='".$this->id."'" ;
            
            if( ( $photo = $this->db->get_assoc($q) ) == false  ){
                return false ;
            } elseif ( $photo['private'] == 1 AND  ! $this->user->is_friend($this->id ) AND $this->id != $this->user->id ) {
                return false ;
            } else {

                $photo['title'] = prevent_html($photo['title']) ;
                $photo['description'] = process_user_supplied_html($photo['description']) ;
                $photo['url'] = image_link('userphoto', $photo['photo_id'] ) ;
                
                if( $photo['album'] != '' and $photo['album'] != null ){
                    
                    $photo['album_name'] = $this->db->get_field( "SELECT `title` FROM `profile_albums` 
                        WHERE `album_id`='".$photo['album']."'" ) ;
                    
                } else {
                    $photo['album_name'] = '' ;
                }
                
                return $photo ;
            }
        }
    }
    
    
    function get_prose_list( $offset = 0, $limit = 99999999, $album = 0 ) {
        
        if( $this->name == false ){
            return false ;
        } else {
            
            if( $this->user->is_friend( $this->id ) or $this->id == $this->user->id ) {
                $q = "SELECT `prose_id`, `title`, `subtitle`, `timestamp`, `likes`, `comments`
                    FROM `profile_prose`
                    WHERE `owner` = '".$this->id."'" ;
            } else {
                $q = "SELECT `prose_id`, `title`, `subtitle`, `timestamp`, `likes`, `comments`
                    FROM `profile_prose`
                    WHERE `owner` = '".$this->id."' and `private`='0'" ;
            }
            
            if( $album != 0 ) {
                $q .= " AND `album`='".$album."' " ;
            }
            
            $q .= " ORDER BY `timestamp` DESC LIMIT ".$offset.", ".$limit ;
            
            $list = array() ;
            
            $this->db->query( $q ) ;
            
            while( ($a = $this->db->assoc() ) != false ){
                $a['title'] = prevent_html($a['title'] );
                $a['subtitle'] = prevent_html($a['subtitle'] );
                $list[] = $a ;
            }
            
            return $list ;
        }
        
    }
    
    function get_prose( $id ) {
        
        if( $this->name == false ) {
            return false ;
        } else {
        
            $q = "SELECT `prose_id`, `title`, `subtitle`, `content`, `timestamp`, `comments`, `likes`, `private`, `album` 
                    FROM `profile_prose` 
                    WHERE `prose_id`='".$id."' AND `owner`='".$this->id."'" ;
        
            if( ( $prose = $this->db->get_assoc($q) ) == false  ){
                return false ;
            } elseif ( $prose['private'] == 1 AND ! $this->user->is_friend($this->id ) AND $this->id != $this->user->id ){
                return false ;
            } else {
                
                $prose['title'] = prevent_html($prose['title']) ;
                $prose['subtitle'] = prevent_html($prose['subtitle']) ;
                $prose['content'] = process_user_supplied_html($prose['content']) ;


                if( $prose['album'] != '' and $prose['album'] != null ){
                
                    $prose['album_name'] = $this->db->get_field( "SELECT `title` FROM `profile_albums`
                        WHERE `album_id`='".$prose['album']."'" ) ;
                
                } else {
                    $prose['album_name'] = '' ;
                }        
                return $prose ;
            }
        }
    }
    
    function get_video_list( $offset = 0, $limit = 99999999, $album = 0 ) {
        // placeholder function until video mechanics have been implemented
    }
    
    function get_video() {
        // placeholder function until video mechanics have been implemented    
    }

    function get_comments( $type, $id, $offset = 0, $limit = 99999999 ){
    
        if( ! in_array( $type, ['photo','video','prose'] ) ){
            return false ;
            
        } else {
    
            $list = array() ;
    
            $q = "SELECT `comment_id`, `user_id`, `comment`, `timestamp` FROM profile_".$type."_comment
                    WHERE `".$type."_id`='".$id."'
                     ORDER BY `timestamp` LIMIT ".$offset.", ".$limit ;
    
            $this->db->query( $q ) ;
    
            while( ( $comment = $this->db->assoc() ) != false ){
    
                if( ! $this->user->is_blocked($comment['user_id'] ) ) {
    
                    $comment['comment'] = process_user_supplied_html( $comment['comment'] ) ;
                    $comment['user'] = new profile_minion( $comment['user_id'] ) ;
                    unset( $comment['user_id'] ) ;
    
                    $list[] = $comment ;
                }
            }
    
            return $list ;
        }
    }
    
    function get_likes( $type, $id, $offset = 0, $limit = 99999999 ){
        
        if( ! in_array( $type, ['photo','video','prose'] ) ){
            return false ;
        } else {
            
            $list = array() ;
            
            $q = "SELECT `liked_by`, `timestamp` FROM profile_".$type."_like
                    WHERE `".$type."_id`='".$id."'
                     ORDER BY `timestamp` LIMIT ".$offset.", ".$limit ;
            
            $this->db->query( $q ) ;
            
            while( ( $like = $this->db->assoc() ) != false ){
                
                if( ! $this->user->is_blocked( $like['user_id'] ) ) {
                    
                    $like['user'] = new profile_minion( $like['liked_by'] ) ;
                    unset( $like['liked_by'] ) ;
                    
                    $list[] = $like ;
                }
            }
            
            return $list ;
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
                // requested has been made of this profile by the user
                return "outgoing" ;
            } else {
                // this profile has requested friendship with the user
                return "incoming" ;
            }
        }
    }
}