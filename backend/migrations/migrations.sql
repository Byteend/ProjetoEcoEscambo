-- SQLite schema for EcoEscambo (moved)
BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  activated INTEGER NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  description TEXT,
  status TEXT NOT NULL DEFAULT 'aberto', -- 'aberto' or 'finalizada'
  image TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS interests (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL, -- product that received interest
  user_id INTEGER NOT NULL, -- user who expressed interest
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(product_id) REFERENCES products(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS proposals (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  interest_id INTEGER NOT NULL, -- reference to interest
  ofertante_product_id INTEGER NOT NULL, -- product proposed by ofertante
  status TEXT NOT NULL DEFAULT 'pending', -- pending, accepted, rejected
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(interest_id) REFERENCES interests(id),
  FOREIGN KEY(ofertante_product_id) REFERENCES products(id)
);

COMMIT;
