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
    <div>
        {foreach from=$products item=product name=prodLoop}
            {if $smarty.foreach.prodLoop.index % 2 == 0}
                <article class="row products row" data-aos="zoom-out-right">
                <div class="offset-md-2 col-md-4 text-container">
                    <h2>{$product->name|escape:'htmlall':'UTF-8'}</h2>
                    <div class="text-content">{$product->text nofilter}</div>
                </div>
                <div class="col-md-5 images-container {$product->img_setting|escape:'htmlall':'UTF-8'}" style="background-image: url('{$product->image_path|escape:'htmlall':'UTF-8'}')">

                </div>
                </article>
            {else}
                <article class="row products" data-aos="zoom-out-left">
                    <div class="offset-md-2 col-md-5 images-container {$product->img_setting|escape:'htmlall':'UTF-8'}" style="background-image: url('{$product->image_path}')">

                    </div>
                    <div class="col-md-4 text-container reverse">
                        <h2>{$product->name|escape:'htmlall':'UTF-8'}</h2>
                        <div class="text-content">{$product->text nofilter}</div>
                    </div>
                </article>
            {/if}
        {/foreach}
    </div>
{/block}