USE aura_store;

-- 1. extra product fields (all types)
ALTER TABLE products
  ADD COLUMN sku VARCHAR(60) NULL UNIQUE,
  ADD COLUMN brand VARCHAR(100) NULL,
  ADD COLUMN sale_price DECIMAL(10,2) NULL,
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1;

-- 2. sub-categories (Electronics > Phones)
ALTER TABLE categories ADD COLUMN parent_id INT NULL,
  ADD FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL;

-- 3. extra photos per product
CREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 4. flexible specs: any product type, any field (Size, Color, RAM, Author, Material...)
CREATE TABLE product_attributes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  attr_name VARCHAR(80) NOT NULL,
  attr_value VARCHAR(255) NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX (attr_name, attr_value)
);

-- 5. variants (size/color with own stock + price)
CREATE TABLE product_variants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  label VARCHAR(120) NOT NULL,        -- e.g. 'Red / XL'
  sku VARCHAR(60) NULL UNIQUE,
  price DECIMAL(10,2) NULL,           -- NULL = use product price
  stock INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 6. more categories
INSERT INTO categories (name) VALUES ('Phones & Tablets'),('Computers'),('Groceries'),('Health'),('Automotive'),('Tools'),('Garden'),('Pets'),('Baby'),('Office');
