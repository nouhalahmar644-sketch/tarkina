SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

DELETE FROM hebergement;
DELETE FROM repas;
DELETE FROM guide;
DELETE FROM artisanat;
DELETE FROM evenement;

INSERT INTO hebergement (titre, localisation, prix, description, statut, capacite, date_debut, date_fin, inclus, photo_principale, photos_sec, region_id, created_at, updated_at) VALUES
('Dar Sidi — Maison d\'hôtes traditionnelle', 'Sidi Bou Saïd', 180, 'Une demeure andalouse authentique avec vue sur le golfe de Tunis. Chambres décorées à la main, petit-déjeuner tunisien inclus.', 'actif', 4, '2025-01-01', '2026-12-31', 'Petit-déjeuner, Wi-Fi, Parking', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600', '', 2, NOW(), NOW()),
('Riad Djerba — Vue mer', 'Djerba', 220, 'Riad traditionnel à deux pas de la plage. Piscine privée, terrasse avec hamacs et cuisine locale sur demande.', 'actif', 6, '2025-01-01', '2026-12-31', 'Piscine, Petit-déjeuner, Wi-Fi', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600', '', 3, NOW(), NOW()),
('Gîte Kairouan — Médina', 'Kairouan', 120, 'Logement au coeur de la médina, à 5 minutes de la Grande Mosquée. Déco berbère authentique.', 'actif', 3, '2025-01-01', '2026-12-31', 'Wi-Fi, Thé d\'accueil', 'https://images.unsplash.com/photo-1548013146-72479768bada?w=600', '', 4, NOW(), NOW()),
('Campement Tozeur — Nuit sous les étoiles', 'Tozeur', 95, 'Tentes berbères confortables aux portes du Sahara. Dîner traditionnel au feu de camp inclus.', 'actif', 8, '2025-01-01', '2026-12-31', 'Dîner, Petit-déjeuner, Draps', 'https://images.unsplash.com/photo-1537225228614-56cc3556d7ed?w=600', '', 5, NOW(), NOW()),
('Maison Takrouna — Vue panoramique', 'Takrouna', 110, 'Maison en pierre perchée sur le piton rocheux avec vue à 360° sur la plaine.', 'actif', 4, '2025-01-01', '2026-12-31', 'Petit-déjeuner berbère, Wi-Fi', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600', '', 6, NOW(), NOW()),
('Dar Kessra — Montagne', 'Kessra', 85, 'Maison montagnarde en pierres centenaires. Ambiance authentique, air pur et hospitalité berbère.', 'actif', 5, '2025-01-01', '2026-12-31', 'Petit-déjeuner, Produits locaux', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=600', '', 7, NOW(), NOW());

INSERT INTO repas (titre, localisation, prix, description, capacite, date_debut, date_fin, inclus, photo_principale, photos_sec, statut, region_id, created_at, updated_at) VALUES
('Déjeuner chez Fatma — Cuisine du terroir', 'Sidi Bou Saïd', 45, 'Repas traditionnel fait maison : brik, couscous, pâtisseries maison. Servi en terrasse avec vue sur la mer.', 6, '2025-01-01', '2026-12-31', 'Entrée, Plat, Dessert, Thé', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600', '', 'actif', 2, NOW(), NOW()),
('Table djerbienne — Fruits de mer', 'Djerba', 65, 'Déjeuner de fruits de mer frais pêchés le matin même. Recettes transmises de génération en génération.', 8, '2025-01-01', '2026-12-31', 'Poisson du jour, Salade, Dessert', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600', '', 'actif', 3, NOW(), NOW()),
('Dîner médiéval — Kairouan', 'Kairouan', 40, 'Dîner au coeur de la médina : ojja, couscous au mouton, makroudh maison.', 10, '2025-01-01', '2026-12-31', 'Plat complet, Thé, Pâtisseries', 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600', '', 'actif', 4, NOW(), NOW()),
('Méchoui saharien — Tozeur', 'Tozeur', 55, 'Repas traditionnel au feu de bois dans une oasis de palmiers. Agneau au méchoui, salade et dattes fraîches.', 12, '2025-01-01', '2026-12-31', 'Méchoui, Salade, Dattes, Thé', 'https://images.unsplash.com/photo-1529543544282-ea669407fca3?w=600', '', 'actif', 5, NOW(), NOW());

INSERT INTO guide (titre, localisation, prix, description, capacite, date_debut, date_fin, inclus, photo_principale, photos_sec, statut, region_id, created_at, updated_at) VALUES
('Visite guidée — Sidi Bou Saïd', 'Sidi Bou Saïd', 60, 'Découvrez les ruelles bleues et blanches, les cafés historiques et les demeures andalouses avec un guide passionné.', 8, '2025-01-01', '2026-12-31', 'Guide, Thé au café des Nattes', 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=600', '', 'actif', 2, NOW(), NOW()),
('Tour de l\'île — Djerba', 'Djerba', 80, 'Journée complète : synagogue la Ghriba, village de potiers, marchés et coucher de soleil sur la plage.', 10, '2025-01-01', '2026-12-31', 'Guide, Transport, Déjeuner', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600', '', 'actif', 3, NOW(), NOW()),
('Médina de Kairouan — Visite historique', 'Kairouan', 50, 'Plongez dans 13 siècles d\'histoire : Grande Mosquée, bassins des Aghlabides, souks des tapis.', 12, '2025-01-01', '2026-12-31', 'Guide certifié, Entrées mosquée', 'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600', '', 'actif', 4, NOW(), NOW()),
('Excursion Sahara — Tozeur', 'Tozeur', 120, 'Dunes de sable, ride en dromadaire, coucher de soleil sur l\'erg et nuit en campement berbère optionnelle.', 6, '2025-01-01', '2026-12-31', 'Guide, Dromadaire, Transport 4x4', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600', '', 'actif', 5, NOW(), NOW()),
('Randonnée Takrouna', 'Takrouna', 40, 'Ascension du piton rocheux avec un guide local. Histoire du village berbère et panoramas exceptionnels.', 8, '2025-01-01', '2026-12-31', 'Guide local, Eau, Collation', 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=600', '', 'actif', 6, NOW(), NOW());

INSERT INTO artisanat (titre, localisation, prix, description, stock, photo_principale, photos_sec, statut, region_id, created_at, updated_at) VALUES
('Poterie de Nabeul — Série Sidi Bou Saïd', 'Sidi Bou Saïd', 35, 'Assiettes et bols peints à la main aux motifs bleus de Sidi Bou Saïd. Pièces uniques.', 20, 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600', '', 'actif', 2, NOW(), NOW()),
('Tapis berbère noué main — Kairouan', 'Kairouan', 280, 'Tapis authentique noué à la main par des artisanes de Kairouan. Laine naturelle, motifs géométriques.', 5, 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600', '', 'actif', 4, NOW(), NOW()),
('Vannerie palmier — Tozeur', 'Tozeur', 25, 'Paniers et objets décoratifs tressés à partir de feuilles de palmier par des artisans locaux.', 30, 'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=600', '', 'actif', 5, NOW(), NOW()),
('Bijoux berbères — Takrouna', 'Takrouna', 75, 'Colliers, bracelets et bagues en argent ornés de pierres locales. Savoir-faire berbère ancestral.', 15, 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600', '', 'actif', 6, NOW(), NOW());

INSERT INTO evenement (titre, localisation, prix, description, capacite, date_debut, date_fin, inclus, photo_principale, photos_sec, statut, region_id, created_at, updated_at) VALUES
('Festival de Sidi Bou Saïd — Musique andalouse', 'Sidi Bou Saïd', 30, 'Soirée musicale dans les jardins d\'une demeure historique. Malouf et musique arabo-andalouse.', 50, '2025-06-01', '2025-06-30', 'Entrée, Thé, Pâtisseries', 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=600', '', 'actif', 2, NOW(), NOW()),
('Nuit du henné — Djerba', 'Djerba', 45, 'Soirée traditionnelle : application de henné, musique live, danse et dîner djerbien.', 20, '2025-01-01', '2026-12-31', 'Henné, Dîner, Musique', 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=600', '', 'actif', 3, NOW(), NOW()),
('Atelier tapis — Kairouan', 'Kairouan', 55, 'Apprenez les techniques de tissage ancestrales avec une maître-artisane. Repartez avec votre création.', 6, '2025-01-01', '2026-12-31', 'Matériaux, Thé, Certificat', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?w=600', '', 'actif', 4, NOW(), NOW()),
('Lever de soleil sur les dunes — Tozeur', 'Tozeur', 70, 'Réveil à 4h, 4x4 jusqu\'aux grandes dunes, lever de soleil sur le Sahara, petit-déjeuner bédouin.', 8, '2025-01-01', '2026-12-31', 'Transport, Petit-déjeuner, Guide', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=600', '', 'actif', 5, NOW(), NOW());
