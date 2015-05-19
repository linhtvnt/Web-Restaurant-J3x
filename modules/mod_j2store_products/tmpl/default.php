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

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/modules/mod_j2store_products/css/j2store_products.css');

$j2store_path = JPATH_ADMINISTRATOR.'/components/com_j2store/j2store.php';
if(JFile::exists($j2store_path)) {
	require_once (JPATH_SITE.'/components/com_j2store/helpers/cart.php');
	require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/library/prices.php');
}

//image position
$image_position = $params->get('image_position', 'top');
if($image_position == 'top') {
	$style = 'clear:both;';
}elseif($image_position == 'left') {
	$style = 'float:left;';
}elseif($image_position == 'right') {
	$style = 'float:right;';
}

$j2params = JComponentHelper::getParams('com_j2store');
//print_r($list);
//initialise certain variables

?>
<div class="mod_j2store_products<?php echo $moduleclass_sfx; ?> j2store_products">
	<ul class="j2store_products_container">
		<?php foreach($list as $item) : ?>
			<li class="j2product product_<?php echo $item->id;?> j2store_product_block">

					<?php if($params->get('show_image', 1)):?>
							<?php $image_path = '';
							$image_path = ModJ2StoreProductsHelper::getImage($item->id, $params); ?>
							<?php if(!empty($image_path)):?>
							<div class="j2store_product_image_block" style="<?php echo $style;?>">
								<span class="j2store_product_image">
								<img
								src="<?php echo JURI::root().$image_path;?>"
								alt="<?php echo $item->title; ?>"
								style="width:<?php echo $params->get('image_size_width');?>px;
								height:<?php echo $params->get('image_size_width');?>px;"
								/>
								</span>
							</div>
							<?php endif;?>
					<?php endif; ?>

				<?php
					if(!$params->get('show_image', 1) || empty($image_path)) {
						$span = '12';
					} else {
						$span = '6';
					}
				?>
				<div class="j2store_product_content_block">
				<?php if($params->get('show_title', 1)):?>
						<h2 class="j2store_product_title">
							<?php if($params->get('link_titles', 1)):?>
								<a href="<?php echo $item->link; ?>" class="j2store_product_title_link">
								<?php echo $item->title; ?>
								</a>
							<?php else:?>
								<?php echo $item->title; ?>
							<?php endif;?>
						</h2>
				<?php endif; ?>

				<?php if($params->get('show_sku', 1) ||  $params->get('show_category', 1)):?>
				<div class="j2store_product_sku_category">
					<?php if($params->get('show_sku', 1)):?>
							<span class="j2store_product_sku">
								<small><?php echo $item->j2store->product_model; ?></small>
							</span>
					<?php endif; ?>

					<?php if ($params->get('show_category', 1)) :?>
						<span class="j2store_product_category">
						<?php if ($params->get('link_category', 1)) :?>
							<a class="j2store_product_category_link" href="<?php echo $item->displayCategoryLink?>">
							<?php echo $item->displayCategoryTitle; ?>
							</a>
						<?php else: ?>
							<?php echo $item->displayCategoryTitle; ?>
						<?php endif;?>
						</span>
					<?php endif; ?>
				</div>
				<?php endif;?>

				<?php if($params->get('show_introtext', 1)):?>
						<span class="j2store_product_introtext">
							<?php echo $item->displayIntrotext; ?>
						</span>
				<?php endif; ?>

				<?php if ($params->get('show_readmore')) :?>
				<a class="j2store_product_readmore <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
					<?php echo JText::_('MOD_J2STORE_PRODUCT_READ_MORE'); ?>
				</a>
				<?php endif;?>

				<?php if($params->get('show_price', 1)):?>

						<span id="j2store_product_price_<?php echo $item->product_id; ?>" class="j2store_product_price">
							<?php if($item->j2store->special_price > 0.000) echo '<strike>'; ?>
			    				<?php  echo J2StoreHelperCart::dispayPriceWithTax($item->j2store->price, $item->j2store->tax, $j2params->get('price_display_options', 1)); ?>
			    			<?php if($item->j2store->special_price > 0.000) echo '</strike>'; ?>
			    		</span>

			    	<!--special price-->
					 <?php if($item->j2store->special_price > 0.000) :?>
					    <span id="j2store_product_special_price_<?php echo $item->product_id; ?>" class="j2store_product_special_price">
					    	<?php  echo J2StoreHelperCart::dispayPriceWithTax($item->j2store->special_price, $item->j2store->sp_tax, $j2params->get('price_display_options', 1)); ?>
						    </span>
			 		<?php endif;?>

				<?php endif; ?>

				<?php if($params->get('show_cartbutton', 1)):?>
						<span class="j2store_product_cart">
							<?php echo $item->j2store->cart_block; ?>
						</span>
				<?php endif; ?>
				</div>
			</li>
		<?php endforeach;?>
	</ul>
</div>

