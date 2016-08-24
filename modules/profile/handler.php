<?php

$user->require_login() ;

$pagedir = $oe_modules['profile']."pages/" ;

if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ) {

    // no id specified-- they're trying to access their own profile
    
    include( $pagedir."editor.php" ) ;
    die() ;
}


if( verify_number( $uri[$pos]) ){

    // this is (at least in theory) an attempt to view a profile

    if( ! $user->is_blocked( $uri[$pos] ) ) {
        // the above makes sure this isn't a profile that has blocked them, or vice versa

        // load the user profile ;
        include $oe_modules['profile']."lib/profile_minion.php" ;
        $profile = new profile_minion($uri[$pos]) ;



        if( $profile->name != false ) {

            $pos++ ;

            if( ! isset( $uri[$pos]) or $uri[$pos] == "" ){

                include( $pagedir."show_profile.php" ) ;
                die() ;

            } else {

                switch( $uri[$pos] ) {

                    case "photos":

                        include( $pagedir."list_photos.php" ) ;
                        die() ;
                         
                    case "photo":

                        $pos++ ;

                        if( preg_match( '/^[0-9]*$/', $uri[$pos] ) != 0 )
                        {
                            $photo = $profile->get_photo( $uri[$pos] ) ;

                            if( ( $photo != false ) ) {

                                include( $pagedir."show_photo.php" ) ;
                                die() ;
                            }
                        }

                        break ;
                    case "albums":

                        $pos++ ;

                        if( ! isset( $uri[$pos]) or $uri[$pos] == "" ){

                            include( $pagedir."list_albums.php" ) ;
                            die() ;

                        } elseif( preg_match( '/^[0-9]*$/', $uri[$pos] ) != 0 ) {

                            $album = $profile->get_album( $uri[$pos] ) ;

                            if( $album != false ){

                                include( $pagedir."show_album.php" ) ;
                                die() ;
                            }

                        }

                        break;

                    case "writing":

                        $pos++ ;

                        if( ! isset( $uri[$pos]) or $uri[$pos] == "" ){

                            include( $pagedir."list_writing.php" ) ;
                            die() ;

                        } elseif( preg_match( '/^[0-9]*$/', $uri[$pos] ) != 0 ) {

                            $writing = $profile->get_prose($uri[$pos]) ;

                            if( $writing != false ){

                                include( $pagedir."show_writing.php" ) ;
                                die() ;
                            }

                        }

                    case "videos":

                        $pos++ ;

                        if( ! isset( $uri[$pos]) or $uri[$pos] == "" ){

                            include( $pagedir."list_videos.php" ) ;
                            die() ;

                        } else {

                            include( $pagedir."show_videos.php" ) ;
                            die() ;

                        }

                    case "albums":

                        include( $pagedir."list_photos.php" ) ;
                        die() ;

                    case "album":

                        $pos++ ;

                        if( preg_match( '/^[0-9]*$/', $uri[$pos] ) != 0 )
                        {


                            $album = $profile->get_album( $uri[$pos] ) ;

                            if( ( $album != false ) ) {

                                include( $pagedir."show_album.php" ) ;
                                die() ;
                            }
                        }

                        break ;

                } //switch
            } // else ( uripos isn't empty )
        } // profile has match if
    }
} // verified profile attempt
else
{

    // no verified profile... maybe editor functions?
    
    switch( $uri[$pos] ) {
        
        case "block_list":
            
            include( $pagedir."show_blocked.php" ) ;
            die() ;
    
    
        case "upload_photo":
        
            include( $pagedir."upload_photo.php" ) ;
            die() ;
    
    
        case "editphoto":
            $pos++ ;
                    
            if( verify_number( $uri[$pos]) ){
                $photo = $db->get_assoc( "SELECT `photo_id`, `title`, `description`, `private`, `album` FROM `profile_photo`
                    WHERE `owner`='".$user->id."' AND `photo_id`='".$uri[$pos]."'") ;
                
                if( $photo != false ){
                    
                    if( $photo['photo_id'] == $user->avatar ){
                        $photo["setavatar"] = 'on' ;
                    }
                    
                    if( $photo['album'] == '' ){
                        $photo['album'] == 'None' ;
                    }
                    
                    include( $pagedir."edit_photo.php" ) ;
                    die() ;
                }
                
            }
            
            break; 
            
        case 'write':
            
            include( $pagedir."write.php" );
            die() ;
            
        case 'editwriting':
            $pos++ ;
            if( verify_number( $uri[$pos]) ){
                
                $writing = $db->get_assoc( "SELECT `prose_id`, `title`, `subtitle`, `content`, `private`, `album` FROM `profile_prose`
                    WHERE `owner`='".$user->id."' AND `prose_id`='".$uri[$pos]."'") ;
                
                if( $writing != false ){
                    
                    if( $writing['album'] == '' ){
                        $writing['album'] == 'None' ;
                    }
                    
                    include( $pagedir."edit_writing.php" ) ;
                    die() ;
                }
            }
    }

}

// if we've reached here, then they get the 404 page for the profile section

include( $pagedir."404.php" ) ;
die() ;