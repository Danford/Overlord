<?php
require_once (oe_frontend . 'html/element.php');

require_once (oe_frontend . 'html/tags/html.php');
require_once (oe_frontend . 'html/tags/head.php');
require_once (oe_frontend . 'html/tags/body.php');
require_once (oe_frontend . 'html/tags/header.php');
require_once (oe_frontend . 'html/tags/content.php');
require_once (oe_frontend . 'html/tags/footer.php');

require_once (oe_frontend . 'html/modules/menu.php');
require_once (oe_frontend . 'html/modules/menu_item.php');

class html_minion extends Element
{

    /*
     * html_minion( $title[, $externalcss, $externaljs, $doctype ] ) {
     *
     * $externalcss and $externaljs can be single values, arrays, or comma delimited strings
     *
     */
    public $html;
    public $head;
    public $body;
    public $header;
    public $menu;
    public $section;
    public $content;
    public $footer;

    function __construct($title)
    {
        global $user;
        $sitename = "Overlord";
        parent::__construct(array());
        
        $this->html = $this->AddElement(new Html());
        $this->head = $this->html->AddElement(new Head($sitename . " - " . $title));
        $this->body = $this->html->AddElement(new Body());
        $this->header = $this->body->AddElement(new Header($sitename));
        $this->menu = $this->header->AddElement(new Menu());
        $contentWrapper = $this->body->AddElement(new Div("content-wrapper"));
        $this->content = $contentWrapper->AddElement(new Content($title));
        $this->footer = $this->body->AddElement(new Footer($sitename));
                
        //$this->content->AddContent("<pre>". print_r($user->$id, true) ."</pre>");
        /*
         * /profile - profile editor
         * /profile/upload_image
         * /profile/block_list
         * /profile/write
         *
         * /profile/editphoto/{photoid}
         * /profile/editwriting/{proseid}
         * /profile/editalbum/{proseid} !!!!!!!!!!!!!!!!!!NOT IMPLEMENTED SOMEHOW
         *
         * /profile/{userid}
         * /profile/{userid}/photos
         * /profile/{userid}/albums
         * /profile/{userid}/writing
         *
         * /profile/{userid}/writing/{proseid}
         * /profile/{userid}/photo/{photoid}
         */
        
        $this->menu->AddMenuItem(new MenuItem("Home", "/"));
        
        if ($user->is_logged_in()) {
	        global $profile;
	        
	        $pMenu = $this->menu->AddMenuList(new MenuItem("Profile", "/profile/". $user->id));
	        $pMenu->AddElement(new MenuItem("Edit", "/profile"));
	        $pMenu->AddElement(new MenuItem("Write", "/profile/write"));
	        $pMenu->AddElement(new MenuItem("Upload Photo", "/profile/upload_photo"));
	        $pMenu->AddElement(new MenuItem("Block List", "/profile/block_list"));
	        
	        /*
	         * Group profiles:
	         * owner only
	         * * moderator or owner
	         *
	         *
	         * /group or /groups - main page of groups section
	         * /group/create
	         *
	         * /group/{groupid}
	         * /group/{groupid}/edit
	         * * /group/{groupid}/banned
	         * * /group/{groupid}/invite_moderation
	         * * /group/{groupid}/request_moderation
	         *
	         *
	         * /group/{groupid}/newthread
	         * /group/{groupid}/threads
	         * /group/{groupid}/thread/{threadid}
	         *
	         * /group/{groupid}/notifications
	         * /group/{groupid}/members
	         * /group/{groupid}/invite
	         */
	        
	        $gMenu = $this->menu->AddMenuList(new MenuItem("Groups", "/group"));
	        $gMenu->AddElement(new MenuItem("Create Group", "/group/create"));
	        
	        foreach ($user->groups_in as $group)
	        {
	        	$gMenu->AddElement(new MenuItem($group, "/group/". $group));
	        }
	        
	        $this->menu->AddMenuItem(new MenuItem("Logout", "/logout/"));
        } else { // user is not logged in.
        	$this->menu->AddMenuItem(new MenuItem("Login", "/login/"));
        	$this->menu->AddMenuItem(new MenuItem("Register", "/register/"));
        }
    }
}

?>