<?php

include_once ('../src/GitBranch.php');
include_once ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class GitBranchTest extends TestCase {
	
	protected $fileWrapper;

	function setUp() {
		$this->fileWrapper = $this->getMockBuilder('\DanielJHarvey\FileWrapper\FileWrapper')
			->getMock();
	}

	public function testCreate() {
		$homeFolder = "/plop/slop/dock";

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$this->assertInstanceOf(
			'\DanielJHarvey\PHPGitBranch\GitBranch',
			$gitBranch,
			"Did not create correct type of object"
		);
	}

	public function testGetGitFolderPath() {
		$homeFolder = "plop/slop/dock/";
		$expected = "plop/slop/dock/.git/HEAD";

		$this->fileWrapper->expects($this->exactly(2))
			->method('fileExists')
			->will($this->returnValue(true));

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->getGitHeadFilePath($homeFolder);

		$this->assertEquals(
			$expected,
			$return,
			"Did not make path properly"
		);
	}

	public function testGetGitFolderPathTwo() {
		$homeFolder = "plop/slop/dock";
		$expected = "plop/slop/dock/.git/HEAD";

		$this->fileWrapper->expects($this->exactly(2))
			->method('fileExists')
			->will($this->returnValue(true));

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->getGitHeadFilePath($homeFolder);

		$this->assertEquals(
			$expected,
			$return,
			"Did not make second path properly"
		);
	}

	public function testFolderNotFound() {
		$homeFolder = "plop/slop/dock";
		$expected = "plop/slop/dock/.git/HEAD";

		$this->fileWrapper->expects($this->once())
			->method('fileExists')
			->will($this->returnValue(false));

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->getGitHeadFilePath($homeFolder);

		$this->assertFalse(
			$return,
			"No files means no git"
		);
	}

	public function testParseFileContents() {
		$homeFolder = "plop";

		$fileContents = "ref: refs/heads/fixes";

		$expected = 'fixes';

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->parseFileContents($fileContents);

		$this->assertEquals(
			$expected,
			$return,
			"Could not get branch from HEAD file"
		);
	}

	public function testParseFileContentsWrong() {
		$homeFolder = "plop";
		
		$fileContents = "heads/fixes";

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->parseFileContents($fileContents);

		$this->assertFalse(
			$return,
			"Did not identify shitty file"
		);
	}

	public function testCalculateBranchBrokenFileRead() {
		
		$this->fileWrapper->expects($this->exactly(2))
			->method('fileExists')
			->will($this->returnValue(true));

		$this->fileWrapper->expects($this->once())
			->method('fileGetContents')
			->will($this->returnValue(false));

		$homeFolder = "plop";

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->calculateBranch($homeFolder);

		$this->assertFalse(
			$return,
			"Did not identify unread stupid file"
		);
	}

	public function testCalculateBranchGreatJob() {
		
		$this->fileWrapper->expects($this->exactly(2))
			->method('fileExists')
			->will($this->returnValue(true));

		$fileContents = "ref: refs/heads/fixes";

		$this->fileWrapper->expects($this->once())
			->method('fileGetContents')
			->will($this->returnValue($fileContents));

		$homeFolder = "plop";

		$expected = 'fixes';

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->calculateBranch($homeFolder);

		$this->assertEquals(
			$expected,
			$return,
			"Did not find git branch"
		);
	}

	public function testCleanName() {
		$grubbyName = "master
";
		$expected = "master";

		$homeFolder = "plop";

		$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($homeFolder, $this->fileWrapper);

		$return = $gitBranch->cleanName($grubbyName);

		$this->assertEquals(
			$expected,
			$return,
			"Could not clean rubbish from name"
		);
	}

}