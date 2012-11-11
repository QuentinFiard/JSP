<?php

require_once('classes/nav/Page.php');
require_once('classes/nav/PageTree.php');
require_once('classes/utilities/RequestInformation.php');
require_once('classes/structures/User.php');

use \process;
use \nav\PageTree;
use \utilities\RequestInformation;
use \structures\User;

// Opening requested page

$pagePath = $_GET['page'];
if(!isset($pagePath) || $pagePath == "")
{
	$pagePath = "/";
}

$tree = PageTree::getTree();
global $currentPage;
$currentPage = $tree->pageWithPath($pagePath);

if(!isset($currentPage))
{
	header('HTTP/1.0 404 Not Found');
	echo "<h1>404 Not Found</h1>";
	echo "The page that you have requested could not be found.";
	exit();
}

global $user;
$user = User::currentUser();

$currentPage->checkSecurityGrant();
if(RequestInformation::isAjax())
{
	$response = $currentPage->handleAjaxRequest();
	echo json_encode($response);
}
else
{
	$currentPage->includeContent();
}