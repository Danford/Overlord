<?php

$id = $uri[$pos + 1 ] ;
$key = $uri[$pos + 2 ] ;

$idregexp = '/^[0-9]*$/' ;
$keyregexp = '/^[0-9a-f]{64}$/' ;

if( preg_match( $idregexp, $id ) == 0 or preg_match( $keyregexp, $key ) == 0 ) {

   // key and id are unsafe
    
    if( preg_match( $keyregexp, $key ) == 0 ){ die( "keymatch fail ".strlen( $key ) ) ; } 
    
    include( $pagedir."passwordresetfail.php" );
    die() ;

} 


// verify that it's a valid key & id

$q = "SELECT COUNT(*) FROM `confirmation_key` WHERE `profile`='".$id."' and `confirmation_key`='".$key."' and `type`='1'" ;

if( $db->get_field( $q ) == 0 ){

    include( $pagedir."passwordresetfail.php" ) ;
    die() ;

}

// at this point, we have an valid id/key combo

$_SESSION['reset']['id'] = $id ;
$_SESSION['reset']['key'] = $key ;

include( oe_frontend."page_minion.php" ) ;
include( oe_lib."form_minion.php" ) ;

$page = new page_minion( "Password Reset" ) ;

$page->header() ;

$form = new form_minion( 'passwordreset', 'login' ) ;

$form->header() ;

?>

Enter your new password to continue.<br /><br />

<?php 

$form->if_error( "password", '%%ERROR%%<br /><br />' ) ; 

$form->pass_field("password", "width:400px" ) ;

?> Confirm Password<?php 

$form->pass_field("confirmpassword", "width:400px" ) ;

?><br/><?php 

$form->submit_button( "Submit" ) ;

$form->footer() ;

$page->footer() ;    