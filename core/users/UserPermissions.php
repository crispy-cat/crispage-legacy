<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/UserPermissions.php - User permissions

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	class UserPermissions {
		public const NO_PERMISSIONS			= 0x0000000000000000;

		public const LOGIN					= 0x0000000000000001;
		public const LOGIN_BACKEND			= 0x0000000000000002;
		public const MODIFY_SELF			= 0x0000000000000004;
		public const MODIFY_USERS			= 0x0000000000000008;
		public const APPROVE_USERS			= 0x0000000000000010;
		public const BAN_USERS				= 0x0000000000000020;
		public const MODIFY_USERGROUPS		= 0x0000000000000040;

		public const MODIFY_ARTICLES_OWN	= 0x0000000000000100;
		public const MODIFY_ARTICLES		= 0x0000000000000200;
		public const MODIFY_CATEGORIES		= 0x0000000000000400;

		public const VIEW_COMMENTS			= 0x0000000000001000;
		public const POST_COMMENTS			= 0x0000000000002000;
		public const MODIFY_COMMENTS_OWN 	= 0x0000000000004000;
		public const MODIFY_COMMENTS		= 0x0000000000008000;

		public const MODIFY_MEDIA			= 0x0000000000010000;

		public const MODIFY_MENUS			= 0x0000000000100000;
		public const MODIFY_MODULES			= 0x0000000000200000;
		public const MODIFY_SETTINGS		= 0x0000000000400000;
		public const USE_INSTALLER			= 0x0000000000800000;

		public const ALL_PERMISSIONS		= 0x0000000000f1f77f;
	}
