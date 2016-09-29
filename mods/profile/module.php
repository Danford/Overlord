<?php

$privacylevel = 1 ;
$privacyoptions = [ 1 => 'All Registered Users', 2 => 'Friends Only' ] ;

// nothing below this line should be edited

if( $privacylevel > 0 ){ $user->require_login() ; }

$pagedir = $oe_modules['profile']."pages/" ;


// USER ONLY SECTION

    // things are specific to the logged in user.

switch( $uri[$pos] ) {
    
    case './final':

        // no id specified-- they're trying to edit their own profile
        
        include( $pagedir."editor.php" ) ;
        die() ;
        
    
    case "block_list":

        include( $pagedir."show_blocked.php" ) ;
        die() ;

}

// everything else is for a profile, so the next number should be a user id.

if( ! verify_number($uri[$pos] )  or $user->is_blocked($uri[$pos]) ) {

    // it's not a number, or it's a user they don't need to see.    
    
    include( $pagedir."404.php" ) ;
    die() ;
}

$profile = new profile_minion($uri[$pos]) ;

if( $profile->name == false ) {

    // it's a number -- but not one that corresponds to a user

    include( $pagedir."404.php" ) ;
    die() ;
}

if( $user->is_friend( $profile->id )){
    $accesslevel = 2 ;  // friends can access level 2 (friends only) content
} else {
    $accesslevel = 1 ;  // not so much
}

$pos++ ;

switch( $uri[$pos] ) {
        
        // module specific sub pages of user profile

    case './final':

        include( $pagedir."show_profile.php" ) ;
        die() ;

    case 'friends':

        $pos++ ;
        include( $pagedir."show_friends.php") ;
        die();

} //switch



// still nothing?  Let's check for a plugin.

if( isset( $oe_plugins[$uri[$pos] ] ) ){

        include( $oe_module['profile']."conf/plugin.conf.php" ) ;
    
    if( in_array($uri[$pos], $oepc[0]['plugins'] )){
            
        $pos++; 
        
        include( $oe_plugins[$uri[$pos - 1]]."plugin.php" ) ;
        
        // does not die() to allow for bombing out to the main 404.
    }
}

// if we've reached here, then they get the 404 page for the profile section

include( $pagedir."404.php" ) ;
die() ;