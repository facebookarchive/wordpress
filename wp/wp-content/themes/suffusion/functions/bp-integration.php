<?php
/**
 * Included for BuddyPress themes.
 *
 * @since 3.8.3
 * @package Suffusion
 * @subpackage Functions
 */

// This is not a BP child theme, but in case it is used with the Suffusion BP support pack, this inclusion is needed.
if (function_exists('bp_is_group') && file_exists(PLUGINDIR . "/buddypress/bp-themes/bp-default/_inc/ajax.php")) {
	include_once (PLUGINDIR . "/buddypress/bp-themes/bp-default/_inc/ajax.php");
}

if (!function_exists('suffusion_bp_content_class')) {
	/**
	 * Similar to the post_class() function, but for BP. This is NOT used by core Suffusion, but is useful for child themes using BP.
	 * This might be defined by the Suffusion BuddyPress Pack for BP users of Suffusion, but is included conditionally here so
	 * that the theme and the plugin can be used independently of each other and so that one version of Suffusion can work with an older
	 * version of the BP pack.
	 *
	 * @since 3.6.7
	 * @param bool $custom
	 * @param bool $echo
	 * @return bool|string
	 */
	function suffusion_bp_content_class($custom = false, $echo = true) {
		if (!function_exists('bp_is_group')) return false;

		$css = array();
		$css[] = 'post';
		if (function_exists('bp_is_profile_component') && bp_is_profile_component()) $css[] = 'profile-component';
		if (function_exists('bp_is_activity_component') && bp_is_activity_component()) $css[] = 'activity-component';
		if (function_exists('bp_is_blogs_component') && bp_is_blogs_component()) $css[] = 'blogs-component';
		if (function_exists('bp_is_messages_component') && bp_is_messages_component()) $css[] = 'messages-component';
		if (function_exists('bp_is_friends_component') && bp_is_friends_component()) $css[] = 'friends-component';
		if (function_exists('bp_is_groups_component') && bp_is_groups_component()) $css[] = 'groups-component';
		if (function_exists('bp_is_settings_component') && bp_is_settings_component()) $css[] = 'settings-component';
		if (function_exists('bp_is_member') && bp_is_member()) $css[] = 'member';
		if (function_exists('bp_is_user_activity') && bp_is_user_activity()) $css[] = 'user-activity';
		if (function_exists('bp_is_user_friends_activity') && bp_is_user_friends_activity()) $css[] = 'user-friends-activity';
		if (function_exists('bp_is_activity_permalink') && bp_is_activity_permalink()) $css[] = 'activity-permalink';
		if (function_exists('bp_is_user_profile') && bp_is_user_profile()) $css[] = 'user-profile';
		if (function_exists('bp_is_profile_edit') && bp_is_profile_edit()) $css[] = 'profile-edit';
		if (function_exists('bp_is_change_avatar') && bp_is_change_avatar()) $css[] = 'change-avatar';
		if (function_exists('bp_is_user_groups') && bp_is_user_groups()) $css[] = 'user-groups';
		if (function_exists('bp_is_group') && bp_is_group()) $css[] = 'group';
		if (function_exists('bp_is_group_home') && bp_is_group_home()) $css[] = 'group-home';
		if (function_exists('bp_is_group_create') && bp_is_group_create()) $css[] = 'group-create';
		if (function_exists('bp_is_group_admin_page') && bp_is_group_admin_page()) $css[] = 'group-admin-page';
		if (function_exists('bp_is_group_forum') && bp_is_group_forum()) $css[] = 'group-forum';
		if (function_exists('bp_is_group_activity') && bp_is_group_activity()) $css[] = 'group-activity';
		if (function_exists('bp_is_group_forum_topic') && bp_is_group_forum_topic()) $css[] = 'group-forum-topic';
		if (function_exists('bp_is_group_forum_topic_edit') && bp_is_group_forum_topic_edit()) $css[] = 'group-forum-topic-edit';
		if (function_exists('bp_is_group_members') && bp_is_group_members()) $css[] = 'group-members';
		if (function_exists('bp_is_group_invites') && bp_is_group_invites()) $css[] = 'group-invites';
		if (function_exists('bp_is_group_membership_request') && bp_is_group_membership_request()) $css[] = 'group-membership-request';
		if (function_exists('bp_is_group_leave') && bp_is_group_leave()) $css[] = 'group-leave';
		if (function_exists('bp_is_group_single') && bp_is_group_single()) $css[] = 'group-single';
		if (function_exists('bp_is_user_blogs') && bp_is_user_blogs()) $css[] = 'user-blogs';
		if (function_exists('bp_is_user_recent_posts') && bp_is_user_recent_posts()) $css[] = 'user-recent-posts';
		if (function_exists('bp_is_user_recent_commments') && bp_is_user_recent_commments()) $css[] = 'user-recent-commments';
		if (function_exists('bp_is_create_blog') && bp_is_create_blog()) $css[] = 'create-blog';
		if (function_exists('bp_is_user_friends') && bp_is_user_friends()) $css[] = 'user-friends';
		if (function_exists('bp_is_friend_requests') && bp_is_friend_requests()) $css[] = 'friend-requests';
		if (function_exists('bp_is_user_messages') && bp_is_user_messages()) $css[] = 'user-messages';
		if (function_exists('bp_is_messages_inbox') && bp_is_messages_inbox()) $css[] = 'messages-inbox';
		if (function_exists('bp_is_messages_sentbox') && bp_is_messages_sentbox()) $css[] = 'messages-sentbox';
		if (function_exists('bp_is_notices') && bp_is_notices()) $css[] = 'notices';
		if (function_exists('bp_is_messages_compose_screen') && bp_is_messages_compose_screen()) $css[] = 'messages-compose-screen';
		if (function_exists('bp_is_single_item') && bp_is_single_item()) $css[] = 'single-item';
		if (function_exists('bp_is_activation_page') && bp_is_activation_page()) $css[] = 'activation-page';
		if (function_exists('bp_is_register_page') && bp_is_register_page()) $css[] = 'register-page';
		$css[] = 'fix';

		if (is_array($custom)) {
			foreach ($custom as $class) {
				if (!in_array($class, $css)) $css[] = esc_attr($class);
			}
		}
		else if ($custom != false) {
			$css[] = $custom;
		}
		$css_class = implode(' ', $css);
		if ($echo) echo ' class="' . $css_class . '" ';
		return ' class="' . $css_class . '" ';
	}
}
