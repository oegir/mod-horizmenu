<?php
/**
 * @package     Joomla.Site
 * @subpackage  Modules.Horizmenu
 * @copyright   © 2014 Alexey Petrov
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined ( '_JEXEC' ) or die ();

ModHorizmenuHelper::addHorizontalStyleLink($module, $params);
$deepWidth = $params->get ( 'dropWidth', 0 );
?>
<?php // The menu class is deprecated. Use nav instead. ?>
<ul class="nav horizmenu<?php echo $class_sfx;?> menu"
	<?php
	$tag = '';
	
	if ($params->get ( 'tag_id' ) != null) {
		$tag = $params->get ( 'tag_id' ) . '';
		echo ' id="' . $tag . '"';
	}
	?>>
<?php

foreach ( $list as $i => &$item ) {
	$class = 'item-' . $item->id;
	
	if ($item->id == $active_id) {
		$class .= ' current';
	}
	
	if (in_array ( $item->id, $path )) {
		$class .= ' active';
	} elseif ($item->type == 'alias') {
		$aliasToId = $item->params->get ( 'aliasoptions' );
		
		if (count ( $path ) > 0 && $aliasToId == $path [count ( $path ) - 1]) {
			$class .= ' active';
		} elseif (in_array ( $aliasToId, $path )) {
			$class .= ' alias-parent-active';
		}
	}
	
	if ($item->type == 'separator') {
		$class .= ' divider';
	}
	
	if ($item->deeper) {
		$class .= ' deeper';
	}
	
	if ($item->parent) {
		$class .= ' parent';
	}
	
	if (! empty ( $class )) {
		$class = ' class="' . trim ( $class ) . '"';
	}
	
	echo '<li' . $class . '>';
	
	// Render the menu item.
	switch ($item->type) :
		case 'separator' :
		case 'url' :
		case 'component' :
		case 'heading' :
			require JModuleHelper::getLayoutPath ( 'mod_horizmenu', 'default_' . $item->type );
			break;
		
		default :
			require JModuleHelper::getLayoutPath ( 'mod_horizmenu', 'default_url' );
			break;
	endswitch
	;
	
	// The next item is deeper.
	if ($item->deeper) {
		$deepStyle = "";
		echo '<ul class="nav-child unstyled small level-' . $item->level . '" style="' . $deepStyle . '">';
	} elseif ($item->shallower) {
		// The next item is shallower.
		echo '</li>';
		echo str_repeat ( '</ul></li>', $item->level_diff );
	} else {
		// The next item is on the same level.
		echo '</li>';
	}
}
?></ul>
<div class="horizmenu_clr"></div>
