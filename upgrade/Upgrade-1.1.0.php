<?php
/**
 * 2019 Thanadev
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file, add a theme override
 *
 *  @author Thomas 'Thanadev' Lambert <contact@thomas-lambert.fr>
 *  @copyright  2019 Thanadev
 *  @license GPL v3 https://www.gnu.org/licenses/quick-guide-gplv3.fr.html
 **/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($object)
{
    $query = 'ALTER TABLE ' . _DB_PREFIX_ . ProductPresentation::$definition['table'] . ' ADD `position` INT NOT NULL';

    return Db::getInstance()->execute($query);
}
