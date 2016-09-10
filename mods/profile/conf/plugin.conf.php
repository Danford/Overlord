<?php

$parent[0] = [ 'type' => 'profile', 'id' => $profile->id ] ;
$plugins[0] = [ 'photo', 'writing', `albums`] ;

$pluginconf[0]['contributor'] = ( $profile->id == $user->id ) ;
$pluginconf[0]['admin'] = ( $profile->id == $user->id ) ;

        // only the user can upload/edit content via plugins on the profile

$pluginconf[0]['photo']['use_albums'] = true ;
$pluginconf[0]['photo']['view'] = 'photo_profile' ;
$pluginconf[0]['photo']['folder'] = ul_img_dir ;

$pluginconf[0]['writing']['use_albums'] = true ;
$pluginconf[0]['writing']['view'] = 'writing_profile' ;

$pluginconf[0]['album']['view'] = 'album_profile' ;