<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Mainbody 3 columns, content in center: sidebar1 - content - sidebar2
 */
?>

<div id="t3-mainbody" class="container t3-mainbody">
	<div class="row">

		<!-- MAIN CONTENT -->
		<div id="t3-content" class="t3-content col-xs-12 col-md-6  col-md-push-3">
	       <?php if ($this->countModules('mast-col')) : ?>
				<div class="mast-col<?php $this->_c('mast-col') ?>">
					<jdoc:include type="modules" name="<?php $this->_p('mast-col') ?>" style="T3Xhtml" />
				</div>
			<?php endif ?>
			<?php if($this->hasMessage()) : ?>
			<jdoc:include type="message" />
			<?php endif ?>
			<jdoc:include type="component" />
			   <?php if ($this->countModules('home-1')) : ?>
				 <!-- HOME SL 1 -->
					<div class="wrap container t3-sl t3-sl-1 <?php $this->_c('home-1') ?>">
						<jdoc:include type="modules" name="<?php $this->_p('home-1') ?>" style="T3Xhtml" />
					</div>
				 <!-- //HOME SL 1 -->
	          <?php endif ?>
		</div>
		<!-- //MAIN CONTENT -->

		<!-- SIDEBAR 1 -->
		<div class="t3-sidebar t3-sidebar-1 col-xs-6  col-md-3  col-md-pull-6 <?php $this->_c($vars['sidebar1']) ?>">
			<jdoc:include type="modules" name="<?php $this->_p($vars['sidebar1']) ?>" style="T3Xhtml" />
		</div>
		<!-- //SIDEBAR 1 -->
	
		<!-- SIDEBAR 2 -->
		<div class="t3-sidebar t3-sidebar-2 col-xs-6  col-md-3 <?php $this->_c($vars['sidebar2']) ?>">
			<jdoc:include type="modules" name="<?php $this->_p($vars['sidebar2']) ?>" style="T3Xhtml" />
		</div>
		<!-- //SIDEBAR 2 -->
	
	</div>
</div> 
