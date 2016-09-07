<?php
include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page=new page_minion( $group->name." - ".$thread['subject'] ) ;

if( $allow_reply == true ){
    $page->addjs( '/js/tinymce/tinymce.min.js' ) ;
    $page->addjs( '/js/invoketinymce.js') ;
}

$page->header() ;

print('<h3>'.$group->name.'</h3>' ) ;
print('<h1>'.$thread['subject'].'</h1>') ;

page_nav( $page_number, $group, $thread, $allow_reply ) ;

foreach( $messages as $message ){
    
   
    
    ?>
    <div>
    	<div style="display:inline-block; vertical-align:top">
    		<img src="<?php print( image_link( 'profilethumb', $message['avatar'])) ; ?>" /><br />
    		<a href="/profile/<?php print( $message['user_id'] ) ; ?>"><?php print( $message['name']) ; ?></a><br />
    		<?php print( $message['timestamp']) ; ?>
    	</div>
    	<div style="display:inline-block; vertical-align:top">
    		<?php print( $message['message'] ) ; ?>
    	</div>
    </div>
    
    <?php 
    
    if( $group->is_moderator() ){

        print( '<div>' ) ;
        
        $form = new form_minion( 'deletemessage', 'group');
        $form->form_id = 'delete'.$message['message_id'] ;
        $form->header();
        $form->hidden( 'message_id', $message['message_id'] ) ;
        
        print( '<a href="javascript:'.$form->form_id.'">Delete Message</a>' ) ;
        
        $form->footer() ;
        
        print( '</div>' ) ; 
    }
}

page_nav( $page_number, $group, $thread, $allow_reply ) ;

if( $allow_reply == true ){

    
    $form = new form_minion( 'message', 'group' );
    $form->header();
    $form->hidden( 'group', $group->id ) ;
    $form->hidden( 'thread_id', $thread['thread_id'] ) ;
    ?><div style="width: 650px"><?php 
    $form->textarea_field('message');
    ?></div><?php 
    $form->submit_button("Submit") ;
    
    if( $group->is_moderator() ) {

        print( '<div>' );
        
            if( $thread['sticky'] == 0 ){
                $form = new form_minion('makesticky', 'group' ) ;   
            } else {
                $form = new form_minion('makeunsticky', 'group' ) ;
            }
        
            $form->form_css( 'display: inline-block' ) ;
            $form->header();
            $form->hidden( 'thread_id', $thread['thread_id'] ) ;
        
            if( $thread['sticky'] == 0 ){
                print( '<a href="javascript: makesticky.submit()">Make Sticky</a>') ;   
            } else {
                print( '<a href="javascript: makeunsticky.submit()">Make Unsticky</a>') ; 
            }
            
            $form->footer();
        
            $form = new form_minion('deletethread', 'group') ;
            $form->form_css( 'display: inline-block' ) ;        
            $form->header();
            $form->hidden( 'thread_id', $thread['thread_id'] ) ;

            print( '<a href="javascript: deletethread.submit()">Delete Thread</a>') ;
            
            $form->footer() ;
            
        print( '</div>' );
    
    }
}

$page->footer();

function page_nav( $page_number, $group, $thread, $allow_reply ){
        
    if( $page_number != 1 ){
    
        print( '<a href="/group/'.$group->id.'/thread/'.$thread['thread_id'].'">First Page</a> ') ;
    
    }
    
    if( $page_number != 2 and $page_number != 1 ){
    
        print( '<a href="/group/'.$group->id.'/thread/'.$thread['thread_id'].'/page/'.($page_number - 1).'">Previous Page</a> ' );
    }
    
    if( ! $allow_reply ){
    
        print( '<a href="/group/'.$group->id.'/thread/'.$thread['thread_id'].'/page/'.($page_number + 1).'">Next Page</a> ' );
    
    }
}