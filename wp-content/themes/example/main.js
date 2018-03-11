jQuery(function () {
    //--- Datatable JS init
    jQuery('#results-table').DataTable();

    jQuery("#country").autocomplete({
        source: function (request, response) {
            jQuery.ajax({
                url: baseUrl + '/wp-content/themes/example/search.php',
                dataType: "json",
                data: {
                    term: request.term,
                    country: true
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2
    });

    jQuery("#city").autocomplete({
        source: function (request, response) {
            jQuery.ajax({
                url: baseUrl + '/wp-content/themes/example/search.php',
                dataType: "json",
                data: {
                    term: request.term,
                    city: true
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2
    });
});


