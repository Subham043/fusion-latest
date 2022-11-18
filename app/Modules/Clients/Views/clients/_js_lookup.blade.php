<script type="text/javascript">

    $(function () {

        var clients = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.num);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            //remote: '{{ route('clients.ajax.lookup') }}' + '?query=%QUERY'
	    remote: {
        	url: '{{ route('clients.ajax.lookup') }}',
        	replace: function (url, query) {
            		return url + '?query=' + query + '&master_client=' + $('#create_master_client_name').val()
            	},
        	wildcard: '%QUERY',
    	    }
        });

        clients.initialize();

        $('.client-lookup').typeahead(null, {
            minLength: 3,
            source: clients.ttAdapter(),
        });

    });

</script>