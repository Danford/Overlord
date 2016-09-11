<?php

    // nothing should be changed below this line
   
    
    // $tier was declared or incremented in the plug's config
    // and here refers to the PLUG, not this plugin,
    // because that's where the settings we're looking for are.
    // if we must incremement it, we do so in our own plugin.conf.php


    // let's load the default settings....

    include( $oe_plugins['photo']."conf/conf.php") ;
    
    // no further information in the uri?  load all the photos attached to this plug item
    
    if( isset( $uri[$pos] ) or $uri[$pos] == "" ){
    
        include( $oe_plugins['photo']."pages/view_all.php" );
        die();
    }
    
    // is it the upload page?  Do they have access?
    
    if( $plug[0]['contributor'] and $uri[$pos] == 'upload' ){
               
        include( $oe_plugins['photo']."/pages/upload.php" ) ;
        die();
               
    }
    
    // is it a specific photo?
    
    
    if( verify_number($uri[$pos] ) ){


        $q = "SELECT `owner`,`privacy`, `title`, `description` FROM `".$plug[$tier]['photo']['view']."`
            WHERE `id`='".$uri[$pos]."'
            AND `module`='".$plug[0]['type']."'
            AND `module_item_id`='".$plug[0]['id']."'";
        
        if( $tier > 1 ){
            $q .= " AND `plug`='".$plug[$tier]['type']."'
            AND `plug_item_id`='".$plug[$tier]['id']."'";
        }
        
        $photo = $db->get_assoc($q) ;
        
        if( $q != false and ! $accesslevel < $photo['privacy'] ){
        
            // it's a specific photo
            
            if( $plug[0]['type'] == 'profile' ){
                // we already have the associated profile information, or should
                $photo["owner"] = $profile ;
            } else {
                $photo["owner"] = new profile_minion($photo["owner"], true );
            }

        
            $pos++ ;
            
            // do they want to see the photo?
            
            if( ! isset( $uri[$pos] ) or $uri[$pos] = "" ){
                
                include( $oe_plugins['photo']."/pages/view_one.php" );
                die();
            }
            
            // do they want to edit it?  is that even allowed?
            
            if( ( $photo->owner == $user->id or $plug[0]['admin'] == true ) and $uri[$pos] == "edit" ){
                
                include( $oe_plugins['photo']."/pages/edit.php" );
                die();
                
            }
        }
    }
    
    
    // are they trying to load the actual image file??
    

    if( in_array($uri[$pos], ['thumb','profile','profileThumb'] )){
        $imagetype = ".".$uri[$pos] ;
        $pos++ ;
    } else {
        $imagetype = "" ;
    }
    

    if( strpos($uri[$pos], '.png') != false ){
                    
            
        $split = explode('.', $uri[$pos]) ;
        
        if( ! verify_number( $split[0] ) ) { die() ; }
        
        $q = "SELECT `privacy`, `filekey` FROM `".$plug[$tier]['photo']['view']."` 
                WHERE `id`='".$split[0]."'
                AND `module`='".$plug[0]['type']."'
                AND `module_item_id`='".$plug[0]['id']."'";
        
        if( $tier > 1 ){
            $q .= " AND `plug`='".$plug[$tier]['type']."'
                AND `plug_item_id`='".$plug[$tier]['id']."'" ;
        }
        
        $photo = $db->get_assoc($q) ;
        
        // user's $accesslevel was set at the modular level
        
        if( $photo != false and ! $photo['privacy'] > $accesslevel ){

            $filename = $plug[$tier]['type'].".".$plug[$tier]['id'].".".$photo['filekey'].$imagetype.".png" ;

            if( file_exists( $plug[$tier]['photo']['path'].$filename ) ){
                
                header("Content-Type: image/png");
                header("Content-Length: " . filesize($plug[$tier]['photo']['path'].$filename));
                $file = @fopen( $plug[$tier]['photo']['path'].$filename, "rb" ) ;
                
                fpassthru( $file );
                
                fclose($file) ;
            }
        }
        
        die();
    }
    
    
    // the photo plugin really does just these five things.  
    // furthermore, its plugins-- comments and likes-- don't load on their own pages, but 
    // rather within the confines of the photo pages, so at this point we drop back and 404 
    // at the level of the previous module.
    
    
