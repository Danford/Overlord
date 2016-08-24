<?php

include( oe_lib."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion( "Password Reset" ) ;

$page->header() ;

$form = new form_minion( 'passwordresetrequest', 'login' ) ;

$form->header() ;

?>

Lost your password?  No problem.  Just punch in your email and we'll send you instructions
on how to reset it. <br /><br />

<?php 

$form->if_error( "email", '%%ERROR%%<br /><br />' ) ; 

$form->text_field("email", "width:400px" ) ;

$form->submit_button( "Submit" ) ;

$form->footer() ;

$page->footer() ;
