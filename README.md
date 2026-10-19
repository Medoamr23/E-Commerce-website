# Aura Store — PHP online store (Laragon)

## Setup
1. Copy the `aura-store` folder into `C:\laragon\www\`.
2. Start Laragon (Apache + MySQL).
3. Open HeidiSQL (Laragon > Database) and run `database.sql` (creates database `aura_store`).
4. Visit http://localhost/aura-store

## Admin login
- Email: admin@aurastore.com
- Password: admin123  -> change it right away (use "Forgot password")

## Features
- Store model: only admins add products; customers browse and buy
- Admin dashboard: stats, products (add/edit/delete + photo), orders (change status), users (make admin / delete), comment moderation, categories
- Product comments: all output escaped with htmlspecialchars (XSS-safe), CSRF protected, 1000-char limit
- Password reset with secure one-time token (expires in 1 hour)
  - DEV_MODE=true in config.php shows the reset link on screen (Laragon has no mail server by default)
  - On a real server set DEV_MODE=false and configure PHP mail()
