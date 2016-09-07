<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( "Password Reset" ) ;

$page->header() ;

?>

If you entered an email address that corresponds to an account, then
an email has been sent to that address with instructions on how to reset your 
password.  For security reasons, we cannot confirm that the email address you
entered was correct.



<?php  $page->footer() ;
