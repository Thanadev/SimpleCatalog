{**
* 2019 Thanadev
*
* NOTICE OF LICENSE
*
* DISCLAIMER
*
* Do not edit or add to this file, add a theme override
*
* @author Thomas Thanadev Lambert <contact@thomas-lambert.fr>
* @copyright  2019 Thanadev
* @license GPL v3 https://www.gnu.org/licenses/quick-guide-gplv3.fr.html
**}

{extends file='page.tpl'}

{block name="page_content"}
    <div class="offset-md-3 col-md-6 coords-container" style="background-image: url(/img/coords.png)">
        <div class="col-md-7"></div>
        <div class="col-md-5 coords-panel">
            <img class="logo img-responsive" src="{$shop.logo|escape:'htmlall':'UTF-8'}" alt="{$shop.name|escape:'htmlall':'UTF-8'}">
            <div>
                <ul>
                    <li>{$contactName|escape:'htmlall':'UTF-8'}</li>
                    <li>{$contactPhone|escape:'htmlall':'UTF-8'}</li>
                    <li>{$contactMail|escape:'htmlall':'UTF-8'}</li>
                </ul>
                <ul>
                    <li>{$officeAddress|escape:'htmlall':'UTF-8'}</li>
                    <li>{$legalNotice|escape:'htmlall':'UTF-8'}</li>
                </ul>
            </div>
        </div>
    </div>
{/block}