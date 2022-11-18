<script type="text/javascript">

    $(function () {

        $('#create-quote').modal();

        $('#create-quote').on('shown.bs.modal', function () {
            $("#create_client_name").focus();
	    $('.js-example-basic-single').select2();
            $('#create_client_name').typeahead('val', clientName);
            $(".tt-dropdown-menu").bind('click keyup keypress blur change', function(){
                $.get('{{ route('clients.ajax.userlookup') }}' + '?query='+$("#create_client_name").val() + '&master_client='+$("#create_master_client_name").val()).done(function (response) {
                    var json = JSON.parse(response)
                    if(json!=null){
                        $('#type').val(json.type!=0 ? json.type : 1)
                    }
                }).fail(function (response) {
                    showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
                });
            });
            $("#create_client_name").bind('click keyup keypress blur change', function(){
                $.get('{{ route('clients.ajax.userlookup') }}' + '?query='+this.value + '&master_client='+$("#create_master_client_name").val()).done(function (response) {
                    var json = JSON.parse(response)
                    if(json!=null){
                        $('#type').val(json.type!=0 ? json.type : 1)
                    }
                }).fail(function (response) {
                    showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
                });
            });
        });

        $("#create_quote_date").datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true, defaultDate: new Date(), startDate: new Date()});
        $("#create_event_date").datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true, defaultDate: new Date(), startDate: new Date()});

        $('#quote-create-confirm').click(function () {

            $.post('{{ route('quotes.store') }}', {
                user_id: $('#user_id').val(),
                company_profile_id: $('#company_profile_id').val(),
                client_name: $('#create_client_name_new').val(),
                quote_date: $('#create_quote_date').val(),
                event_date: $('#create_event_date').val(),
                // group_id: $('#create_group_id').val(),
                group_id: 2,
                type: $('#type').val()
            }).done(function (response) {
                window.location = '{{ url('quotes') }}' + '/' + response.id + '/edit';
            }).fail(function (response) {
                showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
            });
        });

	$('#create_master_client_name').change(function() {
		$.get('{{ route('clients.ajax.userlookup_master') }}' + '?master_client='+$("#create_master_client_name").val()).done(function (response) {
                    var json = JSON.parse(response)
                    if(json.length>0){
			console.log(json)
			let data = '';
                        for(i=0;i<json.length;i++){
				data+=`<option id="${json[i].id}">${json[i].name}</option>`
			}
			$('#create_client_name_new').html('');
			$('#create_client_name_new').html(data);	
                    }else{
			$('#create_client_name_new').html('');
		    }
                }).fail(function (response) {
                    showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
                });

	});

    });

</script>