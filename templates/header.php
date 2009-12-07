<?php

if(!isset($page_title)) $page_title = 'Page';
if(!isset($page_type)) $page_type = '';
if(!isset($colour_scheme)) $colour_scheme = 'graphite';
if(strpos($colour_scheme, '/') === false) $colour_scheme = 'admin/' . $colour_scheme;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<meta name="generator" content="Eregansu" />
		<link rel="stylesheet" type="text/css" href="<?php echo $templates_iri; ?>admin/screen.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php e($templates_iri . $colour_scheme . '.css');?>" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?php echo $skin_iri; ?>screen.css" media="screen" />
		<?php foreach($scripts as $script)
		{
			echo "\t\t" . '<script type="text/javascript" src="' . htmlspecialchars($script) . '"></script>' . "\n";
		}
		foreach($links as $link)
		{
			$s = '<link';
			foreach($link as $k => $v)
			{
				$s .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
			}
			$s .= ' />';
			echo "\t\t" . $s . "\n";
		}
		?>
		<title><?php echo $page_title; ?></title>
	</head>
	<body class="<?php echo $page_type . (isset($site_nav)||isset($source_list) ? ' site-nav' : '') . (count($crumb) ? ' crumb' : '') . ($backRef ? ' backref' : ''); ?>">
		<div id="surround">
			<div id="content">
				<h1><?php e(isset($h1) ? $h1 : $page_title); ?></h1>
				<?php
				if(count($crumb) || $backRef)
				{
					echo '<div id="crumb">';
					if(count($crumb))
					{
						echo '<p class="trail"><span class="leader">You are in: </span>';
						$f = true;
						foreach($crumb as $trail)
						{
							echo ($f ? '' : '<span class="a"> &rarr; </span>') . '<a href="' . _e($trail['link']) . '"';
							if(isset($trail['class'])) echo ' class="' . _e($trail['class']) . '"';
							echo '>' . _e($trail['name']) . '</a>';
							$f = false;
						}
					}
					if($backRef)
					{
						echo '<p class="back"><span><a href="' . _e($backRef['link']) . '">Back to ' . _e($backRef['name']) . '</a></span></p>';
					}
					echo '</div>';
				}
				?>
				