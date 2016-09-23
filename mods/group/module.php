<?php

$privacylevel = 1 ;
$privacyoptions = [ 2 => 'Members Only' ] ;

if( $privacylevel > 0 ){ $user->require_login() ; }

$pagedir = $oe_modules['group']."pages/" ;


// USER ONLY SECTION

// things are specific to the logged in user.

if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ) {

    // no id specified-- show them a list of their groups
    
    include( $oe_modules['group']."lib/group.lib.php" ) ;
    $groups = get_my_groups() ;

    include( $pagedir."mygroups.php" ) ;
    die() ;
}

if( $uri[$pos] == "create" ) {

        include( $pagedir."create.php" ) ;
        die() ;
    
}

// anything else should specify the group number

if( verify_number( $uri[$pos] ) ){
    
    include_once $oe_modules['group']."lib/group_minion.php" ;
    
    $profile = new group_minion( $uri[$pos] ) ;
    
    if( $profile['id'] != false ){
        
        // they at least have permission to see the group profile
        
        $pos++ ;
        
        if( ! isset( $uri[$pos] ) or $uri[$pos] == "" ){
            
            include( $pagedir."profile.php" );
            die() ;
            
        }
        
        // anything other than the group profile requires membership
        
        if( $group->membership > 0 ){
            
            if( $uri[$pos] ==  'members' ){
                    
                    include( $pagedir."members.php" ) ;
                    die() ;
                
            }
            
            if( $user->id = $group->owner and $uri[$pos] == "edit" ){
                    
                    include( $pagedir."edit.php" ) ;
                    die() ;
                
            }
            
            // check for a plugin 
            
            if( isset( $oe_plugin[$uri[$pos] ] ) ){
            
                include( $oe_module['group']."conf/plugin.conf.php" ) ;
            
                if( in_array($uri[$pos], $plugins[0] )){
            
                    $pos++;
            
                    include( $oe_plugin[$uri[$pos - 1]]."plugin.php" ) ;
            
                    // does not die() to allow for bombing out to the main 404.
                }
            }
        }
    }
}