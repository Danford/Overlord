<?php

include( oe_lib.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$option = array() ;

$db->query( "SELECT `user_profile`.`user_id`, `user_profile`.`screen_name`
                                FROM `group_invite`, `user_profile`
                                WHERE
                                  `group_id` = '".$group->id."'
                                    AND
                                  `type` = '2'
                                    AND
                                  `group_invite`.`user_id` = `user_profile`.`user_id`
                                ORDER BY `screen_name`" ) ;

while( ( $friend = $db->assoc() ) != false ){
    $option[ $friend['user_id'] ] = $friend['screen_name'] ;
}

$page = new page_minion( 'Group - '.$group->name.' - Request Moderation' );
$form = new form_minion( 'approve_request', 'group' );

$page->header() ;

$form->header() ;
$form->hidden( 'group_id', $group->id ) ;
$form->select( 'invitees', $option, true ) ;
$form->submit_button( 'Invite' ) ;

$form->footer() ;
$page->footer() ;