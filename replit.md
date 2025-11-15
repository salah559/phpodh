# Sheep Marketplace - Replit Setup

## Project Overview
A sheep marketplace web application built with PHP, SQLite, and Firebase Authentication. This platform allows users to browse and purchase sheep, with an admin panel for managing products and orders.

**Language**: Arabic (RTL design)
**Original Target**: Algeria market

## Tech Stack
- **Frontend**: HTML, CSS (responsive design), JavaScript
- **Backend**: PHP 8.2
- **Database**: SQLite (converted from MySQL for Replit compatibility)
- **Authentication**: Firebase (Email/Password + Google Sign-in)

## Project Structure
```
.
├── index.html              # Homepage
├── pages/                  # Application pages
│   ├── products.html       # Products listing
│   ├── cart.html          # Shopping cart
│   ├── login.html         # Login/signup
│   └── admin.html         # Admin dashboard
├── assets/
│   ├── css/main.css       # Styles
│   └── js/
│       ├── api.js         # API client
│       └── firebase-config.js  # Firebase setup
├── api/                   # PHP backend API
│   ├── config.php         # Database & CORS config
│   ├── sheep.php          # Products API
│   ├── orders.php         # Orders API
│   ├── admins.php         # Admin management
│   ├── auth.php           # Authentication middleware
│   └── setup_sqlite.sql   # Database schema
├── config.local.php       # Database configuration
└── database.sqlite        # SQLite database
```

## Recent Changes

### Homepage Redesign (November 15, 2025)
- **Updated Hero Section**: Changed headline to "اختر أضحيتك المثالية" to match target design
- **Added Stats Section**: Four-stat display showing quality guarantees, trust metrics, pricing advantages, and customer satisfaction
- **Enhanced Category Navigation**: Added visual category cards with images for محلي, روماني, and إسباني categories
- **New Special Offers Section**: Dedicated section displaying discounted products
- **Refreshed Features Section**: Updated bottom features highlighting quality assurance, fast delivery, halal certification, and customer service
- **CSS Enhancements**: Added responsive styles for stats-grid, categories-grid, and features-section
- **Fixed Cart Functionality**: Implemented shared product cache (`addToAllSheep`) to ensure cart buttons work across all sections

### Replit Setup (November 15, 2025)
- Converted from MySQL to SQLite for Replit compatibility
- Created SQLite schema in `api/setup_sqlite.sql`
- Updated `api/config.php` to support both MySQL and SQLite
- Database is created at project root as `database.sqlite`
- Updated CORS settings to allow all origins (required for Replit webview)
- Created `config.local.php` at project root with SQLite configuration
- PHP built-in server configured on port 5000
- Server command: `php -S 0.0.0.0:5000`
- Deployment configured for autoscale

## Features
✅ Product browsing with category filters  
✅ Shopping cart (localStorage)  
✅ Order management system  
✅ Firebase authentication (Email/Password + Google)  
✅ Admin dashboard  
✅ Product management (CRUD)  
✅ Order status updates  
✅ Responsive RTL design  
✅ Full Arabic language support  

## Database Schema
- **sheep**: Product listings (name, category, price, images, etc.)
- **orders**: Customer orders with items and delivery info
- **admins**: Admin user management
- **discounts**: Time-based discount system

## Default Admin
Email: `bouazzasalah120120@gmail.com`

To add more admins, run SQL:
```sql
INSERT INTO admins (id, email, role, addedAt) 
VALUES ('admin_' || hex(randomblob(16)), 'your-email@example.com', 'primary', datetime('now'));
```

## Firebase Setup Required
The Firebase configuration in `assets/js/firebase-config.js` needs to be updated with your Firebase project credentials:

1. Create a Firebase project at https://console.firebase.google.com/
2. Enable Authentication → Email/Password and Google Sign-in
3. Copy your Firebase config from Project Settings
4. Update the values in `assets/js/firebase-config.js`

Current config has placeholder values that need replacement.

## API Endpoints

### Sheep (Products)
- `GET /api/sheep.php` - Get all products
- `GET /api/sheep.php/{id}` - Get single product
- `POST /api/sheep.php` - Create product (admin only)
- `PUT /api/sheep.php/{id}` - Update product (admin only)
- `DELETE /api/sheep.php/{id}` - Delete product (admin only)

### Orders
- `GET /api/orders.php` - Get all orders
- `GET /api/orders.php/{id}` - Get single order
- `POST /api/orders.php` - Create order
- `PUT /api/orders.php/{id}` - Update order status

### Admins
- `GET /api/admins.php` - Get all admins
- `GET /api/admins.php?email=xxx` - Check admin status
- `POST /api/admins.php` - Add admin (admin only)
- `DELETE /api/admins.php` - Remove admin (admin only)

## Development Notes

### Running the Application
The PHP server is configured to run automatically on port 5000. The webview will display the application.

### Database Management
To reinitialize the database:
```bash
rm database.sqlite
php api/init_database.php
```

### Adding Sample Data
The database contains a sample product for testing. You can add more products through:
1. The admin panel (after setting up Firebase and logging in)
2. Direct SQL inserts via SQLite
3. Using the API endpoints with proper authentication

## Production Deployment Considerations
When deploying to production (outside Replit):

1. **CORS**: Update CORS settings in `api/config.php` to restrict to your domain
2. **Firebase**: Ensure Firebase credentials are properly configured
3. **HTTPS**: SSL/TLS certificate required for Firebase auth
4. **Database**: Consider migrating back to MySQL/PostgreSQL for production scale
5. **Security**: Review all security settings in deployment checklist

## User Preferences
- None documented yet

## Known Issues
- Firebase credentials need to be configured (currently using placeholders)
- Favicon.ico missing (causes 404 in logs, cosmetic only)
- Category images are currently hosted externally (from odhiyati.vercel.app) - consider hosting locally for production
