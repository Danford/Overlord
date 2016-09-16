<?php

/*
 *  Form Minion 4.0.1
 *  
 *      Creates dynamic forms that autofill their data based on data that is manually provided, pulled from
 *      a database, or passed back to the form from the post minion
 *  
 *  
 *  
 *  form_minion( $form_id, $module[, $action, $method] )
 *      
 *  fill_with_values( $assoc[, $overwrite] ) ;
 *      
 *      Uses the associative array $assoc to prefill the table.  Will be ignored in favour of the session data, 
 *      if such exists, unless $overwrite == true.
 *      
 *  fill_from_db( $db_obj, $query[, $overwrite ] )
 *  
 *      Uses the mysqli minion $db_obj to fill the form with the results of $query, but
 *      won't run if session data exists unless $overwrite == true 
 *      
 *  fill_field( $field, $value[, $overwrite ] )
 *  
 *      Provides data for a specific field.  Will be ignored in favour of the session data, 
 *      if such exists, unless $overwrite == true.
 *      
 *  has_file()
 *  
 *      designates this form as one that will contain a file.  Must be run before $this->header()
 *      
 *  header()
 *  
 *      generates the form header and hidden values needed for operation
 *      
 *  footer()
 *     
 *      closes the form and erases the session variable used to manage it.
 *      
 *  hidden( $name, $field )
 *  
 *      adds another hidden field
 *  
 *  error( $field )
 *  
 *      returns any error for a given field that was passed from the posthandler minion
 *      
 *  is_error( $field ) 
 *  
 *      returns boolean stating whether there IS an error for a given field
 *      
 *  All the methods that follow generate the elements for which they are named, prefilled with the data, with the 
 *  following conventions:
 *  
 *          $name - the name and id of the element
 *          $css - either the name of a class, or a style statement ( ex. 'color: red; font-weight: bold' )
 *          $js - javascript trigers ( ex.  'onload="checkThisField()"' ) 
 *          $value - (buttons only) the text for the button
 *          
 *      text_field( $name[, $css, $js ] )
 *      file_input( $name[, $css, $js ] )
 *      textarea_field( $name[, $css, $js ] )
 *      pass_field( $name[, $css, $js ] )  -- does not prefill!
 *      radio_button( $name[, $css, $js ] )
 *      checkbox( $name[, $css, $js ] )
 *   
 * 
 * 
 *  Version History:

	0.x - mid 2004 to early 2005 - a series of messianic and complicated configurations that started wonderfully but 
	   eventually became more trouble than they were worth.

	0.9.x - Early 2005 - An object class

	1.0 - April 2005 - Table generation split off into a child class, begin version tracking
	1.0.1 - May 10, 2005 - fixed textarea bug
	2b - February 2006 - Complete overhaul
	2.01 - 2007-12-11 - Made constant httproot the default form action
	
	4.0 - somewhere in 2016.
	
	   Overhauled for new incarnation of the Overload project. 
	
	   Mostly just code reformatting, but added footer() to replace cosmetic function close().   Footer wipes out session 
	   variable.  Version history got lost somehow.
	   
	4.0.1 - 2016-09-16
	
	   removed field oe_return; post minion now uses $_SERVER[ 'HTTP_REFERER' ]
 * 
 * 
 */





class form_minion
{

    var $form_id ;
    var $oe_form_id ;
    var $module ;
    var $method ;
    var $form_action ;
    var $css ;
    
    
    // constructor

    function __construct( $form_id, $module, $action= httproot, $method="POST"  )
    {
        $this->form_id = $form_id ;
        $this->oe_form_id = $form_id ;
        $this->module = $module ;
        $this->method = $method ;
        $this->form_action = $action ;
        
        if( isset( $_SESSION["oe_form"][$this->oe_form_id]["data"] ) )
        {
            $this->data = $_SESSION["oe_form"][$this->oe_form_id]["data"] ;
        }        
    }
    
    function fill_with_values( $assoc="", $overwrite = false  ) 
    {
        // fills the form from the supplied associative array
        // if data has been prefilled from the session, it will not overwrite unless $overwrite == true 
        
        if( ( ! isset( $_SESSION["oe_form"][$this->oe_form_id]["data"] ) ) or $overwrite == true )
        {
            $this->data = $assoc ;
        }
    }
    
