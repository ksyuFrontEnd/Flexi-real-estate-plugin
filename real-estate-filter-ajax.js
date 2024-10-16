jQuery(document).ready(function($) {

    // Function to load posts on page load
    function loadPosts(page = 1) {
        var filterData = $('#real-estate-filter').serialize(); 

        $.ajax({
            url: realEstateAjax.ajax_url, 
            type: 'POST',
            data: {
                action: 'real_estate_filter',
                filter: filterData, 
                security: realEstateAjax.nonce,
                paged: page, 
            },
            success: function(response) {
                $('#real-estate-results').html(response); 
            },
            error: function() {
                $('#real-estate-results').html('Виникла помилка при завантаженні даних.');
            }
        });
    }

    // Load posts on page load
    loadPosts(); 

    // Handle filter form submit
    $('#real-estate-filter').submit(function(e) {
        e.preventDefault();
        loadPosts(); 
    });

    // Pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        
        var page = $(this).attr('href').split('paged=')[1]; 
        loadPosts(page); 
    });
});
