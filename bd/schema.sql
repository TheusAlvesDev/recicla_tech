-- Crie um banco chamado reciclatech e execute as instruções abaixo
CREATE DATABASE IF NOT EXISTS reciclatech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reciclatech;

-- ALTER TABLE users (Adicionar pontos)
ALTER TABLE users ADD COLUMN points INT DEFAULT 0;

-- 1. Tabela de Usuários (users)
-- Garante que a tabela 'users' só será criada se ainda não existir.
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabela de Dispositivos (devices)
-- Garante que a tabela 'devices' só será criada se ainda não existir.
CREATE TABLE IF NOT EXISTS devices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  device_type VARCHAR(100) NOT NULL,
  brand VARCHAR(100),
  model VARCHAR(100),
  -- RENOMEADO 'condition' para 'device_condition'
  device_condition ENUM('novo','bom','funcional','com_defeito','para_pecas') DEFAULT 'funcional',
  description TEXT,
  photo VARCHAR(255) NULL,
  status ENUM('available','reserved','donated') DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 3. Tabela de Doações (donations)
-- Garante que a tabela 'donations' só será criada se ainda não existir.
CREATE TABLE IF NOT EXISTS donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  device_id INT NOT NULL,
  donor_name VARCHAR(150),
  donor_email VARCHAR(150),
  pickup_address VARCHAR(255),
  status ENUM('pending','scheduled','completed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);

-- 4. Tabela de Reservas (reservations)
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  device_id INT NOT NULL,
  adopter_name VARCHAR(150) NOT NULL,
  adopter_email VARCHAR(150) NOT NULL,
  purpose TEXT,
  status ENUM('pending','approved','rejected','completed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS points_config (
  action_key VARCHAR(50) PRIMARY KEY,
  points_value INT NOT NULL
);

-- Inserção dos valores iniciais de gamificação
INSERT IGNORE INTO points_config (action_key, points_value) VALUES 
('doacao_completa', 50),
('reserva_aprovada', 30);

-- 4. Inserção de um Administrador Inicial (Opcional, mas recomendado)
-- Lembre-se de substituir 'SEU_HASH_AQUI' pelo hash seguro da senha.
/*
INSERT IGNORE INTO users (name, email, password_hash, role) VALUES 
('Admin ReciclaTech', 'admin@reciclatech.com', 'SEU_HASH_AQUI', 'admin');
*/