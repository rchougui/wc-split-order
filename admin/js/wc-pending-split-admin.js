(function( $ ) {
	'use strict';
	$(function(){
		var WCSplitBackboneModal = Backbone.View.extend({
			tagName: 'div',
			id: 'wc-split-modal-dialog',
			_target: undefined,
			events: {
				'click .modal-close': 'closeButton',
				'click #btn-split':      'addButton'
			},
			initialize: function( ) {
				this._target = '#wc-modal-split-products';
				_.bindAll( this, 'render' );
			},
			render: function(items) {
				this.$el.attr( 'tabindex' , '0' ).append( $( this._target ).html() );

				$( 'body' ).css({
					'overflow': 'hidden'
				}).append( this.$el );

				var $content  =  $( '.wc-backbone-modal-content' ).find( 'article' );
				

				$(items).each(function(index, el) {
					$content.find('table tbody').append(
						'<tr class="split-item">'+
							'<td class="thumb">'+el.thumb+'</td>'+
							'<td>'+el.name+'</td>'+
							'<td class="cost" data-cost="'+el.item_cost+'">'+el.item_cost_view+'</td>'+
							'<td><input class="move-quantity" type="number" name="order_item_qty['+el.item_id+']" step="1" min="0" max="'+parseInt(el.max_qty)+'"data-max_qty="'+parseInt(el.max_qty)+'" autocomplete="off"  placeholder="1" value="1" size="4"></td>'+
							'<td class="total"><span>'+el.item_cost_view+
							'</span><input type="hidden" name="line_total['+el.item_id+']" value=""/>'+
							'<input type="hidden" name="line_subtotal['+el.item_id+']" value="" />'+
							'</td>'+
						'</tr>'
						);
				});



			},
			closeButton: function( e ) {
				e.preventDefault();
				this.undelegateEvents();
				$( document ).off( 'focusin' );
				$( 'body' ).css({
					'overflow': 'auto'
				});
				this.remove();
			},
			addButton: function( e ) {

				/*
				$.post(ajaxurl, {'action': 'split_order_items', 'order_id': order_id, 'order_item_ids': order_item_ids}, function(data, textStatus, xhr) {
					$("#order_line_items .item").has('.check-column input:checked').remove();
				});*/
				var orderdata = {
					order_id: woocommerce_admin_meta_boxes.post_id,
					items:    $( 'table.split-table :input[name]' ).serialize(),
					action:   'split_order_items',
					security: wc_split_order.split_order_items_nonce
				};
				$.post(ajaxurl, orderdata, function(data, textStatus, xhr) {
					//force a reload by mimicking a cancel edition event.
					$('button.cancel-action').attr('data-reload', 'true').click();
				});
	
				this.closeButton( e );
			},


		});

		$('#woocommerce-order-items').on('click', '#wc_split_button', function(){
						
			var WCSplitModal = new WCSplitBackboneModal();
			
			//retrieve a list of objects from the selected products to be moved.
			var order_item_ids=[];

			$("#woocommerce-order-items .item").has('.check-column input:checked').each(function(index, el) {
				order_item_ids.push({
					item_id : $(el).data('order_item_id'),
					thumb : $(el).find('.thumb').html(),
					name : $(el).find('.name').html(),
					item_cost : $(el).find('.item_cost').data('sort-value'),
					item_cost_view : $(el).find('.item_cost .view').html(),
					max_qty : $(el).find('.quantity .view').html()
				});
			});

			//only open modal if we have selected products.
			if(order_item_ids.length >0) {
				WCSplitModal.render(order_item_ids);
				//prevent user from moving products more than their original quantity.
				$('body').on('change', '.move-quantity', function(e){
					var qty = $(this).val();
					var cost = $(this).parent().siblings('.cost').data('cost');
					if(qty > $(this).data('max_qty')){
						$(this).val($(this).data('max_qty'));
					}
					var line_subtotal = cost*qty;
					$(this).parent().siblings('.total').find('span').html(accounting.formatMoney(line_subtotal, { symbol: woocommerce_admin_meta_boxes.currency_format_symbol,  format: woocommerce_admin_meta_boxes.currency_format }));
					//We won't be using discounts for now, so applying subtotal to all total fields
					$(this).parent().siblings('.total').find('input').val(line_subtotal);
				})
			}
			
		})
	})
})( jQuery );

