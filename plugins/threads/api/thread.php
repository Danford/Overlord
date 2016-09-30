<?php

if( $oepc[0]['admin'] ){

    $post->hold( 'sticky', 'locked' ) ;
    $o['sticky'] = $_POST['sticky'] ;
    $o['locked'] = $_POST['locked'] ;
}

$post->hold( 'title', 'detail' );

$post->require_true( strlen( $_POST['title'] ) < 76 , 'title', 'Title cannot be longer than 75 characters.' ) ;
$post->require_true( strlen( $_POST['detail'] ) > 0 , 'detail', 'Text cannot be empty.' ) ;

$post->checkpoint() ;

$o['edited'] = oe_time() ;
$o['title'] = prevent_html($_POST['title'] );
$o['detail'] = process_user_supplied_html( $_POST['detail'] ) ;

if( $apiCall == 'edit' ){
    

    $db->update( "UPDATE `".$oepc[$tier]['thread']['table']."` 
                        SET ".$db->build_set_string_from_array($o)." 
                      WHERE ".build_api_where_string()." 
                        AND `thread_id`='".$_POST['thread_id']."'" ) ;
    
    $post->json_reply("SUCCESS");
    header( 'Location: '.str_replace('/edit', '', $_SERVER['HTTP_REFERER']) ) ;
    die() ;
    
} else {
    
    $o['owner'] = $user->id ;
    $o['created'] = oe_time();
    
    $id = $db->insert( "INSERT INTO `".$oepc[$tier]['thread']['table']."` SET ".build_api_set_string().",".$db->build_set_string_from_array($o) ) ;
    
    $post->json_reply("SUCCESS", ['id' => $id ] ) ;
    header( 'Location: '.str_replace('create', $id, $_SERVER['HTTP_REFERER']) ) ;
    die() ;
    
    
}