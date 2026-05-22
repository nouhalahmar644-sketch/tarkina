UPDATE hebergement SET 
  titre = CONVERT(BINARY CONVERT(titre USING latin1) USING utf8mb4),
  description = CONVERT(BINARY CONVERT(description USING latin1) USING utf8mb4),
  localisation = CONVERT(BINARY CONVERT(localisation USING latin1) USING utf8mb4),
  inclus = CONVERT(BINARY CONVERT(inclus USING latin1) USING utf8mb4);

UPDATE repas SET 
  titre = CONVERT(BINARY CONVERT(titre USING latin1) USING utf8mb4),
  description = CONVERT(BINARY CONVERT(description USING latin1) USING utf8mb4),
  localisation = CONVERT(BINARY CONVERT(localisation USING latin1) USING utf8mb4),
  inclus = CONVERT(BINARY CONVERT(inclus USING latin1) USING utf8mb4);

UPDATE guide SET 
  titre = CONVERT(BINARY CONVERT(titre USING latin1) USING utf8mb4),
  description = CONVERT(BINARY CONVERT(description USING latin1) USING utf8mb4),
  localisation = CONVERT(BINARY CONVERT(localisation USING latin1) USING utf8mb4),
  inclus = CONVERT(BINARY CONVERT(inclus USING latin1) USING utf8mb4);

UPDATE artisanat SET 
  titre = CONVERT(BINARY CONVERT(titre USING latin1) USING utf8mb4),
  description = CONVERT(BINARY CONVERT(description USING latin1) USING utf8mb4),
  localisation = CONVERT(BINARY CONVERT(localisation USING latin1) USING utf8mb4);

UPDATE evenement SET 
  titre = CONVERT(BINARY CONVERT(titre USING latin1) USING utf8mb4),
  description = CONVERT(BINARY CONVERT(description USING latin1) USING utf8mb4),
  localisation = CONVERT(BINARY CONVERT(localisation USING latin1) USING utf8mb4),
  inclus = CONVERT(BINARY CONVERT(inclus USING latin1) USING utf8mb4);
