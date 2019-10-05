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

class ProductPresentation extends ObjectModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $image_path;

    /**
     * @var string
     */
    public $text;

    /**
     * @var int
     */
    public $img_setting;

    /**
     * @var int
     */
    public $position;

    /**
     * @var array
     */
    public static $definition = array(
        'table' => 'product_presentation',
        'primary' => 'id_product_presentation',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'image_path' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'text' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true),
            'img_setting' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
        ),
    );


    /**
     * @return bool
     */
    public static function install()
    {
        $query = '
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . static::$definition['table'] . ' (
                ' . static::$definition['primary'] . ' INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                image_path VARCHAR(255) NOT NULL,
                text TEXT NOT NULL,
                img_setting INT NOT NULL,
                position INT NOT NULL
            );
        ';

        return Db::getInstance()->execute($query);
    }

    /**
     * @return bool
     */
    public static function uninstall()
    {
        $query = '
            DROP TABLE IF EXISTS ' . _DB_PREFIX_ . static::$definition['table']. ';
        ';

        return Db::getInstance()->execute($query);
    }

    /**
     * @param bool $doesReturnObject
     * @param bool $byPosition
     * @return array
     */
    public static function getAll($doesReturnObject = true, $byPosition = true)
    {
        $query = '
            SELECT ' . static::$definition['primary'] . ', name, image_path, text, position
            FROM ' . _DB_PREFIX_ . static::$definition['table'] . '
            
        ';

        if ($byPosition) {
            $query .= ' ORDER BY position ASC';
        }

        $rows = Db::getInstance()->executeS($query);

        if ($doesReturnObject) {
            if (is_array($rows) && count($rows) > 0) {
                foreach ($rows as &$row) {
                    $row = new ProductPresentation($row[static::$definition['primary']]);
                    $row->img_setting = ProductPresentation::getClassFromImgSetting($row->img_setting);
                }
            } else {
                $rows = array();
            }
        }

        return $rows;
    }

    public static function getClassFromImgSetting($imgSetting)
    {
        $class = '';

        switch ($imgSetting) {
            default:
            case SimpleCatalog::IMG_SETTING_COVER:
                $class = '';
                break;
            case SimpleCatalog::IMG_SETTING_NO_RESIZE:
                $class = 'no-resize';
                break;
            case SimpleCatalog::IMG_SETTING_LOW:
                $class = 'low-position';
                break;
            case SimpleCatalog::IMG_SETTING_TOP:
                $class = 'high-position';
                break;
        }

        return $class;
    }

    public static function getMaxPos()
    {
        $query = '
            SELECT MAX(position)
            FROM ' . _DB_PREFIX_ . static::$definition['table'] . '
        ';

        $rows = Db::getInstance()->executeS($query);

        return $rows[0]['MAX(position)'];
    }
}
