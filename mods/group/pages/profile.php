<?php 
/*
 * 
 *  use group object and its properties
 * 
 *  `name`, `owner`, `short`, `detail`, `avatar`, `group`, `city_id`
 *  
 *  `privacy`= 1 - public, 2- closed, 3 - secret
 *  
 *  `membership` - 0 not member 1 - member 2 - admin
 *  
 *  `invited` - boolean, only set if not a member and privacy > 1
 *  
 *  get_city() - returns string of city, state or ""
 *  
 *   get_member_count() -- see tin
 *   
 * 
 *   if( $group->membership > 0 ){
 *   
 *      leave button
 *      
 *    } elseif(  $group->privacy == 1 or $group->invited == true ){ 
 *    
 *      join button     
 *    
 *    } elseif( $group->privacy == 2 ) {
 *    
 *      request to join
 *    
 *    }
 *   
 * 
 */ echo 'profile.php' ;