<?php

if( $user->id == 0 ){ die('not logged in') ; } // THERE IS NO REASON THEY SHOULD BE HERE.

$baseurl = httproot.'profile/' ;
$pagedir = $oe_modules['profile']."post/" ;

switch( $_POST["oe_formid"] ) {

    case "imageupload":
    case "editphoto":
    case "deletephoto" :
        
        include( $pagedir.'photo.php' ) ;
        die() ;

    case 'editprofile':
    case 'editProfile':
        include( $pagedir.'profile.php' ) ;
        die() ;
        
    case 'addfriend' :
    case 'removefriend' :
    case 'confirmfriend' :
    case 'denyfriend' :
    case 'cancelfriendrq' :
    case 'blockuser' :
        
    case 'addFriend' :
    case 'removeFriend' :
    case 'confirmFriend' :
    case 'denyFriend' :
    case 'cancelFriendrq' :
    case 'blockUser' :
        
        include( $pagedir."friendmanagement.php") ;
        die();
        
    case 'addcomment' :
    case 'deletecomment' :
    case 'like' :
    case 'unlike':
        
    case 'addComment' :
    case 'deleteComment' :
    case 'unLike':
        
        include( $pagedir."comments_and_likes.php" ) ;
        die();

    case 'new_writing':
    case 'edit_writing':
    case 'delete_writing':

    case 'newWriting':
    case 'editWriting':
    case 'deleteWriting':
        
        include( $pagedir."writing.php" ) ;
        die() ;
}
$post->json_reply('FAIL') ;
die( 'FAIL' ) ;