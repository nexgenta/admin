<?php

/* Copyright 2009 Mo McRoberts.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The names of the author(s) of this software may not be used to endorse
 *    or promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, 
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY 
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL
 * AUTHORS OF THIS SOFTWARE BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class AdminAppRouter extends App
{
	protected $crumbName = 'Administration';
	protected $crumbClass = 'administration';
	
	public function __construct()
	{
		global $ADMIN_ROUTES;
		
		parent::__construct();
		$this->sapi['http'] = array_merge($this->sapi['http'], $ADMIN_ROUTES);
		if(!isset($this->routes['__NONE__']))
		{
			$this->routes['__NONE__'] = array('class' => 'AdminRedirect');
		}
	}
}

class AdminRedirect extends Redirect
{
 	public function __construct()
 	{
 		global $ADMIN_ROUTES;
 		
 		parent::__construct();
		foreach($ADMIN_ROUTES as $k => $route)
		{
			$this->target = '/' . $k . '/';
			break;
		}
 	}
}

class AdminPage extends Page
{
	protected function assignTemplate()
	{
		global $ADMIN_ROUTES;
		
		parent::assignTemplate();
		$perms = array();
		if(isset($this->session->user) && isset($this->session->user['perms']))
		{
			$perms = $this->session->user['perms'];
		}
		$this->vars['global_nav'] = array();
		foreach($ADMIN_ROUTES as $k => $route)
		{
			if(in_array($route['require'], $perms))
			{
				$this->vars['global_nav'][$k] = array('name' => $route['title'], 'link' => $this->request->root . $k . '/', 'class' => $route['linkClass']);
			}
		}
		$this->vars['global_nav']['logout'] = array('name' => 'Sign out', 'link' => $this->request->root . 'login/?logout=' . $this->session->nonce, 'class' => 'logout');
	}
	
	protected function setActiveSourceListEntry()
	{
		$args = func_get_args();
		if(!count($args)) return;
		$first = array_shift($args);
		if(!isset($this->vars['source_list'][$first])) return;
		$p =& $this->vars['source_list'][$first];
		foreach($args as $node)
		{
			if(!isset($p['children'][$node]))
			{
				break;
			}
			$p =& $p['children'][$node];
		}
		if(!isset($p['class'])) $p['class'] = '';
		$p['class'] .= ' active';
	}
}