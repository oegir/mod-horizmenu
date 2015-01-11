<?php
/**
 * @package     Joomla.Site
 * @subpackage  Modules.Horizmenu
 * @copyright   © 2014 Alexey Petrov
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined ( '_JEXEC' ) or die ();

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$list = ModHorizmenuHelper::getList ( $params );
$base = ModHorizmenuHelper::getBase ( $params );
$active = ModHorizmenuHelper::getActive ( $params );
$active_id = $active->id;
$path = $base->tree;

$showAll = $params->get ( 'showAllChildren' );
$class_sfx = htmlspecialchars ( $params->get ( 'class_sfx' ) );

$action = $app->input->get ( 'action', '', 'string' );

switch ($action) {
	case 'css' :
		require JModuleHelper::getLayoutPath('mod_horizmenu', $params->get ( 'layout', 'default' ) . '_css');
		break;
	
	default :
		if (count ( $list )) {
			require JModuleHelper::getLayoutPath ( 'mod_horizmenu', $params->get ( 'layout', 'default' ) );
		}
}
