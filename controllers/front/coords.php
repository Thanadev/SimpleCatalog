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

/**
 * Class simplecatalogcoordsModuleFrontController
 */
class simplecatalogcoordsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        Context::getContext()->smarty->assign(array(
            'contactName' => Configuration::get(SimpleCatalog::CONTACT_NAME_KEY),
            'contactPhone' => Configuration::get(SimpleCatalog::CONTACT_PHONE_KEY),
            'contactMail' => Configuration::get(SimpleCatalog::CONTACT_MAIL_KEY),
            'officeAddress' => Configuration::get(SimpleCatalog::CONTACT_OFFICE_KEY),
            'legalNotice' => Configuration::get(SimpleCatalog::CONTACT_NOTICE_KEY),
        ));

        $this->setTemplate('module:simplecatalog/views/templates/front/coords.tpl');
    }
}
