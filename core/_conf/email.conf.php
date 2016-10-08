<?php


/*
 *  siteurl is used for generating links in emails.  
 *  
 *  The addresses below are used as the 'from' field in autogenerated emails.
 *  
 */

$oe_mail['reg']['address'] = "Site Registration <do-not-reply@codexfive.net>" ;

$oe_mail['reg']['subject'] = 'Activate your Membership' ;






$oe_mail['reg']['message'] = "A user at ".get_client_ip()." has registered an account with our website.  If this was not you,
take no action, and the request will expire after 48 hours.  To activate your membership, click the
link below or copy and paste it into your web browser.

        ".siteurl."register/activate/%%USERID%%/%%KEY%%" ;



$oe_mail['reset']['address'] = "Password Reset <do-not-reply@codexfive.net>" ;

$oe_mail['reset']['subject'] = "Reset Your Password" ;

$oe_mail['reset']['message'] = "A user at ".get_client_ip()." has requested a password reset.  If this was not you,
take no action, and the request will expire after 24 hours.  To reset your password, click the
link below or copy and paste it into your web browser.

        ".siteurl."login/passwordreset/%%USERID%%/%%KEY%%" ;