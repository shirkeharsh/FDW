$(document).ready(function () {
    let cart = {}; // Initialize cart

    // Hide Cart Sidebar by Default
    $('#cart-sidebar').hide();

    // Toggle Cart Sidebar on Click
    $('#cart-toggle-btn').on('click', function () {
        $('#cart-sidebar').fadeToggle();
    });

    // Close Cart Sidebar on Close Button Click
    $('#close-cart-btn').on('click', function () {
        $('#cart-sidebar').fadeOut();
    });

    // Add to Cart Functionality
 // Add to Cart Functionality
// Add to Cart Functionality
// Add to Cart Functionality
$(document).ready(function () {
    $(document).on('click', '.add-to-cart-btn', function () {
        if (!isLoggedIn) {
            let confirmLogin = confirm("You need to log in to add items to the cart. Do you want to continue to login?");
            if (confirmLogin) {
                window.location.href = "login/login.php"; // Redirect to login page
            }
            return;
        }

        let productCard = $(this).closest('.product-card');
        let itemName = productCard.find('.item-name').text();
        let itemPrice = parseFloat(productCard.find('.item-price').contents().last().text().replace('₹', '').trim());
        let itemImg = productCard.find('.product-image img').attr('src');
        let quantityContainer = productCard.find('.quantity-container');
        let quantityDisplay = quantityContainer.find('.quantity-display');

        if (isNaN(itemPrice)) {
            console.error("Price not found! Check selectors.");
            return;
        }

        $(this).hide();
        quantityContainer.show();
        quantityDisplay.text(1);

        cart[itemName] = { name: itemName, price: itemPrice, img: itemImg, quantity: 1 };
        updateCart();
    });
});



    // Quantity Increase/Decrease
    $(document).on('click', '.quantity-btn', function () {
        let quantityDisplay = $(this).siblings('.quantity-display');
        let productCard = $(this).closest('.product-card');
        let itemName = productCard.find('.item-name').text();
        let currentQuantity = parseInt(quantityDisplay.text());

        if ($(this).hasClass('minus')) {
            if (currentQuantity > 1) {
                quantityDisplay.text(currentQuantity - 1);
                cart[itemName].quantity--;
            } else {
                quantityDisplay.text(0);
                productCard.find('.quantity-container').hide();
                productCard.find('.add-to-cart-btn').show();
                delete cart[itemName];
            }
        } else if ($(this).hasClass('plus')) {
            if (currentQuantity < 10) {
                quantityDisplay.text(currentQuantity + 1);
                cart[itemName].quantity++;
            }
        }
        updateCart();
    });

    // Remove Item from Cart
    $(document).on('click', '.remove-item', function () {
        let itemName = $(this).data('name');

        // Remove from cart
        delete cart[itemName];
        updateCart();

        // Reset quantity to 0 in product list
        let productCard = $('.product-card').filter(function () {
            return $(this).find('.item-name').text() === itemName;
        });

        productCard.find('.quantity-display').text(0);
        productCard.find('.quantity-container').hide();
        productCard.find('.add-to-cart-btn').show();
    });

    // Update Cart Function
    function updateCart() {
        localStorage.setItem("cart", JSON.stringify(cart));
        console.log("Cart Updated:", cart);

        let totalQuantity = 0;
        let totalPrice = 0;
        let cartHtml = "";

        $.each(cart, function (key, item) {
            let itemTotal = item.price * item.quantity;
            totalPrice += itemTotal;
            totalQuantity += item.quantity;
            cartHtml += `
                <div class="d-flex align-items-center mb-3">
                    <img src="${item.img}" class="rounded me-2" style="width: 50px; height: 50px;">
                    <div class="flex-grow-1">
                        <strong>${item.name}</strong> <br> ₹${item.price} x ${item.quantity} = ₹${itemTotal}
                    </div>
                    <button class="btn btn-sm btn-danger remove-item" data-name="${item.name}">Remove</button>
                </div>`;
        });

        $('#cart-items').html(cartHtml);
        $('#cart-total').text(totalPrice.toFixed(2));
        $('#cart-count').text(totalQuantity);

        if (totalQuantity === 0) {
            $('#cart-sidebar').fadeOut(); // Close the cart if empty
        }
    }
});
