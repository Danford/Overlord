<?php

security_report( "FAILED ACTIVATION" ) ;

include( oe_lib."page_minion.php" ) ;
$page = new page_minion( "Activation Failed" ) ;

$page->header() ;

?>

	Activation Failed.


<?php $page->footer() ;?>