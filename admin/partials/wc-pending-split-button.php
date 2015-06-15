<?php

/**
 * Provide the order area view for the plugin's button
 *
 * @link       http://richpress.org
 * @since      1.0.0
 *
 * @package    Wc_Pending_Split
 * @subpackage Wc_Pending_Split/admin/partials
 */
?>


<button type="button" class="button" id="wc_split_button">Move to split order</button>

<script type="text/template" id="wc-modal-split-products">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
					<h1>Move products to split order</h1>
				</header>
				<article>
					<table class="split-table">
						 <colgroup>
						    <col style="width:10%">
						    <col style="width:50%">
						    <col style="width:15%">
						    <col style="width:10%">
						    <col style="width:15%">
						  </colgroup> 
						  <tbody>
						  	
						  </tbody>
					</table>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-split" class="button button-primary button-large">Move</button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close">&nbsp;</div>
</script>