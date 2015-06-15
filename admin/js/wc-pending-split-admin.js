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
					$content.find('table').append(
						'<tr>'+
							'<td>'+el.thumb+'</td>'+
							'<td>'+el.name+'</td>'+
							'<td>'+el.item_cost_view+'</td>'+
							'<td><input type="number" step="1" min="1" data-max_qty="'+parseInt(el.max_qty)+'" autocomplete="off"  placeholder="1" value="1" size="4" class="move-quantity"></td>'+
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
				alert('moving now')
				/*
				$.post(ajaxurl, {'action': 'split_order_items', 'order_id': order_id, 'order_item_ids': order_item_ids}, function(data, textStatus, xhr) {
					$("#order_line_items .item").has('.check-column input:checked').remove();
				});*/
				this.closeButton( e );
			},


		});

		$('#woocommerce-order-items').on('click', '#wc_split_button', function(){
						
			var WCSplitModal = new WCSplitBackboneModal();
			
			//retrieve a list of objects from the selected products to be moved.
			var order_item_ids=[];

			$("#order_line_items .item").has('.check-column input:checked').each(function(index, el) {
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
					if($(this).val() > $(this).data('max_qty')){
						$(this).val($(this).data('max_qty'));
					}
				})
			}
			
		})
	})
})( jQuery );

