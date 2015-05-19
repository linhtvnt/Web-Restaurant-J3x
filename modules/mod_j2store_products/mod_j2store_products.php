<?php
/*------------------------------------------------------------------------
 # mod_j2store_products - J2Store
 # ------------------------------------------------------------------------
 # author    ThemeParrot - ThemeParrot http://www.ThemeParrot.com
 # copyright Copyright (C) 2014 ThemeParrot.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://ThemeParrot.com
 # Based on Latest Articles module of Joomla
 -------------------------------------------------------------------------*/

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$list = ModJ2StoreProductsHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_j2store_products', $params->get('layout', 'default'));
