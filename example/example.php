<?php

include('../vendor/autoload.php');

$currentFolder = realpath(dirname(__FILE__)."/../");

$fileWrapper = new \DanielJHarvey\FileWrapper\FileWrapper;

$gitBranch = new \DanielJHarvey\PHPGitBranch\GitBranch($currentFolder,$fileWrapper);

$branch = $gitBranch->branch;

echo "This repository is currently working in \033[0;34m[".$branch."]\033[0;39m branch\n";