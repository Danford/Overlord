<?php


/*

    MYSQLI Minion 1.0

	The single-query, single use methods allow you to perform your query and return the results in one step.  
	Result set is automatically freed after use.

		get_field( $query_string [, $position ] ) 		returns a single value
		get_assoc( $query_string )						returns an associative array of the first matching row
		get_row( $query_string )						returns an numerical array of the first matching row
		get_obj( $query_string )						returns an object of the first matching row
		get_affected( $query string )					performs a query and returns the number of rows affected (mysql_affected_rows)
		insert( $query_string )							performs an insert query and returns the insert id

	These are useful for quick queries, but if you want to perform multiple functions on a singly query, then you will be better served 
	by the co-operative functions.  Once a query is called by the query() method, it can be called again by another method.  Optionally, 
	you can specify a query id, which will allow you to manage multiple simultaneous and/or nested queries, but if you are performing a 
	single query at a time, then you may leave that and the methods will use a default query id.
	
		query( $query_string[, $query_id ] )		performs the initial query
		count( [$query_id] )						returns the value for mysql_num_rows
		assoc( [$query_id] )						returns an associative array of the next row of the query results
		row( [$query_id)                            returns a numerical array of the next row of the query results
		obj( [$query_id)                            returns an object of the next row of the query results
		field( [ $position, $query_id ] )			returns a single value from the next row of the query results

    Co-operative functions will automatically free() the result set once you reach the end, but they can also be freed manually 
    with 
    
        free( $query_id )                           frees the result set

    VERSION HISTORY
    
        1.0 - 2016-03-13 - 
        
            A complete reworking of the original mysql minion, which used deprecated mysql functionality.


*/


class mysqli_minion {
    
    var $log ;
    var $sql_config ;
    var $multi_mode ;
    var $write_db ;
    var $read_db ;
    var $result = array() ;
    var $prepared_stmt = array() ;
    var $prepared_stmt_key = array() ;
    var $prepared_stmt_param = array() ;
    var $prepared_stmt_result = array() ;
    
    function __construct( $sql_config_array, $logfile = sql_error_log, $multi_mode_flag = false ) {
        $this->log = $logfile ;
        $this->sql_config = $sql_config_array ;
        $this->multi_mode = $multi_mode_flag ; // if false, there's only one db
        $this->write_db = false ;
        $this->read_db = false ;
        
        
    }
    
    // internal methods
    
    function log_error( $error ) {
        
        $f = @fopen( $this->log, 'a' ) ;
        fwrite( $f, oe_time()." ".$error.PHP_EOL ) ;
        fclose( $f ) ;       
        
    }
    
    function write_connection() {
        if( ! $this->write_db ) {
            
            // establish a connection to insert db if there isn't one already
            
            $this->write_db = new mysqli( $this->sql_config['insert']['host'], $this->sql_config['insert']['user'], 
                                                        $this->sql_config['insert']['pass'], $this->sql_config['insert']['db']) ;
            
            if( $this->write_db->connect_error ){
                
                $this->log_error( "FAILED TO CONNECT TO ".$this->sql_config['insert']['host'].": ".$this->write_db->connect_errno ) ;                
                die( "FAILED TO CONNECT TO ".$this->sql_config['insert']['host'].": ".$this->write_db->connect_error ) ;
                
            }
        }
        
        return $this->write_db ;
    }
    
    function read_connection() {
        
        if( ! $this->read_db ) {
            
            if( ! $this->multi_mode ) {
            
                // if not in multiple server mode, just use the insert connection
                
               
                $this->read_db =  $this->write_connection() ;
            
            } else {
                
                $this->read_db = new mysqli( $this->sql_config['select']['host'], $this->sql_config['select']['user'], 
                                                        $this->sql_config['select']['pass'], $this->sql_config['select']['db']) ;
                
                if( $this->read_db->connect_error ){
                
                    $this->log_error( "FAILED TO CONNECT TO ".$this->sql_config['select']['host'].": ".$this->write_db->connect_errno ) ;
                    die( "FAILED TO CONNECT TO ".$this->sql_config['select']['host'].": ".$this->write_db->connect_error ) ;
                }
            }
        }
        
        return $this->read_db ;
    }
    
    // cooperative methods
    
    function sanitize( $string ) {
        
        $this->write_connection() ;
        
        return $this->write_db->real_escape_string($string) ;
    }
    
