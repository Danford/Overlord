<?php
    // needs to be a check here for if they're already logged in. 

    include( oe_lib."form_minion.php" ) ;
    include( oe_frontend."page_minion.php" ) ;
    
    $page = new page_minion( "Register", '', '/js/register.start.js' ) ;
    
    $form = new form_minion( "start", "register" ) ;
    
    $page->header() ;
    $form->header() ;
?>

	Screen Name: 
	<br />

<?php 
    
    $form->text_field( "screen_name", "width: 250px" ) ;
    $form->if_error("screen_name", "<br />%%ERROR%%") ;
?>
	<br /><br />
	
	Note: Gender identification is completely optional.  Additionally, transgender individuals are
	not required to select a transgender option; if they so choose they may simply select their gender.
	
	<br /><br />
	Birthdate: <?php $form->date_input("birth", 1900, (date("Y") - 18)); $form->if_error("birth", '<br />%%ERROR%%'); ?>
	
	Gender: 
<?php
    $option["0"] = "Not Disclosed" ;
    
    for( $i = 1 ; $i < count($gender) ; $i++ ) {
        $option[ $i ] = $gender[$i]["label"] ;
    }
    
    $form->select("gender", $option ) ;
    $form->if_error( "gender", '<br />%%ERROR%%' ) ;
?>

	<br /><br />

	E-mail: <?php $form->text_field("email", "width: 400px"); ?>

	<br />
	Confirm E-mail: <?php $form->text_field("confirmemail", "width: 400px"); $form->if_error("email", "<br />%%ERROR%%"); ?>
	<br /><br />	
	
	Passwords must be at least 8 characters long and contain at least one capital letter, one lowercase letter, and one number or special character.
	
	<br /><br />	
	
	Password: <?php $form->pass_field( "password", "width: 400px" ) ; ?>

	<br />
	Confirm Password: <?php $form->pass_field("confirmpassword", "width: 400px"); $form->if_error("password", "<br />%%ERROR%%"); ?>

	<br /><br />
	
	Zip: <?php $form->text_field( "zip", "width: 100px"); $form->if_error("zip", "<br />%%ERROR%%"); ?>
    City (Optional): <?php $form->text_field( "city", "width: 300px"); $form->if_error("city", "<br />%%ERROR%%"); ?>
    
	<br /><br />
	
	Show Age in Profile? 
	
<?php 

    $bit_options["1"] = "Yes" ;
    $bit_options["0"] = "No" ;
    
    $form->select( "show_age" , $bit_options ) ;  ?>

	<br /><br />
	
	Allow messages from users not in your friends list? 
	
<?php $form->select( "allow_contact", $bit_options ) ;  ?>

	<br /><br />
	
	Receive e-mail notification of new messages? 
	
<?php $form->select( "email_notification", $bit_options ) ;  ?>

	<br /><br />
	
	Receive e-mail notification of new event invitations? 
	
<?php $form->select( "invite_notification", $bit_options ) ;  ?>

	<br /><br />
		
	Agree to TOS 
	
<?php $form->checkbox( "tos_agree" ) ;
    
    $form->if_error( "tos_agree", '<br />%%ERROR%%' ) ; ?>

	<br /><br />
	
<?php 

$form->plain_button( 'Submit', 'x', '', 'onclick="validate()"') ; 

$form->footer() ;

$page->footer() ;

?>
	