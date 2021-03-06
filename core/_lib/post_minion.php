<?php

/*

	Post handler Minion

	The post handler serves three functions:

	1) Processes all data provided by the form (specifically one generated by the form handler), and verifies that the data is as intended.

	2) If the data is not as intended, it puts the appropriate errors and values into the session, and returns the site visitor to the submitting form, which will be prefilled accordingly and have any errors noted.

	3) Using build_set_string, the developer can simplify the process of making a query from the POST data.


	Methods:

	hold( $field1 [ , $field2 ... ] )

		Fields flagged with this method will be "kept" and used to repopulate the form if the visitor is returned to it.

		All fields listed with hold() must have a value, or an error is thrown to the browser and the script will die().  This is a security feature, 
		though it may be more trouble than it is worth.

	checkpoint()

		If any errors have been encountered in the process, checkpoint() will use the list created by hold() to add the requested fields to the session 
		to repopulate the form, add any errors to the session, and return the user to the form, using $_POST["oe_return"] as the target.

	require_field( $field [, $message= "This is a required field."] )

		If the requested field is not filled out, an error will be added to the error list.
		If the requested field is not supplied, the form will bomb out to the error page.

		DOES NOT WORK with <select multiple>.

	require_set( $field1, $field2, ... )

		Same as require_field, but allows multiple fields.  Does not allow a custom error message.

		DOES NOT WORK with <select multiple>.

	require_true( $statement, $field, $error )

		$statement is any statement that resolves to true or false.  If $statement resolves to false,
		the supplied $error will be added to the designated $field.

		Examples:

			$post->require_true( ( $_POST["zip"] != "90210" ), $zip, "We don't like Beverly Hills." )

			$x = ( $_POST["donation"] > 499 ) ;

			$post->require_true( $x, "donation", "Minimum donation is $500." ) ;

	validate_email( $field )

			Rudimentary check to verify that the field is an e-mail address.

	build_set_string_from_post( $field1 [,$field2 ...] )

			Returns the fields listed in a string compatible with a mySQL SET statement for use
			in updating the database, for example: "`field1`='x', `field2`='y'"

	checkbox( $field1 [,$field2 ] )

			For fields submitted using an HTML Checkbox.

			For the purposes of the Minions, checkboxes are either "y" (checked) or "n" (not checked).  Since a checkbox variable is not submitted if not checked, 
			this field checks to see if the variable has been submitted, and if not, defines it with a value of "n".

	require_checked( $field, $message )

			Invokes checkbox(), but also reports an error to the submitting form if a given checkbox is not checked.

	require_selected( $field, $message )

			If field is from a radio button, one radio button must be selected or returns an error to the submitting form.

			If field is from a <select>, the selected field must not have a value of " ".  This allows for a "Choose One:" option.

	require_specific( $field, $value, $error )

			The specified field must be equal to the specified value.

	ms_require_selected( $field, $error, $count=1 )

			For <select multiple>.  Sets the lower required limit of selected items.

			Also, if no items are selected, a placeholder value of "" assigned to the field so that it can be included in hold() without throwing an error.

	ms_require_count( $field, $error, $count )

			For <select multiple>.  Sets an exact required limit of selected items.

			Also, if no items are selected, a placeholder value of "" assigned to the field so that it can be included in hold() without throwing an error.

	ms_restrict_selected( $field, $error, $count )

			For <select multiple>.  Sets the upper required limit of selected items.

			Also, if no items are selected, a placeholder value of "" assigned to the field so that it can be included in hold() without throwing an error.

	ms_require_specific( $field, $value, $error )


			For <select multiple>.  Requires that $value be among the selected fields. Use multiple values

			Also, if no items are selected, a placeholder value of "" assigned to the field so that it can be included in hold() without throwing an error.

	date_combine( $prefix )

			takes submitted fields for $prefix._year, $prefix._month, and $prefix._day and combines them into a single string ( yyyy-mm-dd ) as $prefix

	set_error( $field, $error ) 

			Sets the error for a given field to the given value.  

	Version History

	1.0 - April 2005 - Got tired of doing all this manually.
	2b - February 2005 - Overhaul

		Significant error conditions previously required external coding to support them, but now error out on their own.  This may be reversed with a new setup.  Also, I may add functions that make database queries automatically, using the database minion..

		Added require_checked(), require_selected(), require_specific(), ms_require_selected(), ms_require_specific(), ms_require_count(), and ms_restrict_selected().

	2.1 - December 24, 2007 
		
		Added addslashes() to "build_set_string"

	2.2 - January 26, 2008 

		Added _ prefix to $_POST["uri"].
		Changed errors to use oe_error for reporting.
		This was for integration into the Overlord engine

	2.2.1 February 4, 2008

		Added methods set_error(), form_error(), and site_error() 
		renamed values to "_oe_prefill" and "_oe_error"

	3.0 - March 26, 2009 

		changed session variables for errors and form data.   
		Changed return value from $_POST["_uri"} to $POST["oe_return"].
		removed form_error() and site_error(). 

	3.0.1 - April 11, 2009

		added date_combine() 
		
	4.0 - August 29, 2016
	
	   Added a constructor method and json reply functionality.  
	   
	4.0.1 - September 16, 2016
	 
	   Ditched $_POST['oe_return'] in favour of $_SERVER[ 'HTTP_REFERER' ]

*/

