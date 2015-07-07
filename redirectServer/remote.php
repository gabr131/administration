<?php

$dispatcher = new OC_Central();
$dispatcher->redirect();

class OC_Central {
	private static $authUser;
	private static $origPath;

	function __construct() {
		$this->authUser = $_SERVER['PHP_AUTH_USER'];
		$this->domain = $_SERVER['SERVER_NAME'];
		$this->origPath = $_SERVER['ORIG_PATH_INFO'];
	}

	public function redirect() {
		$this->checkRequest();

		$baseDomain = "https://" . $this->domain . "/";
		$url = $baseDomain . $this->parseUser() . $this->parsePath();

		header('Location: ' . $url, true, 301);
		exit;
	}

	private function checkRequest() {
		if (!isset($this->authUser)) {
			$this->errorUnauthorized('Error: Please provide a valid username and password');
			exit;
		}

		if (!filter_var($this->authUser, FILTER_VALIDATE_EMAIL)) {
			$this->errorUnauthorized('Error: Username has no valid email format: ' . $this->authUser);
			exit;
		}
	}

	private function parsePath() {
		return $this->origPath;
	}

	private function parseUser() {
		$mail = explode("@", $this->authUser);
		$domain = $mail[1];
		$domainParts = explode(".", $domain);
		array_pop($domainParts);
		return $domainParts[1];
	}

	private function errorUnauthorized($message) {
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		echo $message;
	}
}