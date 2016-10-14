<?php

function get_my_groups(){
    
    global $db ;
    global $user ;
    
    $ownedgroups = array() ;

    $db->query( "SELECT `id`, `name`, `short`, `avatar` FROM `group` WHERE `owner`='".$user->id."'" ) ;
    
    while( ( $g = $db->assoc() ) != false ){
        $ownedgroups[] = $g ; 
    }
 
    $admingroups = array() ;
    $membergroups = array() ;
    
    $db->query("SELECT `id`, `name`, `short`, `avatar`, `access` 
                                FROM `group_membership`, `group` 
                                WHERE `group_membership`.`user`='".$user->id."'
                                AND `group_membership`.`group` = `group`.`id`
                                AND `access` != 0
                                ORDER BY ACCESS DESC") ;
    
    while( ( $g = $db->assoc() ) != false ){
        if( $g['access'] == 1 ) { 
            unset( $g['access'] ) ;
            $membergroups[] = $g ;
        } elseif( $g['access'] == 2 ) {
            unset( $g['access'] ) ;
            $admingroups[] = $g ;
        }
    }
        
    return ['owned' => $ownedgroups, 'admin' => $admingroups , 'member' => $membergroups ]  ;
}