    function fill_from_db( $db_obj, $query, $overwrite = false )
    {
        // fills the form from the DB, assuming that it does not currently exist in the session
        // if data has been prefilled from the session, it will not overwrite unless $overwrite == true 
    
        if( ( ! isset( $_SESSION["oe_form"][$this->oe_form_id]["data"] ) ) or $overwrite == true )
        {
            $this->data = $db_obj->get_assoc( $query ) ;
        }
    }
    
    function fill_field( $field, $value, $overwrite = false )
    {
        // should only be used AFTER fill_with_values() or fill_from_db(), else these functions will overwrite this usage
    
        if( ( ! isset( $_SESSION["oe_form"][$this->oe_form_id]["data"] ) ) or $overwrite == true )
        {$this->data[$field] = $value ; }
    }
    
    function has_file() {
        $this->type = "file" ;
    }
    
    function form_style( $style ){
        $this->css = $style ;
    }
    
    function header()
    {    
    
        print( '<form name="'.$this->form_id.'" id="'.$this->form_id.'" method="'.$this->method.'" action="'.$this->form_action.'"' );
        
        if( isset( $this->type ) and ( $this->type == "file" ) )
		{
			print( ' enctype="multipart/form-data" ') ;
		}

		print( $this->check_css( $this->css ) );
		
		print( '>'.PHP_EOL.'<input type="hidden" name="oe_call" value="'.$this->oe_form_id.'" />'.PHP_EOL ) ;
    	print( '<input type="hidden" name="oe_post_api" value="'.$this->module.'" />'.PHP_EOL ) ;
    
	} // end header()

	function footer()
	{
		print( '</form>' );
		
		unset( $_SESSION["oe_form"][$this->oe_form_id] ) ;
	} // end footer()
	
	
	// internal use
	
	function check_css( $css )
	{
	    // identifies whether a CSS statement is a style or a class.  (Classes have no colons.)
	
	    if( strpos( $css, ':' ) != false  ) 
	    {
	        return( "style=\"$css\"" ) ;
	    }
	    elseif( $css != "" )
	    {
	        return( " class=\"$css\" " ) ;
	    }
	    else
	    {
	        return "" ;
	    }
	
	} // end method check_css
	
	function value( $field )
	{
	    if( isset( $this->data[$field] ))
	    {
	        return $this->data[$field] ;
	    }
	    else
	    {
	        return "" ;
	    }
	}
	

	// form elements
	
