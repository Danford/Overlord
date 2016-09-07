<?php


include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$htmltitle = $profile->name." - Photo " ;

if( $photo['title'] != '' ){ $htmltitle .= '- '.$photo['title'] ; }

$page = new page_minion( $htmltitle ) ;


$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;
$page->header() ;

print( '<img src="'.create_image_link( 'userimage', $photo['photo_id']).'" /><br />' ) ;

print( $photo['title'].'<br />'.$photo['description'].'<br /><br />' ) ;

if( $photo['likes'] != 0 ){
    print( $photo['likes']." Likes" );
    print( '<br/><br/>' ) ;
}

if( $user->id == $profile->id ) {
    
    print( '<a href="/profile/editphoto/'.$photo['photo_id'].'">Edit Photo</a><br />' );
    
    $form = new form_minion('deletephoto', 'profile' ) ;
    $form->header() ;
    $form->hidden( "photo_id", $photo['photo_id'] ) ;
    
    print( '<a href="javascript:deletephoto.submit()">Delete Photo</a><br />' );
    
    
    $form->footer() ;
    
    
} else {
    
    // let's end the practice of liking your own stuff.  

    if( $photo['likes'] == 0 ){
     
        $form = new form_minion('like', 'profile' ) ;
        $button = "Like" ;
        
    } elseif( user_likes_item('photo', $photo['photo_id'] ) ) {
     
        $form = new form_minion('unlike', 'profile' ) ;
        $button = "Unlike" ;
        
    } else {
     
        $form = new form_minion('like', 'profile' ) ;
        $button = "Like" ;
    }
    
    $form->header() ;
    $form->hidden( 'type', 'photo') ;
    $form->hidden( 'id', $photo['photo_id']) ;
    $form->submit_button( $button ) ;
    $form->footer() ;
} 


if( $photo['comments'] != 0 ){
    
    print( '<h3>Comments</h3>' );
    
    foreach( $profile->get_comments( 'photo', $photo['photo_id'] ) as $comment ) {
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
          	        $form->hidden( 'type', 'photo' );
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
$form->hidden( 'type', 'photo' ) ;
$form->hidden( 'id', $photo['photo_id'] ) ;

print( '<h3>Add a comment</h3>' ) ;

print( '<div style="width: 600px">' ) ;

$form->textarea_field('comment' ) ;

print( '</div>' ) ;

$form->submit_button( 'Post Comment' ) ;
$form->footer() ;

$page->footer() ; ?>