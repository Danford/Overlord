<?php

include( oe_frontend."page_minion.php" ) ;

$page = new page_minion( "Registration" ) ;

$page->header() ;
?>

<p>An email confirmation has been sent to the email address you provided. Please click on the link provided to activate
your membership.  If you do not activate your membership within 48 hours, you may need to register again.</p>

<?php 

$page->footer() ;

?>