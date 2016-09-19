<?php
    
    include_once $oe_plugins['comment']."lib/comment.lib.php";
    include_once $oe_plugins['comment']."conf/conf.php";

     /*
      *  use get_comments( [$start[, $stop]] ) to retrieve an array
      * 
      *     id - comment id
      *     comment - comment text
      *     owner - profile object
      * 
      * 
      *  It is up to you whether you want the whole listing, or just to load the first part 
      *  and op to load them later.  
      * 
      * 
      */