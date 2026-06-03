CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(180) NOT NULL,
    profil VARCHAR(100) NOT NULL,
    personnes INT NOT NULL DEFAULT 1,
    salle VARCHAR(20) NOT NULL,
    creneau VARCHAR(50) NOT NULL,
    buffet ENUM('oui', 'non') NOT NULL DEFAULT 'non',
    date_creation DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Exemple avec un mot de passe en clair pour un test rapide :
-- INSERT INTO administrateurs (mot_de_passe) VALUES ('admin');
--
-- Version conseillee : mettre ici un mot de passe genere avec password_hash().
