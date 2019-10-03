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
 * Class simplecatalogproductsModuleFrontController
 */
class simplecatalogproductsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        Context::getContext()->smarty->assign(array(
            'products' => ProductPresentation::getAll(),
        ));

        $this->setTemplate('module:simplecatalog/views/templates/front/products.tpl');
    }
}
