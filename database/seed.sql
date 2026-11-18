-- GymShop demo data. Import after schema.sql.
-- Demo accounts:
--   admin@gymshop.test  / Admin123!
--   client@gymshop.test / Client123!

SET NAMES utf8mb4;

INSERT INTO roles (id, name) VALUES
    (1, 'admin'),
    (2, 'customer');

INSERT INTO users (id, role_id, full_name, email, password_hash) VALUES
    (1, 1, 'Administrator GymShop', 'admin@gymshop.test', '$2y$10$IUicD.bJRs7md7D9.AY5ZOZJGpLq9rcml0YXb2FK5EDTATmuYvr/q'),
    (2, 2, 'Andrei Popescu', 'client@gymshop.test', '$2y$10$HBeTCjiW4pStGPN9ERFiYurS2ja1qQZlAYaLccTLOdS5//ujFA1UG');

INSERT INTO categories (id, name, slug, description) VALUES
    (1, 'Creatină', 'creatina', 'Creatină monohidrat și creatină micronizată pentru forță și putere.'),
    (2, 'Proteine', 'proteine', 'Proteine din zer, izolate și vegane pentru recuperare și masă musculară.'),
    (3, 'Pre-Workout', 'pre-workout', 'Formule cu cofeină, beta-alanină și citrulină pentru antrenamente intense.'),
    (4, 'Aminoacizi', 'aminoacizi', 'BCAA, EAA și glutamină pentru recuperare.'),
    (5, 'Vitamine și minerale', 'vitamine-si-minerale', 'Multivitamine, magneziu, zinc și omega-3.');

INSERT INTO brands (id, name, country) VALUES
    (1, 'IronCore Nutrition', 'România'),
    (2, 'PureForm Labs', 'Germania'),
    (3, 'TitanFuel', 'SUA'),
    (4, 'NordStrength', 'Suedia');

INSERT INTO products (id, category_id, brand_id, name, slug, description, price, stock, weight_grams, servings, image_path) VALUES
    (1, 1, 1, 'Creatină Monohidrat 300g', 'creatina-monohidrat-300g',
     'Creatină monohidrat pură, micronizată, fără aditivi. 5g pe zi susțin creșterea forței și a performanței la eforturi scurte și intense.',
     89.99, 50, 300, 60, 'assets/images/products/creatina-monohidrat-300g.svg'),
    (2, 1, 2, 'Creatină Creapure 500g', 'creatina-creapure-500g',
     'Creatină monohidrat Creapure® produsă în Germania, puritate de 99,9%. Se dizolvă ușor în apă sau în shake-ul proteic.',
     149.99, 35, 500, 100, 'assets/images/products/creatina-creapure-500g.svg'),
    (3, 1, 3, 'Creatină HCl Capsule', 'creatina-hcl-capsule',
     'Creatină hidroclorică în capsule, absorbție rapidă și fără balonare. Ideală pentru cei care preferă capsulele.',
     119.50, 20, 180, 90, 'assets/images/products/creatina-hcl-capsule.svg'),
    (4, 2, 1, 'Whey Protein Ciocolată 2kg', 'whey-protein-ciocolata-2kg',
     'Concentrat de proteină din zer cu 24g proteină pe porție și gust intens de ciocolată. Perfectă după antrenament.',
     219.99, 40, 2000, 66, 'assets/images/products/whey-protein-ciocolata-2kg.svg'),
    (5, 2, 4, 'Whey Isolate Vanilie 1kg', 'whey-isolate-vanilie-1kg',
     'Izolat de proteină din zer cu peste 90% proteină, aproape fără lactoză și grăsimi. Gust de vanilie bourbon.',
     189.00, 25, 1000, 33, 'assets/images/products/whey-isolate-vanilie-1kg.svg'),
    (6, 2, 2, 'Proteină Vegană Mix 750g', 'proteina-vegana-mix-750g',
     'Amestec de proteină din mazăre și orez brun, profil complet de aminoacizi, potrivit pentru dietele vegane.',
     139.99, 18, 750, 25, 'assets/images/products/proteina-vegana-mix-750g.svg'),
    (7, 3, 3, 'Pre-Workout Blast Fructe de pădure', 'pre-workout-blast-fructe-de-padure',
     'Formulă pre-antrenament cu 200mg cofeină, beta-alanină și L-citrulină pentru energie, focus și pump.',
     129.99, 30, 390, 30, 'assets/images/products/pre-workout-blast-fructe-de-padure.svg'),
    (8, 3, 4, 'Pump Fără Stimulente', 'pump-fara-stimulente',
     'Pre-workout fără cofeină, cu citrulină malat și nitrați din sfeclă. Potrivit pentru antrenamentele de seară.',
     109.99, 0, 350, 25, 'assets/images/products/pump-fara-stimulente.svg'),
    (9, 4, 1, 'BCAA 2:1:1 Lămâie 400g', 'bcaa-211-lamaie-400g',
     'Aminoacizi cu catenă ramificată în raport 2:1:1, cu electroliți. Gust răcoritor de lămâie.',
     99.99, 45, 400, 40, 'assets/images/products/bcaa-211-lamaie-400g.svg'),
    (10, 4, 2, 'EAA Complex Mango', 'eaa-complex-mango',
     'Toți cei 9 aminoacizi esențiali într-o formulă cu absorbție rapidă, ideală în timpul antrenamentului.',
     114.99, 22, 300, 30, 'assets/images/products/eaa-complex-mango.svg'),
    (11, 5, 4, 'Magneziu Bisglicinat 120 Capsule', 'magneziu-bisglicinat-120-capsule',
     'Magneziu chelat cu biodisponibilitate ridicată, sprijină funcția musculară și reduce oboseala.',
     59.99, 60, 150, 120, 'assets/images/products/magneziu-bisglicinat-120-capsule.svg'),
    (12, 5, 3, 'Omega-3 Ulei de Pește 90 Capsule', 'omega-3-ulei-de-peste-90-capsule',
     'Ulei de pește purificat cu EPA și DHA, susține sănătatea inimii și a articulațiilor.',
     69.99, 38, 140, 90, 'assets/images/products/omega-3-ulei-de-peste-90-capsule.svg');

INSERT INTO orders (id, user_id, status, shipping_address, phone, created_at) VALUES
    (1, 2, 'delivered', 'Str. Exemplu nr. 10, București', '0712345678', '2026-10-02 14:25:00'),
    (2, 2, 'pending', 'Str. Exemplu nr. 10, București', '0712345678', '2026-11-10 09:12:00');

INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES
    (1, 1, 2, 89.99),
    (1, 4, 1, 219.99),
    (2, 7, 1, 129.99);
