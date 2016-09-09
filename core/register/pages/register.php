<?php
    // needs to be a check here for if they're already logged in. 

    include( oe_lib."form_minion.php" ) ;
    include( oe_frontend."page_minion.php" ) ;
    
    $page = new page_minion( "Register", '', '/js/register.start.js' ) ;
    
    $form = new form_minion( "start", "register" ) ;

    $bit_options["1"] = "Yes" ;
    $bit_options["0"] = "No" ;
    
    $option["0"] = "Not Disclosed" ;
    
    for( $i = 1 ; $i < count($gender) ; $i++ ) {
    	$option[ $i ] = $gender[$i]["label"] ;
    }
     
    $page->header() ;
    $form->header() ;
?>

	<div>
		Screen Name: 
		<?php $form->text_field("screen_name", "width: 250px"); $form->if_error("screen_name", "<br />%%ERROR%%"); ?>
	</div>
	<div>
		Note: Gender identification is completely optional.  Additionally, transgender individuals are
		not required to select a transgender option; if they so choose they may simply select their gender.
		Gender: 
		<?php $form->select("gender", $option); $form->if_error("gender", '<br />%%ERROR%%'); ?>
	</div>
	<div>
		Birthdate: <?php $form->date_input("birth", 1900, (date("Y") - 18)); $form->if_error("birth", '<br />%%ERROR%%'); ?>
	</div>
	<div>
		<p>E-mail: <?php $form->text_field("email", "width: 400px"); ?></p>
		<p>Confirm E-mail: <?php $form->text_field("confirmemail", "width: 400px"); $form->if_error("email", "<br />%%ERROR%%"); ?></
	</div>
	<div>
		<p>Passwords must be at least 8 characters long and contain at least one capital letter, one lowercase letter, and one number or special character.</p>
		<p>Password: <?php $form->pass_field("password", "width: 400px"); ?></p>
		<p>Confirm Password: <?php $form->pass_field("confirmpassword", "width: 400px"); $form->if_error("password", "<br />%%ERROR%%"); ?></p>
	</div>
	<div>
		<p>Zip: <?php $form->text_field( "zip", "width: 100px"); $form->if_error("zip", "<br />%%ERROR%%"); ?></p>
	    <p>City (Optional):</p>
	    <input type="hidden" name="city" id="city" value="">
	    <div id="city-input">
	    <input type="text" name="city-input" id="city-input" autocomplete="off" value="" style="width: 300px">
		    <div id="city-autocomplete-wrapper">
			    <div id="city-autocomplete">
			    
			    </div>
			</div>
	    </div>
	</div>
	<div>	
		<p>Show Age in Profile?</p> 
		<?php $form->select("show_age", $bit_options); ?>
	</div>
	<div>
		<p>Allow messages from users not in your friends list?</p> 
		<?php $form->select("allow_contact", $bit_options); ?>
	</div>
	<div>
		<p>Receive e-mail notification of new messages?</p> 
		<?php $form->select("email_notification", $bit_options ); ?>
		<p>Receive e-mail notification of new event invitations?</p> 
		<?php $form->select("invite_notification", $bit_options); ?>
	
		<p>Agree to TOS</p>
		<?php $form->checkbox( "tos_agree" ); $form->if_error("tos_agree", '<br />%%ERROR%%'); ?>
	</div>
	
	<?php $form->plain_button('Submit', 'x', '', 'onclick="validate()"'); ?>
	<?php $form->footer(); ?>	
	<?php $page->footer(); ?>
	