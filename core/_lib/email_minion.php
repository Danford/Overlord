<?

$mailer = new email_minion() ;

class email_minion
{

	/*

	Mail Handler Minion

		Unlike the other Minions, the mail handler actually involves more code to use than just using the mail() function.  However, it will build the headers for you, and it accepts items one at a time, allowing for easier use in code.

	Additionally, as of version 1.2, it has the capability of keeping an archive of each e-mail sent, either by sending a BCC: of the e-mail or adding it to a database.

	At this time the code assumes that you have *correctly* provided *all the necessary data* to send and e-mail; there is no validation.   PHP may or may not throw errors due to misuse, depending on your configuration.

	CONFIGURATION:

		The archive feature requires at least one constant ( mail_backup ) to be defined.

		mail_backup can be set to the following:

			bcc

				sends a blind carbon copy e-mail to the address defined in constant mail_backup_bcc.  This constant is only required for the bcc archive.

			db
				dumps a copy to a database (specifications for the database will be added at the bottom of this documentation).  This functionality assumes that you have a mysql minion named $db, a table named "mail_archive" (specifications below), and defined function dbtime() (in minion_lib.php).

			both

				has all the requirements of db and bcc.

			none

				Actually, any value other than bcc, db, or both will work here, and result in no archive.  Unless your PHP config is set to display E_NOTICE errors, you can just leave the constant undefined for the same effect.

	VARIABLES:

		from
		subject
		body
		type	(auto or user)

	

	METHODS:

		to() and bcc()

			These add email addresses to the list of reciepients or bcc recipients.  Addresses may be added singly or in groups, separated by commas.

		send()

			Uses provided information to send an e-mail.  Will bcc and/or archive the e-mail in the database

		debug()

			Outputs your e-mail to the screen and die()s.  Does not archive the e-mail.


	DATABASE:


		Use this.

			CREATE TABLE `oe_mail_archive` (
			  `id` int(11) auto_increment,
			  `timestamp` varchar(20),
			  `ip` varchar(15),
			  `from` varchar(255),
			  `to` varchar(255),
			  `headers` tinytext,
			  `body` text,
			  PRIMARY KEY  (`id`)
			) ;


	v1.0	2007-08-15

		Very basic class.  No error checking, only the basic functionality.  It is, however, all I need for this application.  I am waiting until I NEED greater functionality before I actually ADD more functionality.  I have plans, oh, yes, but I have more important code to write.

	v1.1	2007-08-18

		Needed a debug to help figure out why it is not sending mail and not failing.

	v1.2	2007-12-12

		Added method bcc and incorporated into send() ;
		Added archive feature.

	v1.3	2009-04-08

		made from, subject, and body controlled directly by variable upon realising a function to define a variable here was superfluous.

		Added variable type which flags the e-mail in the database as automatic or user generated.  defaults to automatic.


	*/

	function to( $to )
	{

		if( isset( $this->to ) )
		{
			$this->to .= ", ".$to ;
		}
		else
		{
			$this->to = $to ;
		}

	} // end function to()

	function bcc( $bcc )
	{

		if( isset( $this->bcc ) )
		{
			$this->bcc .= ", ".$bcc ;
		}
		else
		{
			$this->bcc = $bcc ;
		}

	} // end function bcc()

	function header( $header )
	{
			if( isset( $this->headers ) )
		{
			$this->headers .= $header."\r\n" ;
		}
		else
		{
			$this->headers = $header."\r\n" ;
		}
	}
	
	
	function send()
	{

		if(( mail_backup == "bcc" ) or ( mail_backup == "both" ))
		{
			$this->bcc( mail_backup_bcc ) ;
		}

		$header = "" ;

		if( isset( $this->bcc ) )
		{
			$header .= "Bcc: ".$this->bcc."\r\n" ;
		}

		if( isset( $this->headers ))
		{
			$header .= $this->headers ;
		}

		$headers = $header."From: ".$this->from."\r\n" ;

		return @mail( $this->to, $this->subject, $this->body, $headers ) ;

		if(( mail_backup == "db" ) or ( mail_backup == "both" ))
		{
			global $db ;
			
			if( ! isset( $this->type ) )
			{
				$this->type = "auto" ;
			}

			$db->insert( "INSERT INTO oe_mail_archive
							SET `timestamp`='".oe_time()."',
							`ip`='".get_client_ip()."',
							`from`='".addslashes($this->from)."',
							`to`='".addslashes($this->to)."',
							`body`='".addslashes($this->body)."',
							`headers`='".addslashes($header)."',
							`type`='".$this->type."'" ) ;

		}


	}


	function debug()
	{

		$headers = "From: ".$this->from."\r\n" ;

		if( isset( $this->bcc ) )
		{
			$headers .= "Bcc: ".$this->bcc."\r\n" ;
		}

		?>
		To:<?=$this->to?><br /><br />
		<?=$headers?><br /><br />
		Re: <?=$this->subject?><br /><br />
		<?=$this->body?>
		<?

		die() ;

	}

} // end class mail_handler

?>