<?php

include( oe_lib.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$page = new page_minion( 'Group - '.$group->name.' - Membership' );

$page->header() ;

$column = 1 ;
$count = 0 ;

?>

<div>

	<div style="inline:block ; vertical-align: top">

<?php 

foreach( $group->get_members( ( $page - 1 ) * ( members_columns * members_per_column ) ) as $member ){
    
    
    print( '<div>' ) ;
    
        print( '<div style="display: inline-block"><img src="'.create_image_link( 'profilethumb', $member['avatar'] ).'" style="padding-right: 15px"></div>' ) ;
        
        print( '<div style="display: inline-block; vertical-align: top"><a href="/profile/'.$member['user_id'].'">'.$member['screen_name'].'</a><br />') ;
        
        // location (once implemented), age, and gender will go here
        
        if( $group->is_owner() ){
            
            if( $group->is_moderator( $member['user_id'] ) ){
                $form = new form_minion('remove_moderator', 'group' ) ;
            } else {
                $form = new form_minion('make_moderator', 'group' ) ;
            }
            
            $form->header();
            $form->hidden( 'group_id', $group->id ) ;
            $form->hidden( 'member', $member['user_id'] ) ;
            
        
            if( $member['user_id'] == $group->owner_id ){
                print( 'OWNER' );
            } elseif( ! $group->is_moderator( $member['user_id'] ) ){
                print( '<a href="javascript: make_moderator.submit()">Make Moderator</a> ' );
            } elseif( ! ( $group->is_owner() and $member['user_id'] == $user->id ) ) {
                print( '<a href="javascript: remove_moderator.submit()">Remove Moderator</a> ' );
            }
            
            
            $form->footer() ;
        }
        
        if( $group->is_moderator() ){
            
            if( ! $group->is_moderator( $member['user_id'] ) and $member['user_id'] != $group->owner_id ) {
                
                $form = new form_minion( 'ban_member', 'group' ) ;
                
                $form->header() ;
                $form->hidden( 'group_id', $group->id ) ;
                $form->hidden( 'member', $member['user_id'] ) ;
                
    
                print( '<a href="javascript: ban_member.submit()">Ban Member</a> ' );
                
                $form->footer() ;
            }            
        }
    
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