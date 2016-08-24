<?php
function oe_time( $timestamp='' ) // returns date as YYYY-MM-DD HH:MM:SS
{
    if( $timestamp == '' )
    {
        $timestamp = time() ;
    }

    return date( 'Y-m-d H:i:s', $timestamp ) ;
}

$sql_config[ 'insert' ]['host'] = 'db.catalystpoint.net' ;
$sql_config[ 'insert' ]['db'] = 'codexfive_db' ;
$sql_config[ 'insert' ]['user'] = 'kinkyrobot' ;
$sql_config[ 'insert' ]['pass'] = 'p3rv3rse!d3l1ghts' ;

$sql_config[ 'select' ] = $sql_config[ 'insert' ] ;

define( 'sql_error_log', "../oe_logs/sqlerrors.log" ) ;

include( "../core/_lib/mysqli_minion.php" ) ;

$db = new mysqli_minion($sql_config) ;

?>
<html>
<head>
<title>SITE ADMIN TOOLS</title>
</head>
<body>
<?php 

    switch( $_GET["task"] ){
        
        case 'emptysqllog':
            
            $f = @fopen( '../oe_logs/sqlerrors.log', 'w' );
            fwrite( $f, "LOG CLEARED ".oe_time().PHP_EOL ) ;
            fclose( $f ) ;
        
        case 'displaysqllog' :
            
            $f = @fopen( '../oe_logs/sqlerrors.log', 'r' ) ;
            $c= fread( $f , filesize('../oe_logs/sqlerrors.log') ) ;
            fclose($f) ;
            ?>
            <textarea style="width:900px; height:200px"><?php print($c); ?></textarea>
            
            
            <?php
        
            break;
        
    }


?>

<form>
<select name="task">
	<option value="displaysqllog">Display SQL LOG</option>
	<option value="emptysqllog">Empty SQL log</option>
	

</select>


<input type="submit" />
</form>

</body>
</html>