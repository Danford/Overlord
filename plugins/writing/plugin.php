<?php

include( $oe_plugins['writing']."conf/conf.php") ;
    
// no further information in the uri?  load all the writing attached to this plug item

if( isset( $uri[$pos] ) or $uri[$pos] == "" or $uri[$pos] == "page" ){
    
    if( $uri[$pos] == "page" ){
        $pos++ ;
        
        if( verify_number( $uri[$pos] ) ){
            $page = $uri[$pos] ;
        } 
        
    } else { $page = 1 ; }
    
    if( isset( $page ) ){
    
        include( $oe_plugins['writing']."pages/view_all.php" );
        die();
        
    } // else nothing-- let them go ahead and fail out to 404
}

// is it the upload page?  Do they have access?

if( $oepc[0]['contributor'] and $uri[$pos] == 'write' ){
    include( $oe_plugins['writing']."/pages/writing.php" ) ;
    die();
}

// is it a specific piece of writing?

if( verify_number($uri[$pos] ) ){


    $q = "SELECT `id`, `owner`, `privacy`, `title`, `subtitle`, `copy`, `timestamp` FROM `".$oepc[$tier]['writing']['view']."`
        WHERE `id`='".$uri[$pos]."'
        AND `module`='".$oepc[0]['type']."'
        AND `module_item_id`='".$oepc[0]['id']."'";

    if( $tier > 1 ){
        $q .= " AND `plug`='".$oepc[$tier]['type']."'
        AND `plug_item_id`='".$oepc[$tier]['id']."'";
    }

    $writing = $db->get_assoc($q) ;

    if( $q != false and ! $accesslevel < $writing['privacy'] ){

        // it's a specific piece of writing

        if( $oepc[0]['type'] == 'profile' ){
            // we already have the associated profile information, or should
            $writing["owner"] = $profile ;
        } else {
            $writing["owner"] = new profile_minion($writing["owner"], true );
        }


        $pos++ ;

        // do they want to read the writing?

        if( ! isset( $uri[$pos] ) or $uri[$pos] = "" or $uri[$pos] == 'page' ){    
            
            if( $uri[$pos] == "page" ){
            
                $pos++ ;
                
                if( verify_number( $uri[$pos] ) ){
                    $page = $uri[$pos] ;
                } 
                
            } else { $page = 1 ; }

            include( $oe_plugins['writing']."/pages/view_one.php" );
            die();
        }

        // do they want to edit it?  is that even allowed?

        if( ( $writing['owner']->id  == $user->id or $oepc[0]['admin'] == true ) and $uri[$pos] == "edit" ){

            include( $oe_plugins['writing']."/pages/edit.php" );
            die();

        }
    }
}