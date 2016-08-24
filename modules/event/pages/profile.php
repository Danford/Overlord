<?php


include( oe_lib.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$page = new page_minion( 'Event - '.$event->title );
$page->header() ;


if( $event->group != null and $event->group->is_moderator() ){
    print( '<a href="/group/'.$event->group.'/event/'.$event->id.'/edit">Edit Event</a><br/><br/>') ;    
} elseif( $user->id == $event->organiser ) {
    print( '<a href="/event/'.$event->id.'/edit">Edit Event</a><br/><br/>') ;
}


print( $event->title.'<br/><br />' );
print( $event->subtitle.'<br/><br />' );

if( $event->group != null ) {
    print( 'Hosted by <a href="/group/'.$group->id.'">'.$group->name.'</a><br /><br />' );
} else {
    print( 'Hosted by <a href="/user/'.$event->organiser.'">'.$db->get_field( "SELECT `name` from `user_profile` WHERE `user_id`='".$event->organiser."'" ).'</a><br /><br />' );
}
    
print( 'Starts '.friendly_time( $event->start ).'<br />' ) ;

if( $event->end != '' ){

    print( 'Ends '.friendly_time( $event->end ).'<br /><br />' ) ;
} else {
    print('<br/>') ;
}

print( 'Address: '.$event->address.'<br/><br/>' ) ;

print( 'Cost: '.$event->cost.'<br/><br/>' ) ;

print( 'Dress Code: '.$event->dress.'<br/><br/>' ) ;

print( $event->detail ) ;

$page->footer() ;
?>

