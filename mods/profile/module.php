<?php

$privacylevel = 1 ;
$privacyoptions = [ 1 => 'All Registered Users', 2 => 'Friends Only' ] ;

// nothing below this line should be edited

if( $privacylevel > 0 ){ $user->require_login() ; }

$pagedir = $oe_modules['profile']."pages/" ;


// USER ONLY SECTION

    // things are specific to the logged in user.

if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ) {

    // no id specified-- they're trying to access their own profile
    
    include( $pagedir."editor.php" ) ;
    die() ;
}

switch( $uri[$pos] ) {
    
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

if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ){

    include( $pagedir."show_profile.php" ) ;
    die() ;

} else {
    
    switch( $uri[$pos] ) {
        
        // module specific sub pages of user profile

        case 'friends':

            $pos++ ;
            include( $pagedir."show_friends.php") ;
            die();

    } //switch
}


// still nothing?  Let's check for a plugin.

if( isset( $oe_plugin[$uri[$pos] ] ) ){

    include( $oe_module['profile']."conf/plugin.php" ) ;
    
    if( in_array($uri[$pos], $plugins[0] )){
            
        $pos++; 
        $tier++ ;
        
        include( $oe_plugin[$uri[$pos - 1]]."plugin.php" ) ;
        
        // does not die() to allow for bombing out to the main 404.
    }
}

// if we've reached here, then they get the 404 page for the profile section

include( $pagedir."404.php" ) ;
die() ;