class post_minion
{

	function __construct() {
	    
	    $this->is_a_json_request = ( isset( $_POST['oe_api_type'] ) and $_POST['oe_api_type'] == 'json' ) ;
	    
	}

	function hold()
	{

		$arg_list = func_get_args() ;

		foreach( $arg_list as $field )
		{

			$this->hold_list[] = $field ;

		}

	} // end method hold

	function checkpoint()
	{

		if ( isset( $this->form_error ) )
		{

	        $this->json_reply( 'ERROR', $this->form_error ) ;

			if ( isset( $this->hold_list ) )
			{

				foreach( $this->hold_list as $hold )
				{

					$prefill[ $hold ] = $_POST[ $hold ] ;

					$_SESSION["oe_form"][$_POST["oe_call"]]["data"]  = $prefill ;

				}

			}

			$_SESSION["oe_form"][$_POST["oe_call"]]["error"] = $this->form_error ;

			$this->return_to_form() ;
			die() ;

		}

	} // end method checkpoint

	function return_to_form(){
	    header( "Location: ".$_SERVER[ 'HTTP_REFERER' ] ) ;
	    die() ;
	}
	

	function require_field( $field, $message= "This is a required field." )
	{

		if( ! isset( $_POST[ $field ] ) )
		{
			die( "ERROR POST 01" ) ;
		}

		if( $_POST[ $field ] == "" )
		{

			$this->form_error[ $field ] = $message ;

		}

	} // end method require
	
	function require_date( $prefix, $message ) {
	    
	    if( ! isset( $_POST[ $prefix."_month" ] ) or ! isset( $_POST[ $prefix."_day" ] ) or ! isset( $_POST[ $prefix."_year" ] ) )
	    {
	        die( "ERROR POST 02" ) ;
	    }
	    
	    if( $_POST[ $prefix."_month" ] == "__" or $_POST[ $prefix."_day" ] == "__" or $_POST[ $prefix."_year" ] == "__" or 
	        $_POST[ $prefix."_month" ] == "" or $_POST[ $prefix."_day" ] == "" or $_POST[ $prefix."_year" ] == ""  )
	    {
	       $this->form_error[ $prefix ] = $message ;
	    }
	    
	}

	function require_set()
	{


		$arg_list = func_get_args() ;

		foreach( $arg_list as $field )
		{

			$this->require_field( $field ) ;

		}


	} // end method require_set

	function require_true( $statement, $field, $error )
	{


		if( ! $statement )
		{

			$this->form_error[ $field ] = $error ;

		}

	} // end method require_true


	function validate_email( $field, $error = "This is not a valid e-mail address." )
	{

		$stepone = explode( "@", $_POST[ $field ] ) ;

		if( count( $stepone ) != 2 )
		{

			$this->form_error[ $field ] = $error ;

		}
		else
		{

			$steptwo = explode( ".", $stepone[1] ) ;

			if( count( $steptwo ) < 2 )
			{

				$this->form_error[$field] = $error ;

			}


		}

	} //end method validate_email

	function checkbox()
	{

		// if a field listed here is not present in $_POST, it will be assigned a value of "off".


		$arg_list = func_get_args() ;

		foreach( $arg_list as $field )
		{

			if ( ! isset( $_POST[ $field ] ) or $_POST[$field] != "on" )
			{

				$_POST[ $field ] = "off" ;

			}
		}


	} // end method checkbox


	function require_checked( $field, $error )
	{

		$this->checkbox( $field ) ;

		if( $_POST[ $field ] != "on" )
		{

			$this->form_error[$field] = $error ;
		}

	} // end method require_checked

	function require_selected( $field, $error )
	{
		if( ! isset( $_POST[ $field ] ) )
		{

			$_POST[ $field ] = "__" ;

		}

		if( $_POST[ $field ] == "__" )
		{

			$this->form_error[$field] = $error ;

		}

	} // end method require_selected

