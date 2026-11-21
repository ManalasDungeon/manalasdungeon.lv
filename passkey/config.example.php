<?php
/**
 * Passkey-kirjautumisen asetukset — MALLI.
 * Kopioi tiedostoksi config.php ja täytä omat arvot. config.php EI kuulu versionhallintaan.
 * Tämä tiedosto on PHP:tä, joten selain ei näe sen sisältöä (lisäksi .htaccess estää suoran haun).
 */
return [
    // Verkkotunnus, johon passkeyt sidotaan (ilman https:// ja ilman www:tä).
    'rp_id'   => 'manalasdungeon.lv',
    'rp_name' => 'Manalas Dungeon',

    // Sallitut alkuperät. .htaccess ohjaa www → ei-www, joten yksi riittää.
    'origins' => ['https://manalasdungeon.lv'],

    // Näkyy laitteen passkey-valikossa.
    'user_name'    => 'admin@manalasdungeon.lv',
    'user_display' => 'Marko Nakari (ylläpito)',

    // Tietokantataulu (luodaan schema.sql:llä).
    'table' => 'admin_passkeys',

    /*
     * OMA TIETOKANTA (valinnainen).
     * Tyhjä 'dsn' = käytetään sivuston db_config.php:n yhteyttä (sama kanta kuin zinas-taulu).
     * Täytetty  = passkey-taulu on erillisessä kannassa. Käyttäjällä pitää olla
     * cPanelissa oikeudet tähän kantaan (MySQL Databases → Add User To Database).
     */
    'db' => [
        'dsn'  => '',   // esim. 'mysql:host=localhost;dbname=manalasd_passkey;charset=utf8mb4'
        'user' => '',   // kannan käyttäjä, esim. sama kuin db_config.php:ssä
        'pass' => '',   // käyttäjän salasana
    ],

    /*
     * ENSIMMÄISEN LAITTEEN ASENNUSAVAIN.
     * Vaihda pitkäksi satunnaiseksi merkkijonoksi (väh. 20 merkkiä), esim. salasanageneraattorilla.
     * Toimii VAIN niin kauan kuin yhtään laitetta ei ole rekisteröity — sen jälkeen
     * uudet laitteet lisätään kirjautuneena hallintasivulta.
     * Jos kaikki laitteet katoavat: poista rivit phpMyAdminissa taulusta admin_passkeys,
     * jolloin asennusavain toimii taas.
     */
    'setup_token' => 'VAIHDA-PITKAKSI-SATUNNAISEKSI-MERKKIJONOKSI',

    // Istunnon aikakatkaisut (sekunteina): 30 min toimettomuus, 8 h enintään.
    'idle_timeout'     => 1800,
    'absolute_timeout' => 28800,

    // Evästeen Secure-lippu (aina true tuotannossa, sivusto on HTTPS).
    'cookie_secure' => true,

    // Linkit hallintasivulla.
    'admin_pages' => [
        'Vieraskirjan hallinta' => '/viesu_admin.php',
        'Ziņas & Ceļojums'      => '/blogi_admin.php',
    ],
];
