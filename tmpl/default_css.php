<?php
/**
 * @package     Joomla.Site
 * @subpackage  Modules.Horizmenu
 * @copyright   © 2014 Alexey Petrov
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$doc->setMimeEncoding('text/css');

$floating = $params->get ( 'floating', '0' ) ? 'right' : 'left';
?>
@CHARSET "UTF-8";

ul.nav.horizmenu<?php echo $class_sfx;?>,
ul.nav.horizmenu<?php echo $class_sfx;?> ul {
	list-style: none;
	padding: 0;
	position: relative;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li.deeper li {
	width: 100%;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li {
    float: <?php echo $floating; ?>;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li a {
	display: block;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li.parent ul {
	position: absolute;
	visibility: hidden;
	margin: 0;
	padding: 0;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li.parent:HOVER > ul {
	visibility: visible;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li.parent:HOVER .nav-child .nav-child {
	
}
ul.nav.horizmenu<?php echo $class_sfx;?> li.parent li.parent a,
ul.nav.horizmenu<?php echo $class_sfx;?> li.parent li.parent .separator {
    float: <?php echo $floating; ?>;
    white-space: nowrap;
    width: 100%;
}
ul.nav.horizmenu<?php echo $class_sfx;?> li.parent li  {
	clear: <?php echo $floating; ?>;
}
ul.nav.horizmenu<?php echo $class_sfx;?> .horizmenu_clr {
    clear: <?php echo $floating; ?>;
}
ul.nav.horizmenu<?php echo $class_sfx;?> .nav-child .nav-child {
    <?php echo $floating; ?>: 100%;
}