<?php

namespace DanielJHarvey\PHPGitBranch;

class GitBranch {
	
	protected $homeFolderPath;
	protected $fileWrapper;

	protected $branch = false;

	function __construct($homeFolderPath, \DanielJHarvey\FileWrapper\FileWrapper $fileWrapper) {
		$this->homeFolderPath = $homeFolderPath;
		$this->fileWrapper = $fileWrapper;
	}

	function __get($name) {
		return $this->getBranch();
	}

	protected function getBranch() {
		if ($this->branch) return $this->branch;
		$this->branch = $this->calculateBranch($this->homeFolderPath);
		return $this->branch;
	}

	public function calculateBranch($homeFolderPath) {
		$gitHeadFilePath = $this->getGitHeadFilePath($homeFolderPath);
		if (!$gitHeadFilePath) return false;
		return $this->cleanName(
			$this->parseFileContents(
				$this->readFile($gitHeadFilePath)
			)
		);
	}

	public function cleanName($branchName) {
		if (!$branchName) return false;
		return preg_replace('/[^\w-]/', '', $branchName);
	}

	protected function readFile($gitHeadFilePath) {
		return $this->fileWrapper->fileGetContents($gitHeadFilePath);
	}

	public function parseFileContents($fileContents) {
		$parts = explode("/", $fileContents, 3); //seperate out by the "/" in the string
		if (count($parts) < 3) return false;
    	return $parts[2]; //get the one that is always the branch name
	}

	public function getGitHeadFilePath($homeFolderPath) {
		$gitFolderPath = $this->getGitFolderPath($homeFolderPath);
		if (!$gitFolderPath) return false;
		$gitHeadFilePath = $gitFolderPath."HEAD";
		if ($this->fileWrapper->fileExists($gitHeadFilePath)) {
			return $gitHeadFilePath;
		}
		return false;
	}

	protected function getGitFolderPath($homeFolderPath) {
		$cleanPath = rtrim($homeFolderPath,"/");
		$gitFolderPath = $cleanPath."/.git/";
		if ($this->fileWrapper->fileExists($gitFolderPath)) {
			return $gitFolderPath;
		}
		return false;
	}

}
