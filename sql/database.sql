CREATE DATABASE eglise_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; USE
    eglise_db;
    -- =========================
    -- TABLE USERS
    -- =========================
CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    PASSWORD VARCHAR(255) NOT NULL,
    role ENUM('admin', 'visiteur') DEFAULT 'visiteur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- =========================
-- TABLE FIDELES
-- =========================
CREATE TABLE fideles(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    postnom VARCHAR(100),
    prenom VARCHAR(100),
    sexe ENUM('M', 'F') NOT NULL,
    telephone VARCHAR(30),
    adresse TEXT,
    date_naissance DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- =========================
-- TABLE CULTES
-- =========================
CREATE TABLE cultes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    theme VARCHAR(255) NOT NULL,
    passage_biblique VARCHAR(255),
    orateur VARCHAR(150),
    interprete VARCHAR(150),
    hommes INT DEFAULT 0,
    femmes INT DEFAULT 0,
    offrande DECIMAL(10, 2) DEFAULT 0,
    dime DECIMAL(10, 2) DEFAULT 0,
    sociale DECIMAL(10, 2) DEFAULT 0,
    autres DECIMAL(10, 2) DEFAULT 0,
    date_culte DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- =========================
-- TABLE DEPENSES
-- =========================
CREATE TABLE depenses(
    id INT AUTO_INCREMENT PRIMARY KEY,
    motif VARCHAR(255) NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    beneficiaire VARCHAR(150),
    date_depense DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- =========================
-- TABLE FONDS
-- =========================
CREATE TABLE fonds(
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(10, 2) NOT NULL,
    motif TEXT NOT NULL,
    statut ENUM('en attente', 'valide', 'refuse') DEFAULT 'en attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- =========================
-- TABLE ANNONCES
-- =========================
CREATE TABLE annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);