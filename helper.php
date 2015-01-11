<?php
/**
 * @package     Joomla.Site
 * @subpackage  Modules.Horizmenu
 * @copyright   © 2014 Alexey Petrov
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined ( '_JEXEC' ) or die ();

/**
 * Helper for mod_menu
 *
 * @package Joomla.Site
 * @subpackage mod_menu
 * @since 1.5
 */
class ModHorizmenuHelper {
	/**
	 * Get a list of the menu items.
	 *
	 * @param
	 *        	JRegistry &$params The module options.
	 *        	
	 * @return array
	 *
	 * @since 1.5
	 */
	public static function getList(&$params) {
		$app = JFactory::getApplication ();
		$menu = $app->getMenu ();
		
		// Get active menu item
		$base = self::getBase ( $params );
		$user = JFactory::getUser ();
		$levels = $user->getAuthorisedViewLevels ();
		asort ( $levels );
		$key = 'menu_items' . $params . implode ( ',', $levels ) . '.' . $base->id;
		$cache = JFactory::getCache ( 'mod_horizmenu', '' );
		
		if (! ($items = $cache->get ( $key ))) {
			$path = $base->tree;
			$start = ( int ) $params->get ( 'startLevel' );
			$end = ( int ) $params->get ( 'endLevel' );
			$showAll = true;
			$items = $menu->getItems ( 'menutype', $params->get ( 'menutype' ) );
			
			$lastitem = 0;
			
			if ($items) {
				foreach ( $items as $i => $item ) {
					if (($start && $start > $item->level) || ($end && $item->level > $end) || (! $showAll && $item->level > 1 && ! in_array ( $item->parent_id, $path )) || ($start > 1 && ! in_array ( $item->tree [$start - 2], $path ))) {
						unset ( $items [$i] );
						continue;
					}
					
					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;
					
					if (isset ( $items [$lastitem] )) {
						$items [$lastitem]->deeper = ($item->level > $items [$lastitem]->level);
						$items [$lastitem]->shallower = ($item->level < $items [$lastitem]->level);
						$items [$lastitem]->level_diff = ($items [$lastitem]->level - $item->level);
					}
					
					$item->parent = ( boolean ) $menu->getItems ( 'parent_id', ( int ) $item->id, true );
					
					$lastitem = $i;
					$item->active = false;
					$item->flink = $item->link;
					
					// Reverted back for CMS version 2.5.6
					switch ($item->type) {
						case 'separator' :
						case 'heading' :
							// No further action needed.
							continue;
						
						case 'url' :
							if ((strpos ( $item->link, 'index.php?' ) === 0) && (strpos ( $item->link, 'Itemid=' ) === false)) {
								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link . '&Itemid=' . $item->id;
							}
							break;
						
						case 'alias' :
							// If this is an alias use the item id stored in the parameters to make the link.
							$item->flink = 'index.php?Itemid=' . $item->params->get ( 'aliasoptions' );
							break;
						
						default :
							$router = $app::getRouter ();
							
							if ($router->getMode () == JROUTER_MODE_SEF) {
								$item->flink = 'index.php?Itemid=' . $item->id;
							} else {
								$item->flink .= '&Itemid=' . $item->id;
							}
							break;
					}
					
					if (strcasecmp ( substr ( $item->flink, 0, 4 ), 'http' ) && (strpos ( $item->flink, 'index.php?' ) !== false)) {
						$item->flink = JRoute::_ ( $item->flink, true, $item->params->get ( 'secure' ) );
					} else {
						$item->flink = JRoute::_ ( $item->flink );
					}
					
					// We prevent the double encoding because for some reason the $item is shared for menu modules and we get double encoding
					// when the cause of that is found the argument should be removed
					$item->title = htmlspecialchars ( $item->title, ENT_COMPAT, 'UTF-8', false );
					$item->anchor_css = htmlspecialchars ( $item->params->get ( 'menu-anchor_css', '' ), ENT_COMPAT, 'UTF-8', false );
					$item->anchor_title = htmlspecialchars ( $item->params->get ( 'menu-anchor_title', '' ), ENT_COMPAT, 'UTF-8', false );
					$item->menu_image = $item->params->get ( 'menu_image', '' ) ? htmlspecialchars ( $item->params->get ( 'menu_image', '' ), ENT_COMPAT, 'UTF-8', false ) : '';
				}
				
				if (isset ( $items [$lastitem] )) {
					$items [$lastitem]->deeper = (($start ? $start : 1) > $items [$lastitem]->level);
					$items [$lastitem]->shallower = (($start ? $start : 1) < $items [$lastitem]->level);
					$items [$lastitem]->level_diff = ($items [$lastitem]->level - ($start ? $start : 1));
				}
			}
			
			$cache->store ( $items, $key );
		}
		
		return $items;
	}
	
	/**
	 * Get base menu item.
	 *
	 * @param
	 *        	JRegistry &$params The module options.
	 *        	
	 * @return object
	 *
	 * @since 3.0.2
	 */
	public static function getBase(&$params) {
		// Get base menu item from parameters
		if ($params->get ( 'base' )) {
			$base = JFactory::getApplication ()->getMenu ()->getItem ( $params->get ( 'base' ) );
		} else {
			$base = false;
		}
		
		// Use active menu item if no base found
		if (! $base) {
			$base = self::getActive ( $params );
		}
		
		return $base;
	}
	
	/**
	 * Get active menu item.
	 *
	 * @param
	 *        	JRegistry &$params The module options.
	 *        	
	 * @return object
	 *
	 * @since 3.0.2
	 */
	public static function getActive(&$params) {
		$menu = JFactory::getApplication ()->getMenu ();
		
		return $menu->getActive () ? $menu->getActive () : $menu->getDefault ();
	}
	
	/**
	 * Точка входа при Ajax-запросе
	 *
	 * @return void
	 */
	public static function getAjax() {
		// Получим объект модуля по его Id
		$joomla_app = JFactory::getApplication ( 'site' );
		$module_id = $joomla_app->input->get ( 'modid', 0, 'integer' );
		
		$dbo = JFactory::getDBO ();
		$module_id = $dbo->quote ( $module_id, true );
		$dbo->setQuery ( 'SELECT * FROM #__modules WHERE id=' . $module_id );
		$module = $dbo->loadObject ();
		
		if (isset ( $module )) {
			echo JModuleHelper::renderModule ( $module );
		}
	}
	
	/**
	 * Добавляет в header документа ссылку на файл со стилями.
	 * Контролилует уникальность добавленных стилей на основе префикса стилей модуля
	 *
	 * @param stdClass $module        	
	 *
	 * @return void
	 */
	public static function addHorizontalStyleLink($module, $params) {
		static $used_sfxs = Array ();
		
		$cur_sfx = $params->get ( 'class_sfx', 'none' );
		
		if (! in_array ( $cur_sfx, $used_sfxs )) {
			$style_uri = JURI::base () . 'index.php?option=com_ajax&amp;format=raw&amp;action=css&amp;module=' . str_replace ( 'mod_', '', $module->module ) . '&amp;modid=' . $module->id;
			$doc = JFactory::getDocument ();
			$doc->addStyleSheet ( $style_uri );
			$used_sfxs [] = $cur_sfx;
		}
	}
}
