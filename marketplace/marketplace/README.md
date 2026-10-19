# Souq Market — PHP marketplace (Laragon)

## Setup
1. Copy the `marketplace` folder into `C:\laragon\www\`.
2. Start Laragon (Apache + MySQL).
3. Open **HeidiSQL** (Laragon > Database) or phpMyAdmin, and run `database.sql`.
4. Visit http://localhost/marketplace (or http://marketplace.test).

Database settings are in `config.php` (Laragon default: user `root`, no password).
Change `CURRENCY` there too.

## Features
- Register / login / logout (hashed passwords, CSRF protection)
- Post listings with photo upload, delete your own listings, see your sales
- Search, categories, sort by price
- Cart (session), checkout (cash on delivery), stock reduces automatically, order history
