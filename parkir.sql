--
-- AUTHOR  : Fanny Hasbi
-- WEBSITE : https://fannyhasbi.github.io
-- GITHUB  : https://github.com/fannyhasbi
--


-- 
-- Data Definiton Language
-- CREATING TABLES
-- 

CREATE TABLE owner (
  id INT NOT NULL AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  nim VARCHAR(20) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE vehicle (
  id INT NOT NULL AUTO_INCREMENT,
  plat VARCHAR(10) NOT NULL,
  merk VARCHAR(50) NOT NULL,
  tipe VARCHAR(50) NOT NULL,
  kode_qr VARCHAR(100) NOT NULL,
  id_owner INT NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_owner) REFERENCES owner(id)
);

CREATE TABLE place (
  id INT NOT NULL AUTO_INCREMENT,
  nama VARCHAR(50) NOT NULL,
  jurusan VARCHAR(50) NOT NULL,
  fakultas VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE officer (
  id INT NOT NULL AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255),
  id_place INT NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_place) REFERENCES place(id)
);

CREATE TABLE scan (
  id INT NOT NULL AUTO_INCREMENT,
  id_vehicle INT NOT NULL,
  id_officer INT NOT NULL,
  waktu TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (id_vehicle) REFERENCES vehicle(id),
  FOREIGN KEY (id_officer) REFERENCES officer(id)
);


-- 
-- Data Manipulation Language
-- Inserting Data
-- Comment/Uncomment these lines as you will

INSERT INTO owner (nama, nim) VALUES
('Fanny Hasbi', '68'),
('Dimas Luhur', '72'),
('Abda Rafi', '13'),
('Adam Maulidani', '60');

INSERT INTO vehicle (plat, merk, tipe, kode_qr, id_owner) VALUES
('G1234AAG', 'Honda', 'Vario', 'd90073c58963e77518ddf3aa1453fc59', 1);

INSERT INTO place (nama, jurusan, fakultas) VALUES
('Parkiran GKB', 'S1 Teknik Komputer', 'Teknik'),
('Parkiran Fakultas Hukum', 'S1 Ilmu Hukum', 'Hukum'),
('Parkiran Kedokteran', 'S1 Kedokteran Umum', 'Kedokteran');

INSERT INTO officer (nama, username, password, id_place) VALUES
('Sueb', 'sueb', '$2y$10$Wl08vpxbi4di6n4GVwBVW.STEt6MyBWeqSR1myj5QCrqmPhYl7PUi', 1),
('Parman', 'parman', '$2y$10$Wl08vpxbi4di6n4GVwBVW.STEt6MyBWeqSR1myj5QCrqmPhYl7PUi', 1);
