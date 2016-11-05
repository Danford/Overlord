<?php

$privacylevel = 1 ;
$privacyoptions = [ 1 => 'Public', 2=> 'Closed', 3 => 'Secret' ] ;

if( $privacylevel > 0 ){ $user->require_login() ; }

$pagedir = $oe_modules['group']."pages/" ;


// USER ONLY SECTION

// things are specific to the logged in user.

switch( $uri[$pos ]){

    case './final':

        // no id specified-- show them a list of their groups
        
        include( $oe_modules['group']."lib/group.lib.php" ) ;
        $groups = get_my_groups() ;
    
        include( $pagedir."mygroups.php" ) ;
        die() ;

   case "create":
       
        include( $pagedir."create.php" ) ;
        die() ;
    
}

// anything else should specify the group number

if( verify_number( $uri[$pos] ) ){
        
    $group = new group_minion( $uri[$pos] ) ;
    
    if( $group->id != false ){
        
        // they at least have permission to see the group profile
        
        $pos++ ;
        
        if( $uri[$pos] == './final' ){
            
            include( $pagedir."profile.php" );
            die() ;
            
        }
        
        // anything other than the group profile requires membership
        
        if( $group->membership > 0 ){
            
            if( $uri[$pos] ==  'members' ){
                    
                    include( $pagedir."members.php" ) ;
                    die() ;
                
            }
            
            if( $user->id == $group->owner->id and $uri[$pos] == "edit" ){
                    
                    include( $pagedir."edit.php" ) ;
                    die() ;
                
            }
            
            // check for a plugin 
            
            if( isset( $oe_plugins[$uri[$pos] ] ) ){
            
                include( $oe_modules['group']."conf/plugin.conf.php" ) ;
                
                $accesslevel = 2 ; // all group content is members only
            
                if( in_array($uri[$pos], $oepc[0]['plugins'] )){
            
                    $pos++;
            
                    include( $oe_plugins[$uri[$pos - 1]]."plugin.php" ) ;
            
                    // does not die() to allow for bombing out to the main 404.
                }
            }
        }
    }
} 