<?php

include( oe_frontend.'page_minion.php' ) ;
include( oe_lib.'form_minion.php' ) ;

$page = new page_minion( 'Group - '.$group->name );
$page->header() ;
print( $group->name ); ?>

<br /><br />

<?php print( prevent_html( $group->short_desc ))?>

<br /><br />

<?php print( process_user_supplied_html( $group->detail )); 

?>

<br /><br />

<?php 

if( $group->access == 'full' ){
        
        ?>
        <a href="/group/<?php print( $group->id ); ?>/notifications">Edit Group Notification Options</a> <br /><br >
        <?
    
    if( $group->is_owner() ){
        
        ?>
        <a href="/group/<?php print( $group->id ); ?>/edit">Edit Group Profile</a> <br /><br >
        <?
        
        // owner can't leave group-- has to transfer ownership or delete group
        // also need a moderation link here.
        
    } else {
        
        // also need a moderation link here (for moderators, obvs).
    
        $form = new form_minion('leave', 'group') ;
        $form->header();
        $form->hidden('group_id', $group->id ) ;
        $form->submit_button( 'Leave Group' ) ;
        $form->footer() ;
    }
    
} else {
    
    switch( $group->type ){
        
        case 1: // public group

            $form = new form_minion('join', 'group') ;
            $form->header();
            $form->hidden('group_id', $group->id ) ;
            $form->submit_button( 'Join Group' ) ;
            $form->footer() ;

        break ;
        
        case 2: // closed group
        case 3: // secret group
            
            if( $group->is_invited() ){
            
                $form = new form_minion('join', 'group') ;
                $form->header();
                $form->hidden('group_id', $group->id ) ;
                $form->submit_button( 'Join Group' ) ;
                $form->footer() ;
                
                
            } elseif( $group->type == 2 ) { // you can't apply to a secret group
            
                $check = $db->get_field( "SELECT COUNT(*) FROM `group_invite` WHERE `group_id`='".$group->id."'
                    AND `user_id`='".$user->id."' AND `type`='2'" ) != 0 ;
                
                if( $check ){
                    print( "Join request pending" ) ;
                } else { 
                    $form = new form_minion('request', 'group') ;
                    $form->header();
                    $form->hidden('group_id', $group->id ) ;
                    $form->submit_button( 'Request to Join Group' ) ;
                    $form->footer() ;                
                }
            }
            
        break ;
    }
    
}


$page->footer();
?>



