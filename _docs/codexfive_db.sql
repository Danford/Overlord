-- --------------------------------------------------------

--
-- Table structure for table `confirmation_key`
--

CREATE TABLE `confirmation_key` (
  `id` int(11) NOT NULL,
  `user_profile` int(11) NOT NULL,
  `confirmation_key` char(64) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0=activation,1=reset',
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_block`
--

CREATE TABLE `event_block` (
  `event_block_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `blocked_user` int(11) NOT NULL,
  `blocked_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_invite`
--

CREATE TABLE `event_invite` (
  `event_id` int(11) NOT NULL,
  `invitee` int(11) NOT NULL,
  `invited_by` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '	0 - invite from admin (requires user approval)1 - request from other group member (admin approves, becomes type 0) 2 - request from user (requires admin approval)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_profile`
--

CREATE TABLE `event_profile` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(100) NOT NULL,
  `start` char(19) NOT NULL,
  `end` char(19) DEFAULT NULL,
  `private` bit(1) NOT NULL COMMENT '0 - public\n1 - private',
  `organiser` int(11) NOT NULL,
  `group` char(26) DEFAULT NULL COMMENT 'If group is specified, will be visible in group profile, and members of the group will be able to see it regardless of privacy setting.',
  `location` int(11) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `cost` varchar(45) DEFAULT NULL,
  `dress` varchar(45) DEFAULT NULL,
  `detail` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_rsvp`
--

CREATE TABLE `event_rsvp` (
  `rsvp_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rsvp_status` tinyint(4) NOT NULL COMMENT '0 - decline\n1 - accept\n2 - maybe',
  `notifications` tinyint(4) NOT NULL COMMENT '0 - no notification\n1 - site notification\n2 - email notification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_block`
--

CREATE TABLE `group_block` (
  `group_id` int(11) NOT NULL,
  `blocked_user` int(11) NOT NULL,
  `blocked_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_invite`
--

CREATE TABLE `group_invite` (
  `invite_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invited_by` int(11) DEFAULT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 - invite from admin (requires user approval)1 -  request from other group member (admin approves, becomes type 0) 2 - request from user (requires admin approval)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `group_id` char(26) NOT NULL,
  `member_id` int(11) NOT NULL,
  `notify_thread` tinyint(1) NOT NULL DEFAULT '1',
  `notify_message` tinyint(1) NOT NULL DEFAULT '1',
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_message`
--

CREATE TABLE `group_message` (
  `message_id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_moderator`
--

CREATE TABLE `group_moderator` (
  `group_id` int(11) NOT NULL,
  `mod_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_profile`
--

CREATE TABLE `group_profile` (
  `group_id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '1 - public group, anyone can join\n2 - private group, listed but requires admin approval to join\n3 - secret group, invite-only',
  `short_desc` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `owner` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_thread`
--

CREATE TABLE `group_thread` (
  `thread_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sticky` int(1) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `latest_timestamp` char(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `group_thread_following`
--

CREATE TABLE `group_thread_following` (
  `follow_notification_id` char(26) NOT NULL,
  `thread_id` char(26) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notify_level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - do not notify\n1 - notify with site notifications\n2 - notify by email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `oe_pages`
--

CREATE TABLE `oe_pages` (
  `id` int(11) NOT NULL,
  `module` varchar(25) NOT NULL,
  `url_key` varchar(25) NOT NULL,
  `title` varchar(255) NOT NULL,
  `externalcss` varchar(255) NOT NULL,
  `externaljs` varchar(255) NOT NULL,
  `otherheaders` text NOT NULL,
  `bodycalls` varchar(255) NOT NULL,
  `bodycontent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `persistent_tokens`
--

CREATE TABLE `persistent_tokens` (
  `user_id` int(11) NOT NULL,
  `token` char(64) NOT NULL,
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_albums`
--

CREATE TABLE `profile_albums` (
  `album_id` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `title` varchar(75) NOT NULL,
  `description` text NOT NULL,
  `private_photo` int(11) NOT NULL,
  `private_prose` int(11) NOT NULL,
  `private_video` int(11) NOT NULL,
  `public_photo` int(11) NOT NULL,
  `public_prose` int(11) NOT NULL,
  `public_video` int(11) NOT NULL,
  `last_updated` char(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_block`
--

CREATE TABLE `profile_block` (
  `blocker` int(11) NOT NULL,
  `blockee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_friendship`
--

CREATE TABLE `profile_friendship` (
  `friend1` int(11) NOT NULL,
  `friend2` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_friendship_rq`
--

CREATE TABLE `profile_friendship_rq` (
  `friend_rq_id` char(19) NOT NULL,
  `requestor` int(11) NOT NULL,
  `requestee` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_photo`
--

CREATE TABLE `profile_photo` (
  `photo_id` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `album` int(11) DEFAULT NULL,
  `title` varchar(75) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestamp` char(19) NOT NULL,
  `file_key` char(36) DEFAULT NULL,
  `comments` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_photo_comment`
--

CREATE TABLE `profile_photo_comment` (
  `comment_id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_photo_like`
--

CREATE TABLE `profile_photo_like` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `liked_by` int(11) DEFAULT NULL,
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_prose`
--

CREATE TABLE `profile_prose` (
  `prose_id` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `private` tinyint(4) NOT NULL,
  `album` int(11) DEFAULT NULL,
  `title` varchar(75) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `timestamp` char(19) NOT NULL,
  `comments` int(11) NOT NULL,
  `likes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_prose_comment`
--

CREATE TABLE `profile_prose_comment` (
  `comment_id` int(11) NOT NULL,
  `prose_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_prose_like`
--

CREATE TABLE `profile_prose_like` (
  `id` int(11) NOT NULL,
  `prose_id` int(11) NOT NULL,
  `liked_by` int(11) DEFAULT NULL,
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `profile_relationship`
--

CREATE TABLE `profile_relationship` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `related_to` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_relationship_rq`
--

CREATE TABLE `profile_relationship_rq` (
  `rel_req_id` int(11) NOT NULL,
  `requestor` int(11) NOT NULL,
  `requestee` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `profile_video`
--

CREATE TABLE `profile_video` (
  `video_id` char(26) NOT NULL,
  `owner` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `album` int(11) NOT NULL,
  `title` varchar(75) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestamp` char(19) NOT NULL,
  `file` varchar(100) NOT NULL,
  `comments` int(11) NOT NULL,
  `likes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `relationship_type`
--

CREATE TABLE `relationship_type` (
  `relationship_id` int(11) NOT NULL,
  `label` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0 - unverified1 - active2 - user suspended3 - admin suspended',
  `passhash` char(64) NOT NULL,
  `date_verified` char(19) DEFAULT NULL,
  `ip_verified` varchar(15) DEFAULT NULL,
  `email` varchar(75) DEFAULT NULL,
  `last_login` char(19) NOT NULL,
  `login_fails` tinyint(4) NOT NULL DEFAULT '0',
  `show_age` tinyint(1) NOT NULL,
  `allow_contact` tinyint(1) NOT NULL,
  `email_notification` tinyint(1) NOT NULL COMMENT 'receive emails of new direct messages',
  `invite_notification` tinyint(1) NOT NULL COMMENT 'get emails for event invites',
  `last_site_notification` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `item` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `ref` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` char(19) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT 'friend request, group invite, etc.',
  `ref` int(11) NOT NULL COMMENT 'the id of the event being notified',
  `read` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `user_id` int(11) NOT NULL,
  `birthdate` char(10) NOT NULL,
  `screen_name` varchar(45) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `detail` text,
  `avatar` int(11) DEFAULT NULL,
  `friend_count` int(11) NOT NULL DEFAULT '0',
  `total_public_photo` int(11) NOT NULL,
  `total_public_prose` int(11) NOT NULL,
  `total_public_video` int(11) NOT NULL,
  `total_public_albums` int(11) NOT NULL,
  `total_private_photo` int(11) NOT NULL,
  `total_private_prose` int(11) NOT NULL,
  `total_private_video` int(11) NOT NULL,
  `total_private_albums` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `confirmation_key`
--
ALTER TABLE `confirmation_key`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profile` (`user_profile`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `type` (`type`),
  ADD KEY `confirmation_key` (`confirmation_key`);

--
-- Indexes for table `event_block`
--
ALTER TABLE `event_block`
  ADD KEY `group_blocked_user_key_idx` (`blocked_user`),
  ADD KEY `group_blocked_by_ket_idx` (`blocked_by`),
  ADD KEY `group_block_key_idx` (`event_id`),
  ADD KEY `event_block_key_idx` (`event_block_id`);

--
-- Indexes for table `event_invite`
--
ALTER TABLE `event_invite`
  ADD KEY `invite_event_key_idx` (`event_id`),
  ADD KEY `event_invitee_key_idx` (`invitee`),
  ADD KEY `event_invited_by_key_idx` (`invited_by`);

--
-- Indexes for table `event_profile`
--
ALTER TABLE `event_profile`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organiser_key_idx` (`organiser`),
  ADD KEY `event_location_key_idx` (`location`),
  ADD KEY `event_group_key_idx` (`group`);

--
-- Indexes for table `event_rsvp`
--
ALTER TABLE `event_rsvp`
  ADD PRIMARY KEY (`rsvp_id`),
  ADD KEY `rsvp_event_key_idx` (`event_id`),
  ADD KEY `rsvp_user_key_idx` (`user_id`);

--
-- Indexes for table `group_block`
--
ALTER TABLE `group_block`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `group_invite`
--
ALTER TABLE `group_invite`
  ADD PRIMARY KEY (`invite_id`);

--
-- Indexes for table `group_message`
--
ALTER TABLE `group_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `group_msg_thread_key_idx` (`thread_id`),
  ADD KEY `group_msg_user_key_idx` (`user_id`);

--
-- Indexes for table `group_profile`
--
ALTER TABLE `group_profile`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `group_name_UNIQUE` (`name`),
  ADD KEY `group_owner_key_idx` (`owner`);

--
-- Indexes for table `group_thread`
--
ALTER TABLE `group_thread`
  ADD PRIMARY KEY (`thread_id`),
  ADD KEY `thread_user_key_idx` (`user_id`),
  ADD KEY `thread_group_key_idx` (`group_id`);

--
-- Indexes for table `group_thread_following`
--
ALTER TABLE `group_thread_following`
  ADD PRIMARY KEY (`follow_notification_id`),
  ADD KEY `followed_thread_key_idx` (`thread_id`),
  ADD KEY `thread_follower_key_idx` (`user_id`);

--
-- Indexes for table `oe_pages`
--
ALTER TABLE `oe_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module` (`module`),
  ADD KEY `url_key` (`url_key`);

--
-- Indexes for table `profile_albums`
--
ALTER TABLE `profile_albums`
  ADD PRIMARY KEY (`album_id`),
  ADD KEY `user_id` (`owner`);

--
-- Indexes for table `profile_block`
--
ALTER TABLE `profile_block`
  ADD KEY `blocker_user_key_idx` (`blocker`),
  ADD KEY `blockee_user_key_idx` (`blockee`);

--
-- Indexes for table `profile_friendship`
--
ALTER TABLE `profile_friendship`
  ADD KEY `friend1_profile_key_idx` (`friend1`),
  ADD KEY `friend2_profile_key_idx` (`friend2`);

--
-- Indexes for table `profile_friendship_rq`
--
ALTER TABLE `profile_friendship_rq`
  ADD PRIMARY KEY (`friend_rq_id`),
  ADD KEY `friend_requestee_user_key_idx` (`requestee`),
  ADD KEY `friend_requestor_user_key_idx` (`requestor`);

--
-- Indexes for table `profile_photo`
--
ALTER TABLE `profile_photo`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `owner` (`owner`),
  ADD KEY `album` (`album`);

--
-- Indexes for table `profile_photo_comment`
--
ALTER TABLE `profile_photo_comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `photo_id` (`photo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profile_photo_like`
--
ALTER TABLE `profile_photo_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`photo_id`),
  ADD KEY `liked_by` (`liked_by`);

--
-- Indexes for table `profile_prose`
--
ALTER TABLE `profile_prose`
  ADD PRIMARY KEY (`prose_id`);

--
-- Indexes for table `profile_prose_comment`
--
ALTER TABLE `profile_prose_comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `photo_id` (`prose_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `profile_prose_like`
--
ALTER TABLE `profile_prose_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`prose_id`),
  ADD KEY `liked_by` (`liked_by`);
  
--
-- Indexes for table `profile_relationship`
--
ALTER TABLE `profile_relationship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile_relationship_rq`
--
ALTER TABLE `profile_relationship_rq`
  ADD PRIMARY KEY (`rel_req_id`);

--
-- Indexes for table `relationship_type`
--
ALTER TABLE `relationship_type`
  ADD PRIMARY KEY (`relationship_id`);
  
--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`item`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_user_key_idx` (`user_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `screen_name` (`screen_name`),
  ADD KEY `screen_name_2` (`screen_name`);
  
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `confirmation_key`
--
ALTER TABLE `confirmation_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `event_block`
--
ALTER TABLE `event_block`
  MODIFY `event_block_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `event_invite`
--
ALTER TABLE `event_invite`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `event_profile`
--
ALTER TABLE `event_profile`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `event_rsvp`
--
ALTER TABLE `event_rsvp`
  MODIFY `rsvp_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_block`
--
ALTER TABLE `group_block`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_invite`
--
ALTER TABLE `group_invite`
  MODIFY `invite_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_message`
--
ALTER TABLE `group_message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_profile`
--
ALTER TABLE `group_profile`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `group_thread`
--
ALTER TABLE `group_thread`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `oe_pages`
--
ALTER TABLE `oe_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_albums`
--
ALTER TABLE `profile_albums`
  MODIFY `album_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_photo`
--
ALTER TABLE `profile_photo`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_photo_comment`
--
ALTER TABLE `profile_photo_comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_photo_like`
--
ALTER TABLE `profile_photo_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_prose`
--
ALTER TABLE `profile_prose`
  MODIFY `prose_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_prose_comment`
--
ALTER TABLE `profile_prose_comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_prose_like`
--
ALTER TABLE `profile_prose_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_relationship`
--
ALTER TABLE `profile_relationship`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `profile_relationship_rq`
--
ALTER TABLE `profile_relationship_rq`
MODIFY `rel_req_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `relationship_type`
--
ALTER TABLE `relationship_type`
MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `item` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;