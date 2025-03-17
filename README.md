# Food Delivery Website

A complete web-based food delivery system that allows users to browse restaurants, order food, and track deliveries in real-time.

## Features
- **User Authentication**: Signup, login, and profile management.
- **Restaurant Listings**: View available restaurants and their menus.
- **Food Ordering**: Add items to cart and place orders.
- **Order Tracking**: Real-time order status updates.
- **Payment Integration**: Online payment and cash-on-delivery options.
- **Admin Panel**: Manage restaurants, orders, and users.

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript, Bootstrap
- **Backend**: PHP, MySQL
- **Payment Gateway**: Razorpay/PayPal (optional)
- **Google Maps API**: For real-time tracking (optional)

## Installation Guide
### Prerequisites
- XAMPP/WAMP for local development
- A web browser

### Steps to Install
1. Clone the repository:
   ```sh
   git clone https://github.com/yourusername/food-delivery-website.git
   ```
2. Move the project folder to your server directory (e.g., `htdocs` for XAMPP).
3. Import the database:
   - Open `phpMyAdmin`
   - Create a new database (e.g., `food_delivery`)
   - Import `database.sql`
4. Configure the database connection:
   - Open `config.php`
   - Update database credentials:
     ```php
     $host = 'localhost';
     $user = 'root';
     $password = '';
     $database = 'food_delivery';
     ```
5. Start your local server (Apache & MySQL).
6. Open the website in a browser:
   ```
   http://localhost/food-delivery/
   ```

## Usage
1. **User Signup/Login**: Create an account or log in.
2. **Browse Restaurants**: Select a restaurant and view its menu.
3. **Place an Order**: Add food items to the cart and confirm the order.
4. **Make Payment**: Choose a payment method and complete the transaction.
5. **Track Order**: View the order status and estimated delivery time.

## Screenshots
![Homepage](screenshots/home.png)
![Menu Page](screenshots/menu.png)
![Order Tracking](screenshots/tracking.png)

## Future Enhancements
- Mobile app integration.
- AI-based food recommendations.
- Live chat support for users.
- Discount coupon system.

## Contributing
Pull requests are welcome! Feel free to submit issues and suggestions.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact
For any queries or suggestions, contact: hrshshirke.site ****
