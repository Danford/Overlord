<?php


function get_my_groups(){
    
    global $db ;
    global $user ;
    
    $ownedgroups = array() ;

    $db->query( "SELECT `id`, `name`, `short`, `avatar` FROM `group` WHERE `owner`='".$user->id."'" ) ;
    
    while( ( $g = $db->assoc() ) != false ){
        $ownedgroups[] = $g ; 
    }
 
    $admingroups = array() ;
    $membergroups = array() ;
    
    $db->query("SELECT `id`, `name`, `short`, `avatar`, `access` 
                                FROM `group_membership`, `group` 
                                WHERE `group_membership`.`user`='".$user->id."'
                                AND `group_membership`.`group` = `group`.`id`
                                AND `access` != 0
                                ORDER BY ACCESS DESC") ;
    
    while( ( $g = $db->assoc() ) != false ){
        if( $g['access'] == 1 ) { 
            unset( $g['access'] ) ;
            $membergroups[] = $g ;
        } elseif( $g['access'] == 2 ) {
            unset( $g['access'] ) ;
            $admingroups[] = $g ;
        }
    }
        
    return ['owned' => $ownedgroups, 'admin' => $admingroups , 'member' => $membergroups ]  ;
}

function GroupInteraction($user, $apiCall, $buttonText)
{
	$form = new form_minion($apiCall, 'invitations');
	$form->header();
	$form->hidden('users', $user->id);
	$form->submit_button($buttonText);
	$form->footer();
}

function GroupInviteResponse($group, $apiCall, $buttonText)
{
	$form = new form_minion($apiCall, 'invitations');
	$form->header();
	$form->hidden('group', $group->id);
	$form->submit_button($buttonText);
	$form->footer();
}

function PrintGroupInteractions($group, $person)
{
	global $user;
	if ($group->check_membership($user->id) == 0)
	{
		if ($person->is_in_group($group->id))
		{
			// remove from group
		}

		if ($group->is_invited($person->id))
		{
			echo GroupInteraction($person, "uninvite", "Uninvite");
		}
		else
		{
			echo GroupInteraction($person, "inviteUser", "Invite");
		}

		// block from group
		echo GroupInteraction($person, "block", "Block - Unimplemented");
	}
	else
		echo "Failed";
}

function PrintGroupInvitationActions($group) {
	echo GroupInviteResponse($group, "acceptInvite", "Accept");
	echo GroupInviteResponse($group, "delineInvite", "Decline");
}