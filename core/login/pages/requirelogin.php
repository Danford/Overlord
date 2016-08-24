<?php
    
    include( oe_lib."page_minion.php" ) ;
    include( oe_lib."form_minion.php" ) ;
    
    $page = new page_minion( "Log In" ) ;
    
    $page->header() ;
    
    $form = new form_minion( 'login', 'login' ) ;

    $form->header() ;
    $form->hidden( 'logintype', 'restrictedpage' );
    
    ?>
    
    This page requires you to be logged in to access it.<br /><br />
    
	<?php $form->if_error( 'login', '%%ERROR%%<br /><br />' ) ; ?>

	Email Address: <br />
		
	<?php $form->text_field( 'email' , "width: 300px" )?>
	
	<br /><br />
	
	Password: <br />
	
	<?php $form->pass_field( 'password' )?>
	
	<br /><br />
	
	Keep Me Logged In 
	
	<?php $form->checkbox( "persist" )?>	

	<br /><br />
	
	<?php $form->submit_button() ?>

<?php 
    
    $form->footer() ;

    $page->footer() ;

?>
