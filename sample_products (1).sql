USE aura_store;
-- add missing categories (safe to re-run)
INSERT INTO categories (name) SELECT 'Pets' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Pets');
INSERT INTO categories (name) SELECT 'Phones & Tablets' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Phones & Tablets');
INSERT INTO categories (name) SELECT 'Tools' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Tools');

INSERT INTO products (category_id,title,description,price,stock)
SELECT c.id,t.title,t.descr,t.price,t.stock FROM (
SELECT 'Books' AS cat,'Atomic Habits' AS title,'Build good habits and break bad ones. Paperback.' AS descr,350 AS price,20 AS stock UNION ALL
SELECT 'Books','The Alchemist','Paulo Coelho classic novel.',180,30 UNION ALL
SELECT 'Books','Clean Code','A handbook of agile software craftsmanship.',650,10 UNION ALL
SELECT 'Electronics','Wireless Earbuds','Bluetooth 5.3, 24h battery, charging case.',799,25 UNION ALL
SELECT 'Electronics','Smart Watch','Heart-rate, sleep tracking, 7-day battery.',1499,15 UNION ALL
SELECT 'Electronics','Power Bank 20000mAh','Fast charging, dual USB + USB-C.',650,40 UNION ALL
SELECT 'Fashion','Men''s Cotton T-Shirt','100% cotton, regular fit.',250,50 UNION ALL
SELECT 'Fashion','Women''s Summer Dress','Light floral dress, sizes S-XL.',550,25 UNION ALL
SELECT 'Fashion','Sneakers','Comfortable everyday sneakers.',950,30 UNION ALL
SELECT 'Home & Kitchen','Non-stick Frying Pan 28cm','Induction-ready, PFOA-free.',420,30 UNION ALL
SELECT 'Home & Kitchen','Electric Kettle 1.7L','Auto shut-off, stainless steel.',480,25 UNION ALL
SELECT 'Home & Kitchen','Bedsheet Set (Queen)','Soft microfiber, 4 pieces.',700,20 UNION ALL
SELECT 'Other','Gift Card Box','Decorative gift box with ribbon.',120,60 UNION ALL
SELECT 'Other','Scented Candle','Vanilla soy candle, 40h burn time.',150,40 UNION ALL
SELECT 'Pets','Dog Food 5kg','Complete dry food for adult dogs.',650,25 UNION ALL
SELECT 'Pets','Cat Scratching Post','Sisal rope, stable base.',380,20 UNION ALL
SELECT 'Pets','Pet Water Fountain','2L automatic, quiet pump.',520,15 UNION ALL
SELECT 'Phones & Tablets','Smartphone 128GB','6.5" display, 50MP camera, 5000mAh.',8999,12 UNION ALL
SELECT 'Phones & Tablets','Tablet 10" 64GB','Wi-Fi, 8MP camera, kids mode.',5499,10 UNION ALL
SELECT 'Phones & Tablets','Phone Case + Screen Protector','Shockproof bundle.',150,80 UNION ALL
SELECT 'Sports','Yoga Mat 6mm','Non-slip, with carry strap.',280,40 UNION ALL
SELECT 'Sports','Adjustable Dumbbell Set 20kg','Chrome plates, anti-slip handles.',1200,15 UNION ALL
SELECT 'Sports','Football Size 5','Machine-stitched match ball.',350,35 UNION ALL
SELECT 'Tools','Cordless Drill 18V','2 batteries, 24-piece bit set.',1850,12 UNION ALL
SELECT 'Tools','Tool Box 120 pcs','Wrenches, screwdrivers, pliers.',990,15 UNION ALL
SELECT 'Tools','Measuring Tape 5m','Magnetic tip, auto-lock.',60,100 UNION ALL
SELECT 'Toys','Building Blocks 500 pcs','Compatible, ages 6+.',420,30 UNION ALL
SELECT 'Toys','RC Racing Car','2.4GHz remote, rechargeable.',650,20 UNION ALL
SELECT 'Toys','Plush Teddy Bear 50cm','Soft and hug-able.',260,35
) t JOIN categories c ON c.name COLLATE utf8mb4_unicode_ci = t.cat COLLATE utf8mb4_unicode_ci;

-- check: should list 10 categories with counts
SELECT c.name, COUNT(p.id) AS products FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id;
