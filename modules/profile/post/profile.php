<?php

$post->hold( 'birth_month', 'birth_day', 'birth_year', 'gender', 'show_age', 'email_notification', 'invite_notification', 'allow_contact', 'detail', 'city' ) ;



$_POST["birthdate"] = $_POST['birth_year']."-".$_POST['birth_month']."-".$_POST['birth_day'] ;

$date = new DateTime();
$date->sub(new DateInterval('P18Y'));

$post->require_true( $_POST["birthdate"] <= $date->format('Y-m-d'), "birth",  "You must be at least 18 years old to register." ) ;
$post->require_true( preg_match( '/\d\d\d\d-\d\d-\d\d/', $_POST['birthdate'] ) == 1, 'birth', 'There is a serious problem with your data submission.' ) ;

$post->checkpoint() ;

$sp = $db->build_set_string_from_post( 'birthdate', 'gender', 'detail','city') ;
$sa = $db->build_set_string_from_post( 'show_age', 'email_notification', 'invite_notification', 'allow_contact' ) ;

$db->update( "UPDATE `user_profile` SET ".$sp." WHERE `user_id`='".$user->id."'" ) ;
$db->update( "UPDATE `user_account` SET ".$sa." WHERE `user_id`='".$user->id."'" ) ;

$post->json_reply( 'SUCCESS' ) ;

header( "Location: ".$baseurl.$user->id ) ;
die() ;