	function hidden( $name, $value )
	{
	    print( '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'" />' ) ;
	} //end method hidden

	function text_field( $name, $css="", $js="" )
	{
		print( '<input type="text" name="'.$name.'" id="'.$name.'" value="'.$this->value($name).'" '.$this->check_css( $css )." " ) ;

		if( $js != "" ){ print( $js." " ) ; }

		print( '/>' ) ;
	
	}
	
	function file_input( $name, $css="", $js="" )
	{
		print( '<input type="file" name="'.$name.'" id="'.$name.'" value="'.$this->value($name).'" '.$this->check_css( $css )." " ) ;

		if( $js != "" ){ print( $js." " ) ; }

		print( '/>' ) ;
	}
	
	function submit_button( $value="Submit", $name="Submit", $css="", $js="" )
	{
		print( '<input type="submit" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$this->check_css( $css )." " ) ;

		if( $js != "" ){ print( $js." " ) ; }

		print( '/>' ) ;
	}
	
	function reset_button( $value="Reset", $name="Reset", $css="", $js="" )
	{
		print( '<input type="reset" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$this->check_css( $css )." " ) ;

		if( $js != "" ){ print( $js." " ) ; }

		print( '/>' ) ;
	}
	
	function plain_button( $value, $name, $css="", $js="" )
	{
		print( '<input type="button" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$this->check_css( $css )." " ) ;

		if( $js != "" ){ print( $js." " ) ; }

		print( '/>' ) ;
	}
	
	function textarea_field( $name,  $css="", $js="" )
	{
	    print( '<textarea name="'.$name.'" id="'.$name.'" value="'.$this->value($name).'" '.$this->check_css( $css )." " ) ;
	    
	    if( $js != "" ){ print( $js." " ) ; }
	    
	    print( '>'.$this->value($name).'</textarea>' ) ;
	    
	}
	
	function pass_field( $name="password", $css="", $js="" )
	{
	    // there is no way to default this field.  This is deliberate.

	    print( '<input type="password" name="'.$name.'" id="'.$name.'" '.$this->check_css( $css )." " ) ;
	    
	    if( $js != "" ){ print( $js." " ) ; }
	    
	    print( '/>' ) ;
	    
	}
	
	function select( $name, $options_assoc, $multiple=false, $css="", $js="" )
	{
    
        if( $multiple ){
            print( '<select multiple name="'.$name.'[]" id="'.$name.'" '.$this->check_css( $css ) ) ; 
            
            if( $js != "" ){ print( $js." " ) ; } 
            
            print( '>'.PHP_EOL ) ;
            
        } else {
 
            print( '<select name="'.$name.'" id="'.$name.'" '.$this->check_css( $css ) ) ; 
            
            if( $js != "" ){ print( $js." " ) ; } 
            
            print( '>'.PHP_EOL ) ;
        }
            
   		foreach( $options_assoc as $key => $val ) {
    			
    		print( '<option value="'.$key.'" ' ) ;
    
    		if (( isset( $this->data[$name] )) and ( $key == $this->data[$name] )){ 
    		    print( ' selected="selected" '); 
    		}
    
    		print( '>'.$val.'</option>'.PHP_EOL ) ;
   		}
    
    	print( '</select>'.PHP_EOL ) ;
    	
    } // end method select	
    
    function radio_button( $name, $css="", $js="" )
    {

        print( '<input type="radio" name="'.$name.'" id="'.$name.'" value="'.$this->value($name).'" '.$this->check_css( $css )." " ) ;
        
        if( $js != "" ){ print( $js." " ) ; }
        
        print( '/>' ) ;
        
        
    }

    function checkbox( $name, $css="", $js="" )
    {
    
        print( '<input type="checkbox" name="'.$name.'" id="'.$name.'" '.$this->check_css( $css )." " ) ;

        if( $js != "" ){ print( $js." " ) ; }

        if( $this->data[$name] == "on" ){ print( ' checked="checked" ') ; }
    
    	print( '/>' ) ;
    
    } // end method checkbox

	function date_input( $prefix, $start_year=1900, $end_year="" )
	{

		if( isset( $this->data[$prefix] ) )
		{
			// This allows the date to be stored as a single string ( "YYYY-MM-DD" or "YYYY-MM-DD HH:MM:SS" )
			// but manipulated with a friendly three-element form.

				$this->data[ $prefix."_year"] = substr( $this->data[$prefix], 0, 4 ) ;
				$this->data[ $prefix."_month"] = substr( $this->data[$prefix], 5, 2 ) ;
				$this->data[ $prefix."_day"] = substr( $this->data[$prefix], 8, 2 ) ;
		}

		if( $end_year == "" )
		{
			$end_year = date( "Y" );
		}

		$month["__"]="Month" ;
		$month["01"]="January" ;
		$month["02"]="February" ;
		$month["03"]="March" ;
		$month["04"]="April" ;
		$month["05"]="May" ;
		$month["06"]="June" ;
		$month["07"]="July" ;
		$month["08"]="August" ;
		$month["09"]="September" ;
		$month["10"]="October" ;
		$month["11"]="November" ;
		$month["12"]="December" ;

		$count = 1 ;

		$day["__"]= "Day" ;
		
		while( $count < 32 )
		{
			if( $count < 10 )
			{
				$day[ $count ] = "0".$count ;
			}
			else
			{
				$day[$count] = $count ;
			}

			$count += 1 ;
		}

		$count = $end_year ;

		$year["__"] = "Year" ;

		while( $count >= $start_year )
		{
			$year[$count]=$count ;
			$count = $count - 1;
		}


		print( $this->select( $prefix."_month", $month )." ".$this->select( $prefix."_day", $day )." ".$this->select( $prefix."_year", $year )." " ) ;

	} // end method date_input
	
	function error( $field )
	{
		if( isset ( $_SESSION["oe_form"][$this->oe_form_id]["error"][$field] ) )
		{
			return( $_SESSION["oe_form"][$this->oe_form_id]["error"][$field] ) ;
		}
		else
		{
			return "" ;
		}

	} // end method error

	function is_error( $field )
	{
		return( isset ( $_SESSION["oe_form"][$this->oe_form_id]["error"][$field] ) ) ;

	} // end method is_error
	
	function if_error( $field, $outputstring ) {
	    
	    if( $this->is_error( $field ) ) {
	        
	        print( str_replace( '%%ERROR%%', $this->error($field), $outputstring ) ) ;
	    }
	    
	}
}