$(document).ready(function () {
    // Cart Toggle
    $('#cart-toggle-btn').click(function () {
        $('#cart-sidebar').fadeIn();
    });

    $('#close-cart-btn').click(function () {
        $('#cart-sidebar').fadeOut();
    });

    // Store manually opened categories
    let manuallyOpened = new Set();

    $('.category-toggle').click(function () {
        var category = $(this).closest('.category-section');
        var productContainer = category.find('.product-container');

        if (productContainer.is(':visible')) {
            productContainer.slideUp();
            manuallyOpened.delete(category[0]); // Mark as manually closed
        } else {
            productContainer.slideDown();
            manuallyOpened.add(category[0]); // Mark as manually opened
        }
    });

    // Search Functionality
    $('#search-box').on('keyup', function () {
        var searchText = $(this).val().toLowerCase();
        var isSearchEmpty = searchText.trim() === '';
        let searchTriggered = false;

        $('.category-section').each(function () {
            var category = $(this);
            var productContainer = category.find('.product-container');
            var hasMatches = false;

            category.find('.product-card').each(function () {
                var itemName = $(this).find('.item-name').text().toLowerCase();
                if (itemName.includes(searchText)) {
                    $(this).show();
                    $(this).prependTo(productContainer); // Bring matching items to the top
                    hasMatches = true;
                } else {
                    $(this).hide();
                }
            });

            if (hasMatches) {
                productContainer.slideDown();
                manuallyOpened.add(category[0]); // Mark category as open when search finds items
                searchTriggered = true;
            } else if (isSearchEmpty && !manuallyOpened.has(category[0])) {
                productContainer.slideUp();
            }
        });
    });

    // Ensure backspace restores manual categories without overriding manual toggle
    $('#search-box').on('input', function () {
        if ($(this).val().trim() === '') {
            $('.category-section').each(function () {
                var category = $(this);
                var productContainer = category.find('.product-container');
                category.find('.product-card').show(); // Show all items again
                if (!manuallyOpened.has(category[0])) {
                    productContainer.slideUp();
                }
            });
        }
    });

    // Filter for Veg and Non-Veg
    $('#veg-btn').click(function () {
        $('.category-section').each(function () {
            var isVeg = $(this).data('category-type') === 'veg';
            $(this).toggle(isVeg);
        });
    });

    $('#nonveg-btn').click(function () {
        $('.category-section').each(function () {
            var isNonVeg = $(this).data('category-type') === 'nonveg';
            $(this).toggle(isNonVeg);
        });
    });
});
