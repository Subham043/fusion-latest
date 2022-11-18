<script type="text/javascript">

    $(function () {


        $(document).on('change', 'select[name="group_name"]', async function () {
            var row = $(this).closest('tr');
            var item =  await $.get('{{ route('inventorygrouplist.ajax.inventoryGroupListLookup') }}' + '?query=' + $(this).val() + '&eventDate=' + row.find('.eventDate').val() + '&invoiceId=' + row.find('.invoiceId').val() + '&quoteId=' + row.find('.quoteId').val())
            var resp = $.parseJSON(item)
            
            row.find('textarea[name="group_description"]').val(resp[0]?.description);
            row.find('input[name="group_quantity"]').val('1');
		row.find('.groupTotalPrice').text(resp[0]?.total ? '$'+ resp[0]?.total : '');
	    row.find('.changeGroupTotal').text(resp[0]?.total ? '$'+ resp[0]?.total : '');
            row.find('input[name="group_price"]').val(resp[0]?.total);
            row.find('.inventory_group_list_id').val(resp[0]?.id);
        });

      
        // Clones a new item row
        async function cloneGroupItemRow() {
            var row = $('#new-group-item').clone().appendTo('#item-group-table');
            row.removeAttr('id').addClass('item_group').show();
            // row.find('input[name="name"]').addClass('item-lookup').typeahead(null, settings);
            
            row.find('select[name="group_name"]').addClass('item-lookup js-example-basic-single-3');
            
            $('textarea').autosize();
	    $('.js-example-basic-single-3').select2();
            
            var item =  await $.get('{{ route('inventorygrouplist.ajax.inventoryGroupListLookup') }}' + '?query=' + row.find('select[name="name"]').val() + '&eventDate=' + row.find('.eventDate').val() + '&invoiceId=' + row.find('.invoiceId').val() + '&quoteId=' + row.find('.quoteId').val())
            var resp = $.parseJSON(item)
            row.find('textarea[name="group_description"]').val(resp[0].description);
            row.find('input[name="group_quantity"]').val('1');
		row.find('.groupTotalPrice').text(resp[0]?.total ? '$'+ resp[0]?.total : '');
	    row.find('.changeGroupTotal').text(resp[0]?.total ? '$'+ resp[0]?.total : '');
            row.find('input[name="group_price"]').val(resp[0].total);
            row.find('.inventory_group_list_id').val(resp[0].id);
        }

        // Sets up .item-lookup to populate proper fields when item is selected
        
        $(document).on('click', '#btn-add-group-item', function () {
            cloneGroupItemRow();
        });
        
        $(document).on('input', 'input[name="group_quantity"]', function () {
             //alert($(this).val() - 1);
	     var row = $(this).closest('tr');
	     var itemAmount = row.find('input[name="group_price"]').val();
	     var calc = parseFloat($(this).val() * itemAmount).toFixed(2)
	     row.find('.changeGroupTotal').html('$'+(calc));
        });

	$(document).on('input', 'input[name="group_price"]', function () {
             //alert($(this).val() - 1);
	     var row = $(this).closest('tr');
	     var itemQuantity = row.find('input[name="group_quantity"]').val();
	     row.find('.changeGroupTotal').html('$'+(parseFloat($(this).val() * itemQuantity).toFixed(2)));
        });
        // Add a new item row if no items currently exist
        @if (!$itemCount)
        //cloneGroupItemRow();
        @endif


    });

</script>