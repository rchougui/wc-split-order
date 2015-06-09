(function( $ ) {
	'use strict';
	$(function(){

		$('#wc_split_button').click(function(){
			var order_item_ids=[];
			var order_id = $(this).data('order_id');
			$("#order_line_items .item").has('.check-column input:checked').each(function(index, el) {
				order_item_ids.push($(el).data('order_item_id'));
			});
			$.post(ajaxurl, {'action': 'split_order_items', 'order_id': order_id, 'order_item_ids': order_item_ids}, function(data, textStatus, xhr) {
				$("#order_line_items .item").has('.check-column input:checked').remove();
			});
		})
	})
})( jQuery );
