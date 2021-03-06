<?php

if( $uri[$pos] == "./final" or $uri[$pos] == "page" ){
    
    if( $uri[$pos] == "page" ){
        $pos++ ;
        
        if( verify_number( $uri[$pos] ) ){
            $page = $uri[$pos] ;
        } else { $page = 1 ; }
        
    } else { $page = 1 ; }

    include( $oe_plugins['threads']."lib/thread.lib.php" );
    include( $oe_plugins['threads']."pages/list_threads.php" );
    die();
    
}

if( $uri[$pos] == 'create' ){
        
        include( $oe_plugins['threads']."pages/create.php" );
        die();
}


if( verify_number( $uri[$pos] ) ){
    
    $thread_id = $uri[$pos] ;
    
    $pos++ ;

    if( $uri[$pos] == "./final" or $uri[$pos] == "page" ){
    
        if( $uri[$pos] == "page" ){
            $pos++ ;
    
            if( verify_number( $uri[$pos] ) ){
                $page = $uri[$pos] ;
            } else { $page = 1 ; }
    
        } else { $page = 1 ; }

        $thread = $db->get_assoc( "SELECT `id`,`title`,`detail`,`sticky`,`locked`, `owner`, `edited`, 
                        ( SELECT COUNT(*) FROM `comment` 
                            WHERE `module`='".$oepc[$tier]['type']."' 
                              AND `module_item_id`='".$oepc[$tier]['id']."'
                              AND `plug`='thread'
                              AND `plug_item_id`= `thread`.`id` ) as `msgcount`
                    FROM `thread`
                    WHERE ".build_api_where_string()."
                      AND `id`='".$thread_id."'" ) ;
        
        if( $thread != false  and ! $user->is_blocked( $thread['owner'] ) ) { 
            
            $thread['owner'] = new profile_minion($thread['owner'], true ) ;
            include( $oe_plugins['threads']."pages/show_thread.php" );
            die();
        }
    } elseif( $uri[$pos] == 'edit' ) {
        
        $thread = $db->get_assoc( "SELECT `id`,`title`,`detail`,`sticky`,`locked`, `owner`
                    FROM `thread`
                    WHERE ".build_api_where_string()."
                      AND `id`='".$thread_id."'" );
        
        if( $thread != false ){ 
            include( $oe_plugins['threads']."pages/edit.php" );
            die();
        }
    }
}
