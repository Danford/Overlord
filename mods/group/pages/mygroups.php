<?php

    /*
     * 
     *   $ownergroups  "SELECT `id`, `name`, `short`, `avatar` FROM `group` WHERE `owner`='".$user->id."'"  ;
     * 
     *   $membergroups "SELECT `id`, `name`, `short`, `avatar`, `access` 
     *                          FROM `group_membership`, `group` 
     *                          WHERE `group_membership`.`user`='".$user->id."'
     *                          AND `group_membership`.`group` = '".$group->id."'
     *                          AND `group_membership`.`group` = `group`.`id`
     *                          AND `access` != 0
     *                          ORDER BY ACCESS DESC" ;
     *                          
     *                          
     *                              
     *  use array $groups, which contains three arrays: owned, admin, and member
     *  
     *      each of these is a list of groups ( `id`, `name`, `short`, `avatar` ) where user is
     *      the owner, an admin, or just a member
     *      
     *      
     *      
     *  You can also get this from the api (getMyGroups), which will return the three arrays (owned, admin, member)
     *  though if you do, remove the call to get_my_groups in module.php.
     *  
     *  
     * 
     */

echo 'mygroups.php' ;

?>

<pre><?php print_r($groups); ?></pre>