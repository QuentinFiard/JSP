<?php

namespace nav;

use structures\User;

require_once 'classes/nav/PageTree.php';
require_once 'classes/utilities/Server.php';

use \nav\PageTree;
use \utilities\Server;

class Page {
	private $parent = null;
	private $children = array();
	private $title;
	private $name;
	
	public function __construct($name,$title="JSP - Site d'inscription aux évênements")
	{
		$this->name = $name;
		$this->title = $title;
	}
	
	public function isRoot()
	{
		return !isset($this->parent);
	}
	
	public function isLeaf()
	{
		return false;
	}

	public function getPathComponents()
	{
		$res = null;
		if($this->isRoot())
		{
			$res = array();
		}
		else
		{
			$res = $this->parent->getPathComponents();
		}
		$res[] = $this->name;
	
		return $res;
	}
	
	public function getPath()
	{
		if($this->isRoot())
		{
			return $this->name;
		}
		if($this->parent->isRoot())
		{
			return '/'.$this->name;
		}
		return $this->parent->getPath().'/'.$this->name;
	}
	
	public function HTMLHierarchyView($depth, $selected)
	{
		$res = '';
	
		switch(depth){
			case 0:
				{
					foreach($this->children as $page)
					{
						$res .= $page->HTMLHierarchyView($depth+1, $selected);
					}
					break;
				}
			case 1:
				{
					$res .= "<h3>";
					$res .= $this->title;
					$res .= "</h3>";
					$res .= "<ul class=\"toggle\" style=\"\">";
					foreach($this->children as $page)
					{
						$res .= $page->HTMLHierarchyView($depth+1, $selected);
					}
					$res .= "</ul>";
					break;
				}
		}
	
		return $res;
	}
	
	public function childWithName($name)
	{
		foreach($this->children as $page)
		{
			if($page->getName() == $name)
			{
				return $page;
			}
		}
		return null;
	}
	
	protected function getPageContentPath()
	{
		$path = $this->getPath();
		if($path == "/")
		{
			$path = "/home";
		}
		$path = "pages".$path.'.php';
		return $path;
	}
	
	protected function getPageStylePath()
	{
		$path = $this->getPath();
		if($path == "/")
		{
			$path = "/home";
		}
		$path = "css".$path.'.css';
		return $path;
	}
	
	protected function getPageScriptPath()
	{
		$path = $this->getPath();
		if($path == "/")
		{
			$path = "/home";
		}
		$path = "js".$path.'.js';
		return $path;
	}
	
	public function includePageContent($isAjax=false)
	{
		if(!$isAjax && file_exists($this->getPageScriptPath()))
		{
			echo '<script type="text/javascript" src="'.$this->getPageScriptPath().'"></script>';
		}
		include $this->getPageContentPath();
	}
	
	public function includeContent()
	{
		global $page;
		$page = $this;
		include "template/head.php";
		$this->includePageContent();
		include "template/foot.php";
	}
	
	public function handleAjaxRequest()
	{
		$res = array();
		
		ob_start();
		$this->includePageContent(true);
		$content = ob_get_clean();
		
		$res['content'] = $content;

		if(file_exists($this->getPageStylePath()))
		{
			$res['css'] = Server::getServerRoot().$this->getPageStylePath();
		}
		if(file_exists($this->getPageScriptPath()))
		{
			$res['js'] = Server::getServerRoot().$this->getPageScriptPath();
		}
		
		if(isset($_GET['lastPath']))
		{
			$tree = PageTree::getTree();
			$lastPage = $tree->pageWithPath($_GET['lastPath']);
			
			if($this->isDescendantOf($lastPage))
			{
				$res['transition'] = "insertRightAndAnimate";
			}
			else if($this->isSiblingOf($lastPage))
			{
				$res['transition'] = "fadeOutFadeIn";
			}
			else if($lastPage->isDescendantOf($this))
			{
				$res['transition'] = "insertLeftAndAnimate";
			}
		}
		
		$res['title'] = $this->getTitle();
		$res['path'] = $this->getPath();
		
		return $res;
	}
	
	public function checkSecurityGrant()
	{
		// To overrride in child classes to handle admin only access
	}
	
	public function addChild($child)
	{
		if($this->children == null)
		{
			$this->children = array();
		}
		$this->children[] = $child;
		$child->setParent($this);
	}
	
	public function isDescendantOfOrEqual($page)
	{
		if($this->isRoot())
		{
			return $this == $page;
		}
		return ($this==$page) || $this->parent->isDescendantOfOrEqual($page);
	}
	
	public function isDescendantOf($page)
	{
		if($this->isRoot())
		{
			return false;
		}
		return ($this!=$page) && $this->isDescendantOfOrEqual($page);
	}
	
	public function isSiblingOfOrEqual($page)
	{
		if($this->isRoot())
		{
			return $this == $page;
		}
		if($page->isRoot())
		{
			return true;
		}
		return $this==$page || $this->parent->isSiblingOfOrEqual($page->parent);
		
	}
	
	public function isSiblingOf($page)
	{
		if($this->isRoot())
		{
			return false;
		}
		return $this!=$page && $this->isSiblingOfOrEqual($page);
	}
	
	public function getParent() {
		return $this->parent;
	}

	public function getChildren() {
		return $this->children;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getName() {
		return $this->name;
	}

	public function setParent($parent) {
		$this->parent = $parent;
	}

	public function setChildren($children) {
		$this->children = $children;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setName($name) {
		$this->name = $name;
	}

	
	
}

?>