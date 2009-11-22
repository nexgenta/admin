		</div>
	</div>
	<div id="navigation">
		<?php if(isset($global_nav))
		{
			echo '<ul id="global-nav">';
			foreach($global_nav as $nav)
			{
				echo '<li class="' . _e($nav['class']) . '"><a href="' . _e($nav['link']) . '">' . _e($nav['name']) . '</a></li>';
			}
			echo '</ul>';
		}
		if(isset($toolbar))
		{
			echo '<ul id="toolbar">';
			foreach($toolbar as $nav)
			{
				if(!isset($nav['class'])) $nav['class'] = '';
				echo '<li class="' . _e($nav['class']) . '"><a href="' . _e($nav['link']) . '">' . _e($nav['name']) . '</a></li>';
			}
			echo '</ul>';		
		}
		?>
	</div>	
	<div id="sidebar">
<?php		
	
function sourcelist_branch($list, $indent = "\t\t\t\t", $parent = null)
{
	echo $indent . '<ul>' . "\n";
	foreach($list as $k => $node)
	{
		$k = (strlen($parent) ? $parent . '-' : '') . $k;
		if(!isset($node['link'])) $node['link'] = '#';
		$children = (isset($leaf['children']) && count($leaf['children']));
		$class = (isset($node['class']) ? $node['class'] : '') .  ($children ? ' branch' : '');
		$class = trim($class);
		echo $indent . "\t" . '<li id="' . _e($k) . '"' . (strlen($class) ? ' class="' . _e($class) . '"' : '' ) . '>' . ($children ? "\n" . $indent . "\t\t" : '');
		echo '<a href="' . _e($node['link']) . '">' . _e($node['name']) . '</a>';
		if($children) sourcelist_branch($list['children'], $indent . "\t\t\t");
		echo ($children ? "\n" . $indent . "\t" : '') . '</li>' . "\n";
	}
	echo $indent . '</ul>' . "\n";
}

if(isset($source_list))
{
	$x = '';
	if(isset($source_list_cookie))
	{
		$x = ' data-cookie="' . _e($source_list_cookie) . '"';
	}
	echo "\t\t" . '<dl class="source-list"' . $x . '>' . "\n";			
	foreach($source_list as $k => $source)
	{
		$k = 'sl-' . $k;
		echo "\t\t\t" . '<dt id="' . _e($k) . '">';
		if(isset($source['link'])) echo '<a href="' . _e($source['link']) . '">';
		echo _e($source['name']);
		if(isset($source['link'])) echo '</a>';
		echo '</dt>' . "\n";
		foreach($source['children'] as $kk => $child)
		{
			$kk = $k . '-' . $kk;
			if(!isset($child['link'])) $child['link'] = '#';
			$class = (isset($child['class']) ? $child['class'] : '');
			$branch = (isset($child['children']) && count($child['children'])) ? true : false;
			if($branch) $class .= ' branch collapsed';
			$class = trim($class);
			echo "\t\t\t" . '<dd id="' . _e($kk) . '"' . (strlen($class) ? ' class="' . _e($class) . '"' : '' ) . '>' . "\n";
			echo "\t\t\t\t" . '<a href="' . _e($child['link']) . '">' . _e($child['name']) . '</a>' . "\n";
			if(isset($child['children']) && count($child['children']))
			{
				sourcelist_branch($child['children'], null, $kk);
			}
			echo "\t\t\t" . '</dd>' . "\n";
		}
	}
	echo "\t\t" . '</dl>' . "\n";
}
else if(isset($site_nav))
{
	if(isset($site_nav))
	{
		echo '<ul id="site-nav" class="menu">';
		$first = true;
		foreach($site_nav as $entry) 
		{
			$cl = htmlspecialchars((isset($entry['class']) ? $entry['class'] : '') . ($first ? '  first' : ''));
			if(strlen($cl)) $cl = ' class="' . $cl . '"';
			echo '<li' . $cl . '><a href="' . htmlspecialchars($entry['link']) . '">' . htmlspecialchars($entry['name']) . '</a></li>';
		}
		echo '</ul>';
	}
}
?>
	</div>	
