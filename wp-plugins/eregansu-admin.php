<?php

/*
Plugin Name: Eregansu administration
Author: Nexgenta
Author URI: http://github.com/nexgenta/admin
*/

require_once(ABSPATH . 'platform/platform.php');
require_once(PLATFORM_LIB . 'request.php');
require_once(PLATFORM_LIB . 'session.php');
require_once(PLATFORM_PATH . 'routable.php');
require_once(PLATFORM_PATH . 'template.php');
require_once(PLATFORM_PATH . 'error.php');

add_action('admin_menu', '_eregansu_wp_menu');

class EregansuWPRequest extends HTTPRequest
{
	public function __construct($uriArray)
	{
		parent::__construct();
		$this->objects = array();
		$this->params = $uriArray;
		$this->base = $this->self . '?page=';
		$this->absoluteBase = $this->httpBase;
		$this->absolutePage = $this->absoluteBase . 'admin.php?page=';
		$this->determineTypes();
	}
	
	public function redirect($uri, $status = 301, $useHTML = false)
	{
		die('attempt to redirect to ' . $uri);
	}
	
	public function header($name, $value, $replace = true)
	{
	}	
}

class EregansuWPRouter extends Router
{
	public function route()
	{
		global $hook_suffix, $ADMIN_ROUTES, $current_user;
		
		$topLevel = 'toplevel_page_';
		if(!strncmp($hook_suffix, $topLevel, strlen($topLevel)))
		{
			$x = array(substr($hook_suffix, strlen($topLevel)));
		}
		else
		{
			$x = explode('/', preg_replace('!^[a-z0-9-]+_page_!', '', $hook_suffix));
		}
		$req = new EregansuWPRequest($x);
		if(isset($ADMIN_ROUTES[$x[0]]))
		{
			$k = $x[0];
			$req->consume();
		}
		else
		{
			/* This should, in theory, never be reached */
			$k = '__DEFAULT__';
		}
		$route = $ADMIN_ROUTES[$k];
		$route['key'] = $k;
		$req->data = $route;
		$req->data['_routes'] = $ADMIN_ROUTES;
		$req->beginTransientSession();
		$req->session->user = array('perms' => $current_user->allcaps);
		$e = error_reporting(E_ALL);
		if(!($target = $this->routeInstance($req, $route))) return false;
		$target->process($req);
		error_reporting($e);
	}
}

function _eregansu_wp_menu()
{
	global $ADMIN_ROUTES, $menu, $admin_page_hooks, $_registered_pages, $submenu, $_eregansu_wp_router;
	
	require_once(CONFIG_ROOT . 'appconfig.php');
	
	if(!isset($ADMIN_ROUTES) || !count($ADMIN_ROUTES)) return;
	if(!isset($_eregansu_wp_router))
	{
		$_eregansu_wp_router = new EregansuWPRouter();
	}
	foreach($ADMIN_ROUTES as $k => $route)
	{
		$menuTitle = $menuPageTitle = ucwords($k);
		$customPosition = 0;
		$objectMenu = false;
		$menuIcon = null;
		$require = null;
		if(isset($route['title'])) $menuTitle = $menuPageTitle = $route['title'];
		if(isset($route['pageTitle'])) $menuPageTitle = $route['pageTitle'];
		if(!empty($route['customPosition'])) $customPosition = $route['customPosition'];
		if(!empty($route['objectMenu'])) $objectMenu = true;
		if(isset($route['icon'])) $menuIcon = $route['icon'];
		if(isset($route['require'])) $require = $route['require'];

		if($customPosition)
		{
			$file = plugin_basename($k);
			$admin_page_hooks[$file] = sanitize_title($menuTitle);
			$hookname = get_plugin_page_hookname($file, '');
			if(!empty($hookname))
			{
				add_action($hookname, '_eregansu_wp_showpage');
			}
			if(!strlen($icon_url))
			{
				$icon_url = 'images/generic.png';
			}
			else if ( is_ssl() && 0 === strpos($icon_url, 'http://') )
			{
				$icon_url = 'https://' . substr($icon_url, 7);
			}
			$menu[$customPosition] = array ($menuTitle, $require, $file, $menuPageTitle, 'menu-top ' . $hookname, $hookname, $icon_url);		
			$_registered_pages[$hookname] = true;
		}
		else if($objectMenu)
		{
			add_object_page($menuTitle, $menuPageTitle, $menuRequiresPerm, $k, array($_eregansu_wp_router, 'route'), $menuIcon);
		}
		else
		{
			add_menu_page($menuTitle, $menuPageTitle, $menuRequiresPerm, $k, array($_eregansu_wp_router, 'route'), $menuIcon);
		}
		if(isset($route['routes']))
		{
			foreach($route['routes'] as $ck => $info)
			{
				if(substr($ck, 0, 1) == '_') continue;
				$requires = null;
				if(isset($info['require']))
				{
					$requires = $info['require'];
				}
				if(!isset($info['optionTitle']))
				{
					$info['optionTitle'] = ucwords($ck);
				}
				if(isset($info['pageTitle']))
				{
					$title = $info['pageTitle'];
				}
				else
				{
					$title = $info['optionTitle'];
				}
				add_submenu_page($k, $title, $info['optionTitle'], $requires, $k . '/' . $ck, array($_eregansu_wp_router, 'route'));
				
			}
		}
	}
}
