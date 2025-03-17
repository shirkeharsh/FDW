
$(document).ready(function () {
    $(document).on('click', '#checkout-btn', function () {
        console.log("Checkout button clicked!"); // Check if this logs

        let cartData = JSON.parse(localStorage.getItem("cart")) || {};
        console.log("Cart Data:", cartData);

        if (Object.keys(cartData).length === 0) {
            alert("Your cart is empty!");
            return;
        }

        // Convert cart data to URL parameters
        let cartParams = encodeURIComponent(JSON.stringify(cartData));

        // Redirect to checkout.php with cart data
        window.location.href = `checkout/checkout.php?cart=${cartParams}`;
    });
});


// Toggle "View More Items" dropdown (if you still want this feature)
$(document).on("click", "#toggle-extra-items", function () {
    let cartData = JSON.parse(localStorage.getItem("cart")) || {};
    let cartItemsArray = Object.values(cartData);

    let fullCartHtml = `<div id="full-cart-popup" class="cart-popup">
        <h2>All Items</h2>`;

    cartItemsArray.forEach(item => {
        let itemTotal = item.price * item.quantity;
        fullCartHtml += `
            <div class="cart-item">
                <img src="${item.img || 'placeholder.jpg'}" class="cart-img">
                <div>

                    <strong>${item.name}</strong><br>₹${item.price} x ${item.quantity} = ₹${itemTotal}
                </div>
            </div>`;
    });

    fullCartHtml += `<button id="close-full-cart" class="close-btn">Close</button></div>`;

    $("body").append(fullCartHtml);
    $("#full-cart-popup").fadeIn();
});

// Close full cart popup
$(document).on("click", "#close-full-cart", function () {
    $("#full-cart-popup").fadeOut(function () {
        $(this).remove();
    });
});

