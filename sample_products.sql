USE aura_store;
-- add missing categories (safe to re-run)
INSERT INTO categories (name) SELECT 'Pets' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Pets');
INSERT INTO categories (name) SELECT 'Phones & Tablets' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Phones & Tablets');
INSERT INTO categories (name) SELECT 'Tools' WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name='Tools');

INSERT INTO products (category_id,title,description,price,stock) VALUES
((SELECT id FROM categories WHERE name='Books' LIMIT 1),'Atomic Habits','Build good habits and break bad ones. Paperback.',350,20),
((SELECT id FROM categories WHERE name='Books' LIMIT 1),'The Alchemist','Paulo Coelho classic novel.',180,30),
((SELECT id FROM categories WHERE name='Books' LIMIT 1),'Clean Code','A handbook of agile software craftsmanship.',650,10),
((SELECT id FROM categories WHERE name='Electronics' LIMIT 1),'Wireless Earbuds','Bluetooth 5.3, 24h battery, charging case.',799,25),
((SELECT id FROM categories WHERE name='Electronics' LIMIT 1),'Smart Watch','Heart-rate, sleep tracking, 7-day battery.',1499,15),
((SELECT id FROM categories WHERE name='Electronics' LIMIT 1),'Power Bank 20000mAh','Fast charging, dual USB + USB-C.',650,40),
((SELECT id FROM categories WHERE name='Fashion' LIMIT 1),'Men''s Cotton T-Shirt','100% cotton, regular fit.',250,50),
((SELECT id FROM categories WHERE name='Fashion' LIMIT 1),'Women''s Summer Dress','Light floral dress, sizes S-XL.',550,25),
((SELECT id FROM categories WHERE name='Fashion' LIMIT 1),'Sneakers','Comfortable everyday sneakers.',950,30),
((SELECT id FROM categories WHERE name='Home & Kitchen' LIMIT 1),'Non-stick Frying Pan 28cm','Induction-ready, PFOA-free.',420,30),
((SELECT id FROM categories WHERE name='Home & Kitchen' LIMIT 1),'Electric Kettle 1.7L','Auto shut-off, stainless steel.',480,25),
((SELECT id FROM categories WHERE name='Home & Kitchen' LIMIT 1),'Bedsheet Set (Queen)','Soft microfiber, 4 pieces.',700,20),
((SELECT id FROM categories WHERE name='Other' LIMIT 1),'Gift Card Box','Decorative gift box with ribbon.',120,60),
((SELECT id FROM categories WHERE name='Other' LIMIT 1),'Scented Candle','Vanilla soy candle, 40h burn time.',150,40),
((SELECT id FROM categories WHERE name='Pets' LIMIT 1),'Dog Food 5kg','Complete dry food for adult dogs.',650,25),
((SELECT id FROM categories WHERE name='Pets' LIMIT 1),'Cat Scratching Post','Sisal rope, stable base.',380,20),
((SELECT id FROM categories WHERE name='Pets' LIMIT 1),'Pet Water Fountain','2L automatic, quiet pump.',520,15),
((SELECT id FROM categories WHERE name='Phones & Tablets' LIMIT 1),'Smartphone 128GB','6.5" display, 50MP camera, 5000mAh.',8999,12),
((SELECT id FROM categories WHERE name='Phones & Tablets' LIMIT 1),'Tablet 10" 64GB','Wi-Fi, 8MP camera, kids mode.',5499,10),
((SELECT id FROM categories WHERE name='Phones & Tablets' LIMIT 1),'Phone Case + Screen Protector','Shockproof bundle.',150,80),
((SELECT id FROM categories WHERE name='Sports' LIMIT 1),'Yoga Mat 6mm','Non-slip, with carry strap.',280,40),
((SELECT id FROM categories WHERE name='Sports' LIMIT 1),'Adjustable Dumbbell Set 20kg','Chrome plates, anti-slip handles.',1200,15),
((SELECT id FROM categories WHERE name='Sports' LIMIT 1),'Football Size 5','Machine-stitched match ball.',350,35),
((SELECT id FROM categories WHERE name='Tools' LIMIT 1),'Cordless Drill 18V','2 batteries, 24-piece bit set.',1850,12),
((SELECT id FROM categories WHERE name='Tools' LIMIT 1),'Tool Box 120 pcs','Wrenches, screwdrivers, pliers.',990,15),
((SELECT id FROM categories WHERE name='Tools' LIMIT 1),'Measuring Tape 5m','Magnetic tip, auto-lock.',60,100),
((SELECT id FROM categories WHERE name='Toys' LIMIT 1),'Building Blocks 500 pcs','Compatible, ages 6+.',420,30),
((SELECT id FROM categories WHERE name='Toys' LIMIT 1),'RC Racing Car','2.4GHz remote, rechargeable.',650,20),
((SELECT id FROM categories WHERE name='Toys' LIMIT 1),'Plush Teddy Bear 50cm','Soft and hug-able.',260,35);
