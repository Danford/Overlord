<?php

include( oe_frontend.'form_minion.php' ) ;
include( oe_lib."page_minion.php" ) ;

$page = new page_minion("Edit Your Profile");
$form = new form_minion('editprofile', 'profile' ) ;

$page->addjs( '/js/tinymce/tinymce.min.js' ) ;
$page->addjs( '/js/invoketinymce.js') ;

$page->header() ;
$form->header() ;

$form->fill_from_db( $db, "SELECT `gender`,`birthdate`,`detail`, `allow_contact`, `show_age`,`email_notification`,`invite_notification`
                            FROM `user_profile`, `user_account` WHERE `user_profile`.`user_id`='".$user->id."' 
                                AND `user_profile`.`user_id` = `user_account`.`user_id`" ) ;

$form->fill_field('birth_day', substr( $form->data['birthdate'], 8, 2 ) ) ;
$form->fill_field('birth_month', substr( $form->data['birthdate'], 5, 2 ) ) ;
$form->fill_field('birth_year', substr( $form->data['birthdate'], 0, 4 ) ) ;

$genderoptions["0"] = "Not Disclosed" ;

for( $i = 1 ; $i < count($gender) ; $i++ ) {

    $genderoptions[ $i ] = $gender[$i]["label"] ;
}


$bit_options["1"] = "Yes" ;
$bit_options["0"] = "No" ;

?>


	Birthdate:
	
	<br />
	
<?php 

    $form->date_input( "birth", 1900, ( date("Y") - 18 ) ) ;
    
    $form->if_error( "birth", '<br />%%ERROR%%' ) ;

?>
	<br /><br />
	Gender
	<br />
<?php 


    $form->select("gender", $genderoptions ) ;
    
    $form->if_error( "gender", '<br />%%ERROR%%' ) ;

?>

	<br /><br />
	
	Show Age in Profile? 
	
<?php 
    
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
	
	About You: <br />
	
	<div style = "width:600px"> 
	
<?php $form->textarea_field('detail', 'width: 100%; height: 100%'); ?>

    </div>
		
<?php 

$form->submit_button(); 
$form->footer() ;
$page->footer() ; ?>