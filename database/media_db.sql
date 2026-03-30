-- Core tables
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  profile_image VARCHAR(255) DEFAULT NULL,
  last_login TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt TEXT,
  content LONGTEXT NOT NULL,
  image_url VARCHAR(255),
  user_id INT NOT NULL,
  category_id INT,
  status ENUM('draft','published') DEFAULT 'draft',
  views INT DEFAULT 0,
  published_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_articles_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
  setting_key VARCHAR(80) PRIMARY KEY,
  setting_value TEXT
);

CREATE TABLE equipe (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  name VARCHAR(120) NOT NULL,
  role VARCHAR(120) NOT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  bio TEXT,
  social VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_equipe_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE about_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  intro TEXT,
  history TEXT,
  mission TEXT,
  vision TEXT,
  method TEXT,
  about_values TEXT,
  distinctiveness TEXT,
  cta_text VARCHAR(150),
  cta_link VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional advanced tables
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) DEFAULT NULL,
  content TEXT NOT NULL,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_article FOREIGN KEY (article_id) REFERENCES articles(id)
);

CREATE TABLE tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE article_tags (
  article_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (article_id, tag_id),
  CONSTRAINT fk_article_tags_article FOREIGN KEY (article_id) REFERENCES articles(id),
  CONSTRAINT fk_article_tags_tag FOREIGN KEY (tag_id) REFERENCES tags(id)
);

CREATE TABLE permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE role_permissions (
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id),
  CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id)
);

-- Seed roles
INSERT INTO roles (id, name, description) VALUES
(1, 'admin', 'Accès total'),
(2, 'editor', 'Gère les articles'),
(3, 'journalist', 'Écrit seulement'),
(4, 'moderator', 'Gère commentaires');

-- Seed users (password: admin123)
INSERT INTO users (name, email, password, role_id, status) VALUES
('Admin', 'admin@media360.ht', '$2y$12$1nzutVDcz4YyQCXtG4sVneGga8sGArBWjRu4oIVyLd7sH.pQgc6Zi', 1, 'active'),
('Editeur', 'editor@media360.ht', '$2y$12$1nzutVDcz4YyQCXtG4sVneGga8sGArBWjRu4oIVyLd7sH.pQgc6Zi', 2, 'active'),
('Journaliste', 'journaliste@media360.ht', '$2y$12$1nzutVDcz4YyQCXtG4sVneGga8sGArBWjRu4oIVyLd7sH.pQgc6Zi', 3, 'active');

-- Seed categories
INSERT INTO categories (name, slug) VALUES
('Politique', 'politique'),
('Sport', 'sport'),
('Tech', 'tech'),
('Culture', 'culture'),
('Économie', 'economie'),
('Société', 'societe');

-- Seed articles
INSERT INTO articles (title, slug, excerpt, content, image_url, user_id, category_id, status, views, published_at) VALUES
('L’économie locale en reprise', 'economie-locale-reprise', 'Résumé de l’actualité économique locale.', 'Contenu complet de l’article sur l’économie locale.', '', 1, 5, 'published', 120, NOW()),
('Le sport haïtien en pleine évolution', 'sport-haitien-evolution', 'Retour sur les performances sportives récentes.', 'Contenu complet de l’article sportif.', '', 2, 2, 'published', 95, NOW()),
('Nouvelles tendances tech', 'tendances-tech', 'Focus sur les innovations technologiques.', 'Contenu complet de l’article tech.', '', 2, 3, 'published', 70, NOW()),
('Dossier culture et patrimoine', 'culture-patrimoine', 'Mise en avant des initiatives culturelles.', 'Contenu complet de l’article culture.', '', 3, 4, 'draft', 10, NULL);

-- Seed messages
INSERT INTO messages (name, email, subject, message) VALUES
('Jean Pierre', 'jean@example.com', 'Partenariat', 'Bonjour, je souhaite discuter d’un partenariat.'),
('Nadia', 'nadia@example.com', 'Suggestion', 'J’aimerais proposer un sujet sur la jeunesse.');

-- Seed settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Media360'),
('site_description', 'Média indépendant pour l’info locale.'),
('contact_email', 'contact@media360.ht'),
('contact_phone', '+509 0000 0000'),
('social_facebook', 'https://facebook.com/media360'),
('social_whatsapp', 'https://wa.me/50900000000'),
('social_x', 'https://x.com/media360'),
('footer_text', 'Media360 — Tous droits réservés.');
