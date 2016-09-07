<?php

include( oe_frontend.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$page = new page_minion( 'Group - '.$group->name.' - Banned Members' );

$page->header() ;

$column = 1 ;
$count = 0 ;

?>

Note:  Once you unban a member they will need to rejoin the group.

<div>

	<div style="inline:block ; vertical-align: top">

<?php 

foreach( $group->get_banned( ( $page - 1 ) * ( members_columns * members_per_column ) ) as $banned ){
    
    
    print( '<div>' ) ;
    
        print( '<div style="display: inline-block"><img src="'.create_image_link( 'profilethumb', $banned['avatar'] ).'" style="padding-right: 15px"></div>' ) ;
        
        print( '<div style="display: inline-block; vertical-align: top"><a href="/profile/'.$banned['user_id'].'">'.$banned['screen_name'].'</a><br />') ;
        
        
        print( 'Banned by: '.$banned['blocked_by'].'<br/>') ;
        // location (once implemented), age, and gender will go here
        
        $form = new form_minion('unban_member', 'group' ) ;
        
        $form->header();
        $form->hidden( 'group_id', $group->id );
        $form->hidden( 'member', $banned['user_id'] );
        
        print( '<a href="javascript: unban_member.submit()">Unban Member</a>' );
        
        $form->footer() ;
    
        $count++ ;
        
        print( '</div></div>' ) ;
        
        if( $count == members_per_column ){
            $count = 0 ;
            
            print( '</div>
            <div style="inline:block ; vertical-align: top">') ;
        }
    
    
    


    
    
}

?>

	</div>
<div>

<?php 
$page->footer() ;