-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03/12/2025 às 02:15
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados:  reciclatech 
--

-- --------------------------------------------------------

--
-- Estrutura para tabela  devices 
--

CREATE TABLE devices (
   id  int(11) NOT NULL,
   user_id  int(11) DEFAULT NULL,
   device_type  varchar(100) NOT NULL,
   brand  varchar(100) DEFAULT NULL,
   model  varchar(100) DEFAULT NULL,
   device_condition  enum('novo','bom','funcional','com_defeito','para_pecas') DEFAULT 'funcional',
   description  text DEFAULT NULL,
   photo  varchar(255) DEFAULT NULL,
   status  enum('available','reserved','donated') DEFAULT 'available',
   created_at  timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela  donations 
--

CREATE TABLE  donations  (
   id  int(11) NOT NULL,
   device_id  int(11) NOT NULL,
   donor_name  varchar(150) DEFAULT NULL,
   donor_email  varchar(150) DEFAULT NULL,
   pickup_address  varchar(255) DEFAULT NULL,
   status  enum('pending','scheduled','completed','cancelled') DEFAULT 'pending',
   created_at  timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela  points_config 
--

CREATE TABLE  points_config  (
   action_key  varchar(50) NOT NULL,
   points_value  int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela  points_config 
--

INSERT INTO  points_config  ( action_key ,  points_value ) VALUES
('doacao_completa', 50),
('reserva_aprovada', 30);

-- --------------------------------------------------------

--
-- Estrutura para tabela  reservations 
--

CREATE TABLE  reservations  (
   id  int(11) NOT NULL,
   device_id  int(11) NOT NULL,
   adopter_name  varchar(150) NOT NULL,
   adopter_email  varchar(150) NOT NULL,
   purpose  text DEFAULT NULL,
   status  enum('pending','approved','rejected','completed') DEFAULT 'pending',
   created_at  timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela  users 
--

CREATE TABLE  users  (
   id  int(11) NOT NULL,
   name  varchar(100) NOT NULL,
   email  varchar(150) NOT NULL,
   password_hash  varchar(255) NOT NULL,
   role  enum('admin','user') DEFAULT 'user',
   created_at  timestamp NOT NULL DEFAULT current_timestamp(),
   points  int(11) DEFAULT 0,
   address_street  varchar(255) DEFAULT NULL,
   address_number  varchar(10) DEFAULT NULL,
   address_complement  varchar(255) DEFAULT NULL,
   address_city  varchar(100) DEFAULT NULL,
   address_state  varchar(100) DEFAULT NULL,
   address_zipcode  varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela  users 
--

INSERT INTO  users  ( id ,  name ,  email ,  password_hash ,  role ,  created_at ,  points ,  address_street ,  address_number ,  address_complement ,  address_city ,  address_state ,  address_zipcode ) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$a9tICIsWWGcvUibHvH01TeDDnoUpWjByttptjK6Sa6eocGySSPv/y', 'admin', '2025-11-29 02:07:14', 0, 'Ana Duarte', '11', 'Ello', 'Iguatu', 'CE', '63503830');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela  devices 
--
ALTER TABLE  devices 
  ADD PRIMARY KEY ( id ),
  ADD KEY  user_id  ( user_id );

--
-- Índices de tabela  donations 
--
ALTER TABLE  donations 
  ADD PRIMARY KEY ( id ),
  ADD KEY  device_id  ( device_id );

--
-- Índices de tabela  points_config 
--
ALTER TABLE  points_config 
  ADD PRIMARY KEY ( action_key );

--
-- Índices de tabela  reservations 
--
ALTER TABLE  reservations 
  ADD PRIMARY KEY ( id ),
  ADD KEY  device_id  ( device_id );

--
-- Índices de tabela  users 
--
ALTER TABLE  users 
  ADD PRIMARY KEY ( id ),
  ADD UNIQUE KEY  email  ( email );

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela  devices 
--
ALTER TABLE  devices 
  MODIFY  id  int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela  donations 
--
ALTER TABLE  donations 
  MODIFY  id  int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela  reservations 
--
ALTER TABLE  reservations 
  MODIFY  id  int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela  users 
--
ALTER TABLE  users 
  MODIFY  id  int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas  devices 
--
ALTER TABLE  devices 
  ADD CONSTRAINT  devices_ibfk_1  FOREIGN KEY ( user_id ) REFERENCES  users  ( id ) ON DELETE SET NULL;

--
-- Restrições para tabelas  donations 
--
ALTER TABLE  donations 
  ADD CONSTRAINT  donations_ibfk_1  FOREIGN KEY ( device_id ) REFERENCES  devices  ( id ) ON DELETE CASCADE;

--
-- Restrições para tabelas  reservations 
--
ALTER TABLE  reservations 
  ADD CONSTRAINT  reservations_ibfk_1  FOREIGN KEY ( device_id ) REFERENCES  devices  ( id ) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