	function require_specific( $field, $value, $error )
	{

		if( $_POST[ $field ] != $value )
		{

			$this->form_error[$field] = $error ;
		}


	} // end method require_specific

	function ms_require_selected( $field, $error, $count=1 )
	{

		if( ( ! isset( $_POST[ $field ] ) ) or ( ! is_array( $_POST[ $field ] ) ) )
		{

			$_POST[$field] = "" ;

			$this->form_error[$field] = $error ;
		}
		elseif( count( $_POST[ $field ] ) < $count )
		{

			$this->form_error[$field] = $error ;

		}

	} // end method ms_require_selected

	function ms_require_count( $field, $error, $count )
	{

		if( ( ! isset( $_POST[ $field ] ) ) or ( ! is_array( $_POST[ $field ] ) ) )
		{

			$_POST[$field] = "" ;

			$this->form_error[$field] = $error ;
		}
		elseif( count( $_POST[ $field ] ) != $count )
		{

			$this->form_error[$field] = $error ;

		}

	} // end method ms_require_count

	function ms_restrict_selected( $field, $error, $count )
	{

		if( ( ! isset( $_POST[ $field ] ) ) or ( ! is_array( $_POST[ $field ] ) ) )
		{

			$_POST[$field] = "" ;

			// does not return an error, as NO selection has been made, rather than too many

		}
		elseif( count( $_POST[ $field ] ) > $count )
		{

			$this->form_error[$field] = $error ;

		}

	} // end method ms_restrict_selected

	function ms_require_specific( $field, $value, $error )
	{

		if( ( ! isset( $_POST[ $field ] ) ) or ( ! is_array( $_POST[ $field ] ) ) )
		{

			$_POST[$field] = "" ;

			$this->form_error[$field] = $error ;
		}
		elseif( ! in_array( $value, $_POST[ $field ] ) )
		{

			$this->form_error[$field] = $error ;

		}

	} // end method ms_require_specific
	
	function date_combine( $prefix )
	{
		if( $_POST[$prefix."_year"] == "__" ){ $_POST[$prefix."_year"] = "9999" ; }
		if( $_POST[$prefix."_month"] == "__" ){	$_POST[$prefix."_month"] = "99" ; }
		if( $_POST[$prefix."_day"] == "__" ){ $_POST[$prefix."_day"] = "99" ; }
		
		$_POST[$prefix] = $_POST[$prefix."_year"]."-".$_POST[$prefix."_month"]."-".$_POST[$prefix."_day"] ;
		
		return $_POST[$prefix] ;
	}

	function time_combine( $prefix ) {
	    
	    if( $_POST[ $prefix.'_meridian'] == "AM" ){
	    
	        if( $_POST[$prefix.'_hour'] != '12' ) {
	            
	            if( $_POST[ $prefix.'_hour'] < 10 ){

	               $timestring = "0".$_POST[$prefix.'_hour'] ;
	            } else {
	               $timestring = $_POST[$prefix.'_hour'] ;
	            }
	        } else {
	            $timestring = '00' ;
	        }
	    
	    
	    } else {
	    
	    
	        if( $_POST[$prefix.'_hour'] != '12' ) {
	            $timestring =  ( $_POST[$prefix.'_hour'] + 12 ) ;
	        } else {
	            $timestring = '12' ;
	        }
	    
	    }
	    
	    if( $_POST[$prefix."_minute"] < 10 ){
	        $timestring .= ':0'.$_POST[$prefix."_minute"] ;
	    } else {
	        $timestring .= ':'.$_POST[$prefix."_minute"] ;	        
	    }
	    
	    $_POST[$prefix."_time"] = $timestring ;
	    return $timestring ;
	}
	
	function set_error( $field, $error )
	{
	
		$this->form_error[$field] = $error ;


	} // end method set_error

	function json_reply( $status, $content = null ) {

	    if( $this->is_a_json_request ){
	    
	        header('Content-Type: application/json');
	        
    	    $response = array() ;
    	    $response['api_status'] = $status ;
    	    
    	    if( $content != null ) {
    	    
        	    $response['response'] = array() ;
        	    
        	    if( is_array( $content ) ){
        	        $response['response'] = $content ;
        	    } elseif ( $content != null ) {
        	        $response['response']['message'] = $content ;
        	    }
    	    }
    	    
    	    echo( json_encode( $response ) ) ;
    	    die() ;
	    }	    
	}
	
	
} // end class post_handler

?>