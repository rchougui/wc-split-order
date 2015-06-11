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
							'<td><input type="number" step="1" min="1" max="" autocomplete="off"  placeholder="1" value="1" size="4" class="quantity"></td>'+
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

		$('#wc_split_button').click(function(){
						
			var WCSplitModal = new WCSplitBackboneModal();

			var order_item_ids=[];
			$("#order_line_items .item").has('.check-column input:checked').each(function(index, el) {
				order_item_ids.push({
					item_id : $(el).data('order_item_id'),
					thumb : $(el).find('.thumb').html(),
					name : $(el).find('.name').html(),
				});
			});
			if(order_item_ids.length >0) {
			WCSplitModal.render(order_item_ids);
			}
			
		})
	})
})( jQuery );