    function get_field( $query_string, $position = 0 ){
        
        $this->read_connection() ;
        
        if( ( $result = $this->read_db->query( $query_string ) ) != false ) {
            
            $grab = $result->fetch_row() ;
            
            $result->free() ;
            
            return $grab[ $position ] ;
            
        }
        else { 

            $this->log_error( $this->write_db->error.$this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
        
    }
    
    function get_assoc( $query_string ) {
        
        $this->read_connection() ;
        
        if( ( $result = $this->read_db->query( $query_string ) ) != false  ) {
            
            $grab = $result->fetch_assoc() ;
            
            $result->free() ;
            
            return $grab ;
            
        }
        else { 

            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
    }
    
    function get_row( $query_string ) {
        
        $this->read_connection() ;
        
        if( ( $result = $this->read_db->query( $query_string ) ) != false  ) {
            
            $grab = $result->fetch_row() ;
            
            $result->free() ;
            
            return $grab ;
            
        }
        else { 

            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
    }
    
    function get_obj( $query_string ) {
        
        $this->read_connection() ;
        
        if( ( $result = $this->read_db->query( $query_string ) ) != false  ) {
            
            $grab = $result->fetch_object() ;
            
            $result->free() ;
            
            return $grab ;
            
        }
        else { 

            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
    }
    
    function get_affected( $query_string ) {
        
        $this->write_connection() ;
        
        if( ( $result = $this->write_db->query( $query_string ) ) != false  ) {
            
            $grab = $this->write_db->affected_rows ;
            
            $result->free() ;
            
            return $grab ;
            
        }
        else { 

            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
    }

    function insert( $query_string ) {
        
        $this->write_connection() ;
        
        if( $this->write_db->query( $query_string ) ) {
            
            $grab = $this->write_db->insert_id ;
            
            return $grab ;
            
        }
        else { 

            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
        
    }

    function update( $query_string ) {
        
        $this->write_connection() ;
        
        if( $this->write_db->query( $query_string ) ) {
            
            $grab = $this->write_db->affected_rows ;
            
            return $grab ;
            
        }
        else { 

            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
            
            return false ;
        }
        
    }
    
    // cooperative methods
    
    function query( $query_string, $qid = 'DEFAULT' ) {
        
        $this->read_connection() ;
        
        if( ! $this->result[$qid] = $this->read_db->query( $query_string ) ) {
            $this->log_error( $this->write_db->error ) ;
            $this->log_error( $query_string.PHP_EOL ) ;
        }
    }
    
    function count( $qid = 'DEFAULT' ) {
        
        return $this->result[$qid]->num_rows ;
    }
    
    function assoc(  $qid = 'DEFAULT' ) {
        
        if( ( $set = $this->result[$qid]->fetch_assoc() ) != false  ) {
            return $set ;
        } else {
            $this->result[$qid]->free() ;
            return false ;
        }
    }
    
    function row(  $qid = 'DEFAULT' ) {
        
        if( ( $set = $this->result[$qid]->fetch_row() ) != false  ) {
            return $set ;
        } else {
            $this->result[$qid]->free() ;
            return false ;
        }
    }
    
    function obj(  $qid = 'DEFAULT' ) {
        
        if( ( $set = $this->result[$qid]->fetch_object() ) != false  ) {
            return $set ;
        } else {
            $this->result[$qid]->free() ;
            return false ;
        }
    }
    
    function field(  $position = 0, $qid = 'DEFAULT' ) {
        
        if( ( $set = $this->result[$qid]->fetch_row() ) != false  ) {
            return $set[$position] ;
        } else {
            $this->result[$qid]->free() ;
            return false ;
        }
    }
    
    function num_rows( $qid="DEFAULT" ) {
        return $this->result[$qid]->num_rows ;
    }
    
    function free( $qid = 'DEFAULT' ) {
            $this->result[$qid]->free() ;        
    }
    
    function build_set_string_from_post() {
    
        // builds, and then returns a string to be used between "SET" and "WHERE"
        // in a SQL 'UPDATE' query, using the fields named and the POSTed values for them.
    
    
        $arg_list = func_get_args() ;
        $arg_count = count( $arg_list ) ;
    
        if( $arg_count < 1 )
        {
            die( "Method build_set_string has no arguments." );
        }
    
        $count = 1 ;
        $set_string = "" ;
    
        foreach( $arg_list as $field )
        {
    
            $set_string .= "`".$field."`='".$this->sanitize( $_POST[ $field ])."'" ;
    
            if ( $count != $arg_count )
            {
    
                $set_string .= ", " ;
                $count += 1 ;
            }
    
    
        }
    
        return $set_string ;
    
    } // end method build_set_string
    
    function build_set_string_from_array( $a ){
         
        // builds, and then returns a string to be used between "SET" and "WHERE"
        // in a SQL 'UPDATE' query, using an associative array
         
        $set_string = "" ;
         
        foreach( $a as $key => $val )
        {
            $set_string .= "`".$key."`='".$this->sanitize( $val )."', " ;
        }
         
        return substr( $set_string, 0 , -2 ) ;
    }
}
?>