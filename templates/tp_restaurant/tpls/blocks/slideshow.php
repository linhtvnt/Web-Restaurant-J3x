<?php
/**
 * ------------------------------------------------------------------------
 * TP Restaurant Template
 * ------------------------------------------------------------------------
 * Copyright (C) Themeparrot.com. All Rights Reserved.
 * Author: Vijayanand M
 * Websites:  http://www.themeparrot.com 
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
?>

<?php if ($this->countModules('slideshow')) : ?>
<!-- Slideshow -->
<div class="tp-slideshow <?php $this->_c('slideshow') ?>">
	<jdoc:include type="modules" name="<?php $this->_p('slideshow') ?>" style="raw" />
</div>
<!-- //Slideshow-->
<?php endif ?>

