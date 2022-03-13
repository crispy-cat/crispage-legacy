<?php
	/*
		Crispage - A lightweight CMS for developers
		core/users/UserManager.php - User manager class

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");

	require_once Config::APPROOT . "/core/users/User.php";
	require_once Config::APPROOT . "/core/users/UserGroup.php";
	require_once Config::APPROOT . "/core/users/UserPermissions.php";

	class UserManager {
		public function getUser(string $id = null) : ?User {
			if ($id == null) return null;
			global $app;

			$user = $app->database->readRow("users", $id);
			if (!$user) return null;

			$user = new User($user);
			return $user;
		}

		public function getUserGroup(string $id = null) : ?UserGroup {
			if ($id == null) return null;
			global $app;

			$group = $app->database->readRow("usergroups", $id);
			if (!$group) return null;

			$group = new UserGroup($group);
			return $group;
		}

		public function setUser(string $id, User $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("users", $id, array(
				"name" => $data->name,
				"email" => $data->email,
				"group" => $data->group,
				"created" => $data->created,
				"modified" => $data->modified,
				"loggedin" => $data->loggedin,
				"activated" => $data->activated
			));
			$app->events->trigger("users.user_set", $id);
		}

		public function setUserGroup(string $id, UserGroup $data) {
			if ($id == null) return;
			global $app;

			$app->database->writeRow("usergroups", $id, array(
				"name" => $data->name,
				"parent" => $data->parent,
				"permissions" => $data->permissions,
				"created" => $data->created,
				"modified" => $data->modified
			));
			$app->events->trigger("user.group_set", $id);
		}

		public function deleteUser(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("users", $id);
			$app->events->trigger("users.user_delete", $id);
		}

		public function deleteUserGroup(string $id) {
			if ($id == null) return;
			global $app;

			$app->database->deleteRow("usergroups", $id);
			$app->events->trigger("users.usergroup_delete", $id);
		}

		public function existsUser(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("users", $id);
		}

		public function existsUserGroup(string $id) : bool {
			if ($id == null) return false;
			global $app;
			return $app->database->existsRow("usergroups", $id);
		}

		public function getUsers(string $group = null) : array {
			global $app;

			if ($group) $dbusers = $app->database->readRows("users", array("group" => $group));
			else $dbusers = $app->database->readRows("users");

			$users = array();

			foreach ($dbusers as $user)
				array_push($users, $this->getUser($user["id"]));

			return $users;
		}

		public function getUserGroups() : array {
			global $app;

			$dbgroups = $app->database->readRows("usergroups");

			$groups = array();

			foreach ($dbgroups as $group)
				array_push($groups, $this->getUserGroup($group["id"]));

			return $groups;
		}

		public function userGroupParentLoop(string $id = null) : bool {
			if ($id == null) return false;

			$names = array();

			$group = $this->getUserGroup($id);
			if (!$group) return false;
			$parent = $group->parent;
			if ($parent == null) return false;

			while ($parent !== null) {
				if (in_array($parent, $names)) return true;
				array_push($names, $parent);
				$parent = $this->getUserGroup($parent);
				if ($parent) $parent = $parent->parent;
			}

			return false;
		}

		public function getGroupPermissions(string $id = null) : int {
			if ($id == null) return 0;
			$group = $this->getUserGroup($id);
			if (!$group) return 0;
			if ($group->parent) return $group->permissions | $this->getGroupPermissions($group->parent);
			else return $group->permissions;
		}

		public function userHasPermissions(string $id = null, int $perm = 0) : bool {
			if ($id == null) return false;
			$user = $this->getUser($id);
			return ($this->getGroupPermissions($user->group) & $perm) == $perm;
		}
	}
?>
