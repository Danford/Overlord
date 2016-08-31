<?php


$post->hold( 'title', 'subtitle', 
                'start_year', 'start_month', 'start_day', 
                'start_hour', 'start_minute', 'start1_meridian',
                'end_year', 'end_month', 'end_day', 
                'end_hour', 'end_minute', 'end_meridian',
                'address', 'cost', 'detail', 'dress' ) ;

if( isset( $_POST['group'] ) ){
    
    $group = new group_minion($_POST['group'] ) ;
    
    if( ! $group->is_moderator( $_POST['group'] ) ){ 
        $post->json_reply('FAIL') ;
        die() ;
    }
    
} else {
    
    $post->hold('type');

    if( ! is_numeric( $_POST['type'] ) or ($_POST['type'] < 1) or ($_POST['type'] > 3) ) {
        $post->json_reply('FAIL') ;
        die('12') ;
    }
    
}

if( $_POST['oe_formid'] == 'edit' ) {

    // verify user actually has access to edit the event
    
    $before = $db->get_assoc( "SELECT `organiser`, `type` FROM `event_profile` WHERE `event_id`='".$db->sanitize( $_POST['event_id'] )."'" );

     
    if( ! isset( $_POST['group'] ) ){
        
        if( $before['organiser'] != $user->id ){ $post->json_reply( 'FAIL' ) ; die( $before['organiser']."-".$user->id ) ; }
                
        $post->require_true( $before['type'] < $_POST['type'], 'type', 'You cannot make an event less secure.' ) ;
        $post->checkpoint() ;
        
    } else {
        
        include( $oe_modules['group']."/lib/group_minion.php" ) ;
        $group = new group_minion( $db->sanitize( $_POST['group'] ) ) ;
        
        if( ! $group->is_moderator() ){ $post->json_reply('FAIL') ; die() ; }         
        
    }
}

$post->require_field( 'title', 'Title cannot be blank.' ) ;

$startstring = $post->date_combine( 'start' )." ".$post->time_combine('start') ;
$endstring = $post->date_combine( 'end' )." ".$post->time_combine('end') ;

if( preg_match( '/99/', $endstring ) == 0 ){
    $post->require_true( $endstring > $startstring, 'start', "Start Time must come before End Time." ) ;
} else {
    $endstring = '' ;
}

$post->require_true( $startstring  > oe_time(), 'start', "You cannot schedule an event for the past." ) ;


$toofar = ( time() + ( ( 365 * event_year_range ) + 1 ) * 24 * 60 * 60 ) ;

$post->require_true( strtotime( $startstring ) < $toofar, 'start', "You cannot schedule more than ".event_year_range." years in the future." ) ;

$post->checkpoint() ;

$set = $db->build_set_string_from_post( 'title', 'subtitle', 'address', 'cost', 'detail', 'dress' ) ;

$set .= ', '.$db->build_set_string_from_array( array( 'start' => $startstring, 'end' => $endstring ) ) ;

if( isset( $_POST['group'] ) ){
    
    $set .=", `type`='0', `group`='".$group->id."'"  ; 
}
else {
    
    $set .= ", `type`='".$_POST['type']."', `organiser`='".$user->id."'" ;
    
}

if( $_POST['oe_formid'] == 'edit' ) {
    
    $post->require_true( $before['type'] <= $_POST['type'] , 'type', 'You cannot lower the security of an already existing event.' ) ;
    $post->checkpoint() ;
   
    $db->update( "UPDATE `event_profile` SET ".$set." WHERE `event_id`='".$db->sanitize( $_POST['event_id'] )."'" ) ;
    
    $post->json_reply('SUCCESS') ;
    
    header( "Location: /event/".$_POST['event_id'] ) ;
    die() ;
}

$event = $db->insert( 'INSERT INTO `event_profile` SET '.$set ) ;

if( isset( $_POST['group'] ) ){

    $post->json_reply('SUCCESS', $event ) ;
    
    header( "Location: /group/".$group->id."/event/".$event ) ;

    // send invitations!!!
    
} else {

    $post->json_reply('SUCCESS', $event ) ; 
    header( "Location: /event/".$event ) ;
}
    
die() ;

