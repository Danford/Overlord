<?php
/*
 * 
 *  I'm leaving this fucker alone, because it already contains all the gear we need to work with.
 *  
 *  It needs a city option, though.
 * 
 * 
 */
include(oe_lib.'form_minion.php');
include(oe_frontend."page_minion.php");

$page = new page_minion("Edit Your Profile");
$form = new form_minion('editProfile', 'profile');

$page->addjs('/js/tinymce/tinymce.min.js');
$page->addjs('/js/invoketinymce.js');
$page->addjs('/js/profile.edit.js');

$page->header();
$form->header();

$form->fill_from_db( $db, "SELECT `gender`,`birthdate`,`detail`, `allow_contact`, `show_age`,`email_notification`,
                            `invite_notification`, `city_id`
                            FROM `profile`, `user_account` WHERE `profile`.`user_id`='".$user->id."' 
                                AND `profile`.`user_id` = `user_account`.`user_id`") ;

$form->fill_field('birth_day', substr($form->data['birthdate'], 8, 2 ));
$form->fill_field('birth_month', substr($form->data['birthdate'], 5, 2));
$form->fill_field('birth_year', substr($form->data['birthdate'], 0, 4));

$genderoptions["0"] = "Not Disclosed";

for ($i = 1; $i < count($gender); $i++) {

    $genderoptions[$i] = $gender[$i]["label"];
}


$bit_options["1"] = "Yes";
$bit_options["0"] = "No";

?>
	Birthdate:
	<br />
<?php 
    $form->date_input("birth", 1900, (date("Y") - 18));
    $form->if_error("birth", '<br />%%ERROR%%');
?>
	<br /><br />
	Gender
	<br />
<?php 


    $form->select("gender", $genderoptions);   
    $form->if_error("gender", '<br />%%ERROR%%');
?>
	<div>
		<p>
			Zip: <?php $form->text_field("zip", "width: 100px"); ?>
			<?php $form->if_error("zip", "<br />%%ERROR%%"); ?>
		</p>	    
	</div>
	
	<p>City (Optional):</p>
	<?php $form->hidden("city_id", ""); ?>
    <div id="city-input">
    <input type="text" name="city-input" id="city-input" autocomplete="off" value="" style="width: 300px">
	    <div id="city-autocomplete-wrapper">
		    <div id="city-autocomplete">
		    
		    </div>
		</div>
    </div>
    <?php $form->if_error("city_id", '<br />%%ERROR%%'); ?>
	
	Show Age in Profile? 
	<?php $form->select("show_age" , $bit_options); ?>
	<br /><br />
	
	Allow messages from users not in your friends list? 
	<?php $form->select("allow_contact", $bit_options); ?>
	<br /><br />
	
	Receive e-mail notification of new messages?
	<?php $form->select("email_notification", $bit_options ); ?>
	<br /><br />
	
	Receive e-mail notification of new event invitations?
	<?php $form->select("invite_notification", $bit_options); ?>
	<br /><br />
	
	About You: <br />
	<div style="width:600px"> 
		<?php $form->textarea_field('detail', 'width: 100%; height: 100%'); ?>
    </div>
		
<?php 

$form->submit_button(); 
$form->footer() ;
$page->footer() ;

?>