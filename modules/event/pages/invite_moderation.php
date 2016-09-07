<?php

include( oe_frontend.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$option = array() ;

$db->query( "SELECT `user_profile`.`user_id`, `user_profile`.`screen_name`, `invited_by`
                                FROM `event_invite`, `user_profile`
                                WHERE
                                  `event_id` = '".$event->id."'
                                    AND
                                  ( `type` = '1' OR `type` = '2' )
                                    AND
                                  `group_invite`.`user_id` = `user_profile`.`user_id`
                                ORDER BY `screen_name`" ) ;

while( ( $friend = $db->assoc() ) != false ){
    $option[ $friend['user_id'] ] = $friend['screen_name']." (invited by " ;
    $option[ $friend['user_id'] ] .= $db->get_field( "SELECT `screen_name` FROM `user_profile` WHERE `user_id`='".$friend['invited_by']."'" ).')' ;
}

$page = new page_minion( 'Event - '.$event->name.' - Invite Moderation' );
$form = new form_minion( 'approve_invitation', 'event' );

$page->header() ;

$form->header() ;
$form->hidden( 'event_id', $event->id ) ;
$form->select( 'invitees', $option, true ) ;
$form->submit_button( 'Invite' ) ;
$form->footer() ;
$page->footer() ;