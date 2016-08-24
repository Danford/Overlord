<?php


include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$option = array() ;

$db->query( "SELECT `user_profile`.`user_id`, `user_profile`.`screen_name`
                                FROM `profile_friendship`, `user_profile`
                                WHERE 
                                  ( `profile_friendship`.`friend1` ='".$user->id."' AND
                                    `profile_friendship`.`friend2` =`user_profile`.`user_id` )
                                    OR
                                  ( `profile_friendship`.`friend2` ='".$user->id."' AND
                                    `profile_friendship`.`friend1` =`user_profile`.`user_id` )
                                ORDER BY `screen_name`" ) ;

while( ( $friend = $db->assoc() ) != false ){
    
    if( ! $event->is_invited( $friend['user_id'] ) ) {
    
        $option[ $friend['user_id'] ] = $friend['screen_name'] ;
    }
}

$page = new page_minion( 'Event - '.$event->name.' - Invite' );
$form = new form_minion( 'invite', 'event' );;

$form->header() ;
$form->hidden( 'event_id', $event->id ) ;
$form->select( 'invitees', $option, true ) ;
$form->submit_button( 'Invite' ) ;
$form->footer() ;

$page->footer() ;