<?php
    /*
     *  
     *  You've got $invitables
     *  
     *  $invitables['people'] = array of profile objects
     *  $invitables['group'] = array of group objects
     * 
     *  
     * 
     * 
     * 
     * 
     */

include(oe_lib."form_minion.php");
include(oe_frontend."page_minion.php");

include(oe_isotope."isotope.php");
include(oe_isotope."invitable_tile.php");

$page = new page_minion($group->name . " - Group Members");

$page->header();

$memberGroups = $group->get_members();

$isotope = new Isotope($page);
foreach ($invitables['people'] as $invitable)
	$isotope->AddTile(new InvitableTileGroup($invitable, $group));

$page->footer();