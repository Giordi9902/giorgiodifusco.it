-- Statistiche del blog per la dashboard admin (visualizzazioni e sorgenti di traffico).
-- Solo contatori aggregati per giorno: nessun IP, cookie o dato personale dei visitatori.
--
-- Da eseguire una volta sul DB di produzione (es. da phpMyAdmin → SQL).
-- Finché non viene eseguita il sito funziona lo stesso: le visite non vengono
-- conteggiate e la dashboard mostra un avviso.

CREATE TABLE IF NOT EXISTS `blog_post_views` (
  `post_id` int NOT NULL,
  `view_date` date NOT NULL,
  `views` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`, `view_date`),
  KEY `idx_blog_post_views_date` (`view_date`),
  CONSTRAINT `fk_blog_post_views_post` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `blog_traffic_sources` (
  `view_date` date NOT NULL,
  `source` varchar(100) NOT NULL,
  `views` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`view_date`, `source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
