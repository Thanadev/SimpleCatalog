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

<div class="panel">
	<h2>{l s='Simple Catalog' mod='simplecatalog'}</h2>

	<p class="alert alert-success {if $hasSubmitSuccessed == false }hidden{/if}">
        {l s='Settings saved!' mod='simplecatalog'}
	</p>

	<div class="alert alert-info">
		<p>{l s='This module creates 2 additional urls dedicated to your activity presentation:' mod='simplecatalog'}</p>
		<ul>
			<li>{l s='To access the product page go to: http://my-shop/module/simplecatalog/products' mod='simplecatalog'}</li>
			<li>{l s='To access the coordinate page go to: http://my-shop/module/simplecatalog/coords' mod='simplecatalog'}</li>
		</ul>
	</div>
</div>
