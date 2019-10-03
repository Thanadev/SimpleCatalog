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

include_once(_PS_MODULE_DIR_ . 'simplecatalog/entities/ProductPresentation.php');

/**
 * Class SimpleCatalog
 * This module was designed to display custom information pages (products and company coordinates)
 */
class SimpleCatalog extends Module
{
    const IMG_SETTING_COVER = 0;
    const IMG_SETTING_NO_RESIZE = 1;
    const IMG_SETTING_LOW = 2;
    const IMG_SETTING_TOP = 3;

    const CONTACT_NAME_KEY = 'SIMPLE_CATALOG_COORDS_NAME';
    const CONTACT_PHONE_KEY = 'SIMPLE_CATALOG_COORDS_PHONE';
    const CONTACT_MAIL_KEY = 'SIMPLE_CATALOG_COORDS_MAIL';
    const CONTACT_OFFICE_KEY = 'SIMPLE_CATALOG_COORDS_OFFICE';
    const CONTACT_NOTICE_KEY = 'SIMPLE_CATALOG_COORDS_NOTICE';

    /**
     * SimpleCatalog constructor.
     */
    public function __construct()
    {
        $this->name = 'simplecatalog';
        $this->version = '1.1.0';
        $this->author = 'Thanadev';
        $this->tab = 'front_office_features';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Simple Catalog');
        $this->description = $this->l('Displays custom information pages (products and company coordinates)');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
    }

    /**
     * @return bool
     */
    public function install()
    {
        return (
            parent::install()
            && $this->registerHook('displayHeader')
            && ProductPresentation::install()
        );
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return (
            parent::uninstall()
            && ProductPresentation::uninstall()
        );
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (Tools::getValue('updatePositions')) {
            $positionUpgrade = Tools::getValue('module-' . $this->name);
            $allProducts = ProductPresentation::getAll(false, false);

            for ($i = 0; $i < count($allProducts); $i++) {
                for ($j = 0; $j < count($positionUpgrade); $j++) {
                    $explPosition = explode('_', $positionUpgrade[$j]);

                    if ($allProducts[$i][ProductPresentation::$definition['primary']] == $explPosition[2]) {
                        $product = new ProductPresentation(
                            $allProducts[$i][ProductPresentation::$definition['primary']]
                        );
                        $product->position = $j;
                        $product->save();
                    }
                }
            }
        }

        $hasSuccessed = $this->postProcess();

        if (Tools::isSubmit('add')) {
            $toDisplay = $this->renderProductsConfigForm();
        } elseif (Tools::isSubmit('update' . ProductPresentation::$definition['table'])) {
            $toDisplay = $this->renderProductsConfigForm();
        } elseif (Tools::isSubmit('delete' . ProductPresentation::$definition['table'])) {
            $prodId = Tools::getValue(ProductPresentation::$definition['primary'], 0);
            $product = new ProductPresentation($prodId);
            $product->delete();

            $toDisplay = $this->renderList() . $this->renderCoordsConfigForm();
        } else {
            $toDisplay = $this->renderList() . $this->renderCoordsConfigForm();
        }

        $this->smarty->assign(array(
            'hasSubmitSuccessed' => $hasSuccessed,
        ));

        return $this->display(__FILE__, 'views/templates/admin/config.tpl') . $toDisplay;
    }

    /**
     * Process the config form
     */
    protected function postProcess()
    {
        $hasSuccessed = null;

        if (Tools::isSubmit($this->name . '_admin_submit')) {
            $text = Tools::getValue('productText', '');
            $name = Tools::getValue('productName', '');
            $imgSetting = Tools::getValue('imgSettings', 0);
            $prodId = Tools::getValue(ProductPresentation::$definition['primary'], 0);

            $prod = new ProductPresentation($prodId);
            $prod->name = $name;
            $prod->text = $text;
            $prod->img_setting = $imgSetting;

            if (!$prodId) {
                $maxPos = ProductPresentation::getMaxPos();

                if ($maxPos == null) {
                    $prod->position = 0;
                } else {
                    $prod->position = ProductPresentation::getMaxPos() + 1;
                }
            }

            if (isset($_FILES['productImg']) && !empty($_FILES['productImg']['name'])) {
                $productImagePath = $this->saveProductImage($_FILES['productImg']);
            } elseif ($prodId) {
                $productImagePath = $prod->image_path;
            } else {
                $productImagePath = _MODULE_DIR_ . $this->name;
            }

            $prod->image_path = $productImagePath;

            $hasSuccessed = $prod->save();
        }

        if (Tools::isSubmit($this->name . '_coords_submit')) {
            $contactName = Tools::getValue('contactName', '');
            $contactPhone = Tools::getValue('contactPhone', '');
            $contactMail = Tools::getValue('contactMail', '');
            $officeAddress = Tools::getValue('officeAddress', '');
            $legalNotice = Tools::getValue('legalNotice', '');

            $hasSuccessed = Configuration::updateValue(static::CONTACT_NAME_KEY, $contactName);
            $hasSuccessed &= Configuration::updateValue(static::CONTACT_MAIL_KEY, $contactPhone);
            $hasSuccessed &= Configuration::updateValue(static::CONTACT_PHONE_KEY, $contactMail);
            $hasSuccessed &= Configuration::updateValue(static::CONTACT_OFFICE_KEY, $officeAddress);
            $hasSuccessed &= Configuration::updateValue(static::CONTACT_NOTICE_KEY, $legalNotice);
        }

        return $hasSuccessed;
    }

    /**
     * Registers module's stylesheet
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'modules-simplecatalog', //This id has to be unique
            'modules/'.$this->name.'/views/css/simplecatalog.css',
            array('media' => 'all', 'priority' => 150)
        );
    }

    /**
     * @return string the configuration form
     */
    protected function renderProductsConfigForm()
    {
        $fields_form = array(
            'form' => array(
                'tinymce' => true,
                'legend' => array(
                    'title' => $this->l('Products page settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'name' => 'productName',
                        'label' => $this->l('Name'),
                    ),
                    array(
                        'type' => 'file',
                        'name' => 'productImg',
                        'label' => $this->l('Image'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Text'),
                        'name' => 'productText',
                        'cols' => 40,
                        'rows' => 10,
                        'class' => 'rte',
                        'autoload_rte' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image resizing and positionning'),
                        'name' => 'imgSettings',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_setting' => static::IMG_SETTING_COVER,
                                    'name' => $this->l('Size - Cover'),
                                ),
                                array(
                                    'id_setting' => static::IMG_SETTING_NO_RESIZE,
                                    'name' => $this->l('Size - No resize'),
                                ),
                                array(
                                    'id_setting' => static::IMG_SETTING_LOW,
                                    'name' => $this->l('Position - Bottom position'),
                                ),
                                array(
                                    'id_setting' => static::IMG_SETTING_TOP,
                                    'name' => $this->l('Position - Top position'),
                                ),
                            ),
                            'id' => 'id_setting',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => ProductPresentation::$definition['primary'],
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'pull-right',
                    'name' => $this->name . '_admin_submit',
                ),
            ),
        );

        $areEmployeeFormLangAllowed = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');

        if (!$areEmployeeFormLangAllowed) {
            $areEmployeeFormLangAllowed = 0;
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = $areEmployeeFormLangAllowed;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdate';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.
            '&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFieldsValues()
    {
        $text = '';
        $name = '';
        $productId = Tools::getValue(ProductPresentation::$definition['primary'], 0);
        $imgSettings = Tools::getValue('imgSettings', 0);

        if ($productId) {
            $prod = new ProductPresentation($productId);
            $name = $prod->name;
            $text = $prod->text;
            $imgSettings = $prod->img_setting;
        }

        return array(
            'productName' => $name,
            'productText' => $text,
            'productImg' => '',
            'imgSettings' => $imgSettings,
            ProductPresentation::$definition['primary'] => $productId,
        );
    }

    /**
     * @return string the configuration form
     */
    protected function renderCoordsConfigForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Coordinates page settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'name' => 'contactName',
                        'label' => $this->l('Contact name'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'contactPhone',
                        'label' => $this->l('Contact phone'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'contactMail',
                        'label' => $this->l('Contact mail'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'officeAddress',
                        'label' => $this->l('Head office address'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'legalNotice',
                        'label' => $this->l('Legal Notice'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'pull-right',
                    'name' => $this->name . '_coords_submit',
                ),
            ),
        );

        $areEmployeeFormLangAllowed = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');

        if (!$areEmployeeFormLangAllowed) {
            $areEmployeeFormLangAllowed = 0;
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = $areEmployeeFormLangAllowed;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdate';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.
            '&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getCoordsFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getCoordsFieldsValues()
    {
        $contactName = Configuration::get(static::CONTACT_NAME_KEY);
        $contactPhone = Configuration::get(static::CONTACT_PHONE_KEY);
        $contactMail = Configuration::get(static::CONTACT_MAIL_KEY);
        $contactNotice = Configuration::get(static::CONTACT_NOTICE_KEY);
        $contactOffice = Configuration::get(static::CONTACT_OFFICE_KEY);

        return array(
            'contactName' => $contactName,
            'contactPhone' => $contactPhone,
            'contactMail' => $contactMail,
            'officeAddress' => $contactOffice,
            'legalNotice' => $contactNotice,
        );
    }

    protected function saveProductImage(array $productImage)
    {
        $filename = explode('.', $productImage['name']);
        $imagePath = '';

        if (ImageManager::validateUpload($productImage)) {
            return $imagePath;
        } elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS'))
            || !move_uploaded_file($productImage['tmp_name'], $tmpName)) {
            return $imagePath;
        } elseif (!ImageManager::resize($tmpName, dirname(__FILE__) . '/img/' . $filename[0] . '.jpg')) {
            return $imagePath;
        }

        $imagePath = _MODULE_DIR_ . $this->name . '/img/' . $filename[0] . '.jpg';
        unlink($tmpName);

        return $imagePath;
    }

    protected function renderList()
    {
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->identifier = ProductPresentation::$definition['primary'];
        $helper->simple_header = false;
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->position_identifier = 'position';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';
        $helper->table_id = 'module-' . $this->name;
        $helper->table = ProductPresentation::$definition['table'];
        $fieldsList = $this->getListHeader();
        $values = ProductPresentation::getAll(false);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.
                '&add&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add')
        );

        $helper->actions = array('edit', 'delete');

        return $helper->generateList($values, $fieldsList);
    }

    protected function getListHeader()
    {
        return array(
            ProductPresentation::$definition['primary'] => array(
                'title' => $this->l('Id'), 'width' => 15, 'align' => 'center'
            ),
            'name' => array(
                'title' => $this->l('Nom'), 'align' => 'center'
            ),
            'position' => array(
                'title' => $this->l('Position'), 'width' => 150, 'align' => 'center', 'position' => 'position'
            ),
        );
    }
}
