<?php

require_once("lib/AuthProvider.php");
require_once("lib/ldap.php");

#
# This class provides support for authenticating against a SR LDAP instance.

class LDAPAuthProvider {

	private $host;
	private $manager;
	private static $groupsOfInterest = array('team-*', 'ide-admin', 'mentors');

	/**
	 * Create the provider.
	 * The user details here are the ones that the provider will use
	 * for looking up information about the user.
	 */
	public function __construct($host, $user, $pass){
		$this->host = $host;
		$this->manager = new LDAPManager($host, $user, $pass);
	}

	public function CheckCredentials($username, $password){
		$tmpManager = new LDAPManager($this->host, $username, $password);
		$authed = $tmpManager->getAuthed();
		return $authed;
	}

	public function GetDisplayName($username){
		if ($this->manager->getAuthed()){
			$info = $this->manager->getUserInfo($username);
			return $info['name.first'] . ' ' . $info['name.last'];
		}
		return null;
	}

	public function GetGroups($username){
		if ($this->manager->getAuthed()){
			$allGroups = array();
			foreach (self::$groupsOfInterest as $group) {
				$groups = $this->manager->getGroupsForUser($username, $group);
				$allGroups = array_merge($allGroups, $groups);
			}
			return $allGroups;
		}
		return null;
	}
}
