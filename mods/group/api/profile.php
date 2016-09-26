<?php


$_POST['name'] = prevent_html( $_POST['name'] ) ;
$_POST['short'] = prevent_html( $_POST['short'] ) ;

$post->hold( 'name', 'short', 'detail','privacy','city' ) ;

$post->require_true( strlen( $_POST['name']) < 76, 'name', 'Group name cannot be longer than 75 characters.' ) ;
$post->require_true( strlen( $_POST['name']) > 4, 'name', 'Group name must be at least 5 characters.' ) ;

$post->require_true( strlen( $_POST['short']) < 256, 'name', 'Short Description cannot be longer than 255 characters.' ) ;

if( $apiCall == "edit "){
    
    $post->require_true( $_POST['privacy'] >= $group->privacy, 'privacy', 'You cannot lower the security of a group.' ) ;
}

$post->checkpoint() ;


if( $apiCall == "edit "){

    $db->update( "UPDATE `group` SET ".$db->build_set_string_from_post('name','short','detail','privacy','city')." WHERE `id`='".$group->id."'" ) ;
 
    $post->json_reply("SUCCESS") ;
    header( "Location:  ".str_replace("/edit", "", $_SERVER['HTTP_REFERER'])) ;
    die() ;
}

$i = $db->insert( "INSERT INTO `group` SET ".$db->build_set_string_from_post('name','short','detail','privacy','city') ) ;

$post->json_reply("SUCCESS", [ 'id' => $i ]) ;
header( "Location:  ".str_replace( "create", $i, $_SERVER['HTTP_REFERER'] ) ) ;
die() ;