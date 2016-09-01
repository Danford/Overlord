<?php
	   
include( oe_lib."page_minion.php" ) ;

$page= new page_minion( "Main Page" ) ;

$logged = $user->is_logged_in() ;

$page->header();

    // this page will eventually check to see if the user is logged in, then redirect them to their dashwall

?>


<?php  if( $logged ) {
    
    print( 'Hello '.$user->name.'!  <a href="/logout">Log Out</a>' ) ;
    
}
else {

    print( 'Welcome to our site! 
        <br /><br /> 
        <a href="/register/">Register</a> or
        <a href="/login/">Sign In</a>  or
        <a href="/login/passwordresetrequest">Reset Your Password</a>') ;
    
}


$page->footer(); ?>