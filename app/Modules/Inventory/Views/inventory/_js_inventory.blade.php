<script type="text/javascript">

    $(function () {


        $(document).on('change', 'select[name="name"]', async function () {
            var row = $(this).closest('tr');
            var item =  await $.get('{{ route('inventory.ajax.inventoryLookup') }}' + '?query=' + $(this).val() + '&eventDate=' + row.find('.eventDate').val() + '&invoiceId=' + row.find('.invoiceId').val() + '&quoteId=' + row.find('.quoteId').val())
            var resp = $.parseJSON(item)
            
            row.find('textarea[name="description"]').val(resp[0]?.description);
            row.find('input[name="quantity"]').val('1');
            row.find('input[name="price"]').val(resp[0]?.price);
            row.find('select[name="tax_rate_id"]').val(resp[0]?.tax_rate_id);
            row.find('select[name="tax_rate_2_id"]').val(resp[0]?.tax_rate_2_id);
	    row.find('.changeTotal').text(resp[0]?.price ? '$'+ resp[0]?.price : '');
            row.find('.inventory_id').val(resp[0]?.id);
            row.find('.total-quan').text(resp[0]?.total);
            row.find('.reserved-quan').text(resp[0]?.reserved);
            row.find('.alloted-quan').text(resp[0]?.allocated);
            row.find('.available-quan').text(resp[0]?.available);
	    //row.find('.productTotalPrice').text(resp[0]?.price ? '$'+ resp[0]?.price : '');
            row.find('input[name="availableQuan"]').val(resp[0]?.available);
        });

        // All existing items should populate proper fields
        typeaheadTrigger();

        // Clones a new item row
        async function cloneItemRow() {
            var row = $('#new-item').clone().appendTo('#item-table');
            row.removeAttr('id').addClass('item').show();
            // row.find('input[name="name"]').addClass('item-lookup').typeahead(null, settings);
            
            row.find('select[name="name"]').addClass('item-lookup js-example-basic-single-2');
            typeaheadTrigger();
            $('textarea').autosize();
    	    $('.js-example-basic-single-2').select2();

            
            var item =  await $.get('{{ route('inventory.ajax.inventoryLookup') }}' + '?query=' + row.find('select[name="name"]').val() + '&eventDate=' + row.find('.eventDate').val() + '&invoiceId=' + row.find('.invoiceId').val() + '&quoteId=' + row.find('.quoteId').val())
            var resp = $.parseJSON(item)
	    console.log(resp)
		//var price = resp[0]?.price;
            row.find('textarea[name="description"]').val(resp[0]?.description ? resp[0].description : '');
            row.find('input[name="quantity"]').val('1');
            row.find('input[name="price"]').val(resp[0]?.price);
            row.find('select[name="tax_rate_id"]').val(resp[0]?.tax_rate_id);
            row.find('select[name="tax_rate_2_id"]').val(resp[0]?.tax_rate_2_id);
		//console.log(row.find('.changeTotal'));
	    row.find('.changeTotal').text(resp[0]?.price ? '$'+ resp[0]?.price : '');
            row.find('.inventory_id').val(resp[0]?.id);
            row.find('.total-quan').text(resp[0]?.total);
            row.find('.reserved-quan').text(resp[0]?.reserved);
            row.find('.alloted-quan').text(resp[0]?.allocated);
            row.find('.available-quan').text(resp[0]?.available);
	    //row.find('.productTotalPrice').text(resp[0]?.price ? '$'+ resp[0]?.price : '');
            row.find('input[name="availableQuan"]').val(resp[0]?.available);
	    
		
        }

        // Sets up .item-lookup to populate proper fields when item is selected
        function typeaheadTrigger() {
            $('.item-lookup').on('typeahead:selected typeahead:autocompleted', function (obj, item, name) {
                var row = $(this).closest('tr');
                row.find('textarea[name="description"]').val(item.description);
                row.find('input[name="quantity"]').val('1');
                row.find('input[name="price"]').val(item.price);
                row.find('select[name="tax_rate_id"]').val(item.tax_rate_id);
                row.find('select[name="tax_rate_2_id"]').val(item.tax_rate_2_id);
            });
        }

        $(document).on('click', '#btn-add-item', function () {
            cloneItemRow();
        });
        
        $(document).on('input', 'input[name="quantity"]', function () {
             //alert($(this).val() - 1);
	     var row = $(this).closest('tr');
	     var itemAmount = row.find('input[name="price"]').val();
	     var calc = parseFloat($(this).val() * itemAmount).toFixed(2)
	     row.find('.changeTotal').html('$'+(calc));
        });

	$(document).on('input', 'input[name="price"]', function () {
             //alert($(this).val() - 1);
	     var row = $(this).closest('tr');
	     var itemQuantity = row.find('input[name="quantity"]').val();
	     row.find('.changeTotal').html('$'+(parseFloat($(this).val() * itemQuantity).toFixed(2)));
        });

        // Add a new item row if no items currently exist
        @if (!$itemCount)
        cloneItemRow();
        @endif


    });

</script>