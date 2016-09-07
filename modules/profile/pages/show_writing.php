<?php

include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion( $profile->name." - Writing - ".$writing['title'] ) ;


$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->header() ;


?>

<h1><?php  print( $writing['title']); ?></h1>

<?php 

    if( $writing['subtitle'] != '' ){
        print( '<h2>'.$writing['subtitle'].'</h2>'.PHP_EOL ) ;
    }

    print( $writing['content'] ) ;
?><br /><br/><?php 

if( $user->id == $profile->id ) {
    
    print( '<a href="/profile/editwriting/'.$writing['prose_id'].'">Edit Writing</a><br />' );
    
    $form = new form_minion('delete_writing', 'profile' ) ;
    $form->header() ;
    $form->hidden( "prose_id", $writing['prose_id'] ) ;
    
    print( '<a href="javascript:delete_writing.submit()">Delete Writing</a><br />' );
    
    
    $form->footer() ;
    
    
} else {
    
    // let's end the practice of liking your own stuff.  

    if( $writing['likes'] == 0 ){
     
        $form = new form_minion('like', 'profile' ) ;
        $button = "Like" ;
        
    } elseif( user_likes_item('prose', $writing['prose_id'] ) ) {
     
        $form = new form_minion('unlike', 'profile' ) ;
        $button = "Unlike" ;
        
    } else {
     
        $form = new form_minion('like', 'profile' ) ;
        $button = "Like" ;
    }
    
    $form->header() ;
    $form->hidden( 'type', 'prose') ;
    $form->hidden( 'id', $writing['prose_id'] ) ;
    $form->submit_button( $button ) ;
    $form->footer() ;
} 


if( $writing['comments'] != 0 ){

    print( '<h3>Comments</h3>' );

    foreach( $profile->get_comments( 'prose', $writing['prose_id'] ) as $comment ) {
        // `comment_id`, `user`, `comment`, `timestamp`

        ?><div>
        	<a name="<?php print( $comment['comment_id'] ); ?>" />
          <div style="display: inline-block">
              <a href="/profile/<?php print( $comment['user']-> id ) ; ?>"><img src="<?php print($comment['user']->profile_thumbnail()) ; ?>"></a>
              <br />
              <a href="/profile/<?php print( $comment['user']-> id ) ; ?>"><?php print( $comment['user']->name )?></a>
          </div>
          
          <div style="display: inline-block">
          
          	
          	<?php 
          	    print( $comment['timestamp'].'<br/>' ); 
          	    
          	    if( $profile->id == $user->id or $comment['user']->id == $user->id  ){
          	        
          	        $form = new form_minion('deletecomment', 'profile' ) ;
          	        $form->form_id = 'delete'.$comment['comment_id'] ;
          	        
          	        $form->header() ;
          	        $form->hidden( 'type', 'prose' );
          	        $form->hidden( 'comment_id', $comment['comment_id'] ) ;
          	        
          	        ?><a href="javascript: delete<?php  print( $comment['comment_id'])?>.submit()">Delete Comment</a><?php 
          	        
          	        $form->footer() ;
          	        
                } ?>
          	        <?php  print( $comment['comment']) ;?> 
          </div> 
          </div> <?php
       
    }
} 

$form = new form_minion('addcomment', 'profile') ;
$form->header() ;
$form->hidden( 'type', 'prose' ) ;
$form->hidden( 'id', $writing['prose_id'] ) ;

print( '<h3>Add a comment</h3>' ) ;

print( '<div style="width: 600px">' ) ;

$form->textarea_field('comment' ) ;

print( '</div>' ) ;

$form->submit_button( 'Post Comment' ) ;
$form->footer() ;


$page->footer() ; ?>