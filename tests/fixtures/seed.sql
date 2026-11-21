-- Kiinteä testidata: päivämäärät eivät muutu, joten kuvavertailu pysyy vakaana.
INSERT INTO zinas (virsraksts, teksts, kategorija, izveidots, publicets) VALUES
('Marraskuinen aamu', 'Tiet ohuen jään peitossa.\n\n[kuva:marraskuun_aamu.jpg]\n\nWinter is coming. https://manalasdungeon.lv', 'zina', '2026-11-14 08:00:00', 1),
('Testiuutinen kaksi', 'Toinen kappale <b>ei saa</b> näkyä HTML:nä — <script>alert(1)</script>', 'zina', '2026-11-01 12:00:00', 1),
('Piilotettu luonnos', 'Tämä ei saa näkyä julkisesti.', 'zina', '2026-11-15 12:00:00', 0),
('1. diena — Vitrupe', 'Lautta, Tallinna ja Vitrupe.', 'celojums', '2026-10-17 20:00:00', 1),
('2. diena — Valmiera', 'Valmiera ja Gauja.', 'celojums', '2026-10-18 20:00:00', 1);

INSERT INTO viesu_gramata (nosaukums, zina, saite, tips, izveidots, ip_hash, apstiprinats) VALUES
('Ilze', 'Ļoti jauka lapa! <img src=x onerror=alert(1)>', 'https://example.lv', NULL, '2026-11-10 10:00:00', 'x', 1),
('Matti', 'Terveisiä Heinolasta.', NULL, NULL, '2026-11-11 11:00:00', 'y', 1),
('Odottava', 'Tämä odottaa hyväksyntää eikä saa näkyä.', NULL, NULL, '2026-11-12 12:00:00', 'z', 0);

-- Yksi (keksitty) passkey, jotta ylläpitosivut vaativat kirjautumisen eivätkä avaa ensiasennusta
INSERT INTO admin_passkeys (credential_hash, credential_id, public_key, sign_count, label, created_at)
VALUES (REPEAT('a', 64), 'testi', 'testi', 0, 'Testilaite', '2026-11-01 00:00:00');
