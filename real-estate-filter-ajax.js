 jQuery(document).ready(function($) {
    $('#real-estate-filter').submit(function(e) {
        e.preventDefault();
        
        var filterData = $(this).serialize(); 

        $.ajax({
            url: realEstateAjax.ajax_url, 
            type: 'POST',
            data: {
                action: 'real_estate_filter',
                // page: 1, 
                filter: filterData, 
                security: realEstateAjax.nonce,
            },
            success: function(response) {
                $('#real-estate-results').html(response); 
            },
            error: function() {
                $('#real-estate-results').html('Виникла помилка при завантаженні даних.');
            }
        });
    });

    // Обробка пагінації
     // $(document).on('click', '.pagination-link', function(e) {
    //     e.preventDefault();

    //     var page = $(this).data('page'); // Отримуємо номер сторінки
    //     var filterData = $('#real-estate-filter').serialize();

    //     $.ajax({
    //         url: realEstateAjax.ajax_url,
    //         type: 'POST',
    //         data: {
    //             action: 'real_estate_filter',
    //             page: page,
    //             filter: filterData
    //         },
    //         success: function(response) {
    //             $('#real-estate-results').html(response); // Оновлюємо список постів
    //         }
    //     });
    // });
    
});
   