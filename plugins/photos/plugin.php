<?php

    // nothing should be changed below this line
    
    $tier = count( $parent );  // how deep is the rabbit hole?
   
    if( isset( $pluginconf[ $tier - 1 ]['photo']['useAlbums'] ) ){
        $useAlbums = $pluginconf[ $tier - 1 ]['photo']['useAlbums'] ;
    } else {
        $useAlbums = true ;
    }


    if( isset( $pluginconf[$tier]['photo']['view'] )){
        $queryfrom = $pluginconf[$tier]['photo']['view'] ;
    } else {
        $queryfrom = 'photo' ;
    }
    
    if( isset( $uri[$pos] ) or $uri[$pos] == "" ){
    
        include( $oe_plugins['photo']."pages/view_all.php" );
        die();
    }
    
    if( $pluginconf[0]['contributor'] and $uri[$pos] == 'upload' ){
               
        include( $oe_plugins['photo']."/pages/upload.php" ) ;
        die();
               
    }
    
    // photo display and edit pages
    
    
    if( verify_number($uri[$pos] ) ){


        $q = "SELECT `owner`,`privacy`, `title`, `description` FROM `".$queryfrom."`
            WHERE `id`='".$uri[$pos]."'
            AND `module`='".$parent[0]['type']."'
            AND `module_item_id`='".$parent[0]['id']."'";
        
        if( $tier > 1 ){
            $q .= " AND `plug`='".$parent[$tier - 1]['type']."'
            AND `plug_item_id`='".$parent[$tier - 1]['id']."'";
        }
        
        $photo = $db->get_assoc($q) ;
        
        if( $q != false and $accesslevel <= $photo['privacy'] ){
        
            // we have verified that's it's a photo id that they can at least see
            
            if( $parent[0]['type'] == 'profile' ){
                // we already have the associated profile information, or should
                $photo["owner"] = $profile ;
            } else {
                $photo["owner"] = new profile_minion($photo["owner"], true );
            }

        
            $pos++ ;
            
            if( ! isset( $uri[$pos] ) or $uri[$pos] = "" ){
                
                include( $oe_plugins['photo']."/pages/view_one.php" );
                die();
            }
            
            if( $photo->owner == $user->id and $uri[$pos] == "edit" ){
                
                include( $oe_plugins['photo']."/pages/edit.php" );
                die();
                
            }
        }
    }
    
    
    // are they trying to load the actual image file??
    

    if( in_array($uri[$pos], ['thumb','profile','tiny'] )){
        $imagetype = ".".$uri[$pos] ;
        $pos++ ;
    } else {
        $imagetype = "" ;
    }
    

    if( strpos($uri[$pos], '.png') != false ){
        
        if( isset( $pluginconf[$tier]['photo']['folder'] )){
            $imageDir = $pluginconf[$tier]['photo']['folder'] ;
        } else {
            $imageDir = ul_img_dir ;
        }
            // the above allows for creation of views for each kind of photo
            
            // $accesslevel is set at the modular level
            
        $split = explode('.', $uri[$pos]) ;
        
        $q = "SELECT `privacy`, `filekey` FROM `".$queryfrom."` 
                WHERE `id`='".$split[0]."'
                AND `module`='".$parent[0]['type']."'
                AND `module_item_id`='".$parent[0]['id']."'";
        
        if( $tier > 1 ){
            $q .= " AND `plug`='".$parent[$tier - 1]['type']."'
                AND `plug_item_id`='".$parent[$tier - 1]['id']."'";
        }
        
        $photo = $db->get_assoc($q) ;
        
        if( $photo != false and $photo['privacy'] <= $accesslevel ){

            $filename = $parent[0]['type'].".".$parent[0].['id'].".".$photo['filekey'].$image['type'].".png" ;

            if( file_exists($imageDir.$filename ) ){
                
                header("Content-Type: image/png");
                header("Content-Length: " . filesize(ul_img_dir.$_SESSION["imagekey"][$uri[$pos]]));
                $file = @fopen( $imageDir.$filename, "rb" ) ;
                
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
    
    
