-- Matkapäivä 1 (17.10.2026), TÄYSI versio: lautta -> Tallinn/Karnaluks ->
-- Ainaži -> Salacgrīva Tops -> Viada -> Vitrupe -> mökki Mežupes.
-- Latvia + suomi, viiva välissä. Korvaa aamun lyhyemmän merkinnän.
--
-- Jos olet jo lisännyt aamun version administa: avaa se "Muokkaa" ja
-- korvaa teksti tällä. Tai aja alla oleva UPDATE (vaihda id oikeaksi),
-- tai lisää uutena INSERT.

-- Vaihtoehto A — päivitä olemassa oleva (korvaa <ID> merkinnän id:llä):
-- UPDATE zinas SET virsraksts='1. diena — no prāmja līdz Vitrupei', teksts='...' WHERE id=<ID>;

-- Vaihtoehto B — lisää uutena:
INSERT INTO zinas (virsraksts, teksts, kategorija, izveidots, publicets) VALUES
('1. diena — no prāmja līdz Vitrupei',
'Ceļš ir sācies! Mašīna uz prāmja, priekšgals pret Tallinu.\n\nJūra šūpoja prāmi vairāk, nekā gaidīts — gandrīz palika nelabi, bet skatiens horizontā palīdzēja, un drīz braucienu atkal varēja baudīt.\n\nTallinā pirmā pietura, protams, Karnaluks — dziju paradīze, no kuras grūti aiziet tukšām rokām. No turienes ar pāris pieturām devāmies uz Ainažiem, kur īsa atelpa jau Latvijas pusē.\n\nTālāk pietura Salacgrīvas "Tops", un pēc degvielas uzpildes "Viada" ceļš veda uz Vitrupes pludmali. Pelēkas debesis, vēja skarta jūra un gara, klusa smilšu mala — rudens Vidzemes krastā savā mierīgākajā skaistumā. Suņi priecīgi iztekājās gar ūdeni.\n\nTagad esam mājās — mājiņā "Mežupes". Silts tējas malks, un ceļa pirmā diena ir noslēgusies.\n\n⸻\n\nMatka on alkanut! Auto lautalla, keula kohti Tallinnaa.\n\nMeri keinutti laivaa enemmän kuin odotti — meinasi tulla hieman huono olo, mutta horisontin tuijottaminen auttoi, ja pian matkasta taas nautti.\n\nTallinnassa ensimmäinen pysähdys oli tietenkin Karnaluks — lankaparatiisi, josta on vaikea lähteä tyhjin käsin. Sieltä ajoimme parilla stopilla Ainaziin, jossa pikainen tauko jo Latvian puolella.\n\nSeuraava pysähdys oli Salacgrivan "Tops", ja tankkauksen ("Viada") jälkeen tie vei Vitrupen rannalle. Harmaa taivas, tuulen pieksemä meri ja pitkä, hiljainen hiekkaranta — syksyinen Vidzemen rannikko rauhallisimmillaan. Koirat juoksivat iloisina veden äärellä.\n\nNyt olemme perillä mökillä "Mežupes". Lämmin kulaus teetä, ja matkan ensimmäinen päivä on päätöksessä.',
'celojums',
'2026-10-17 20:30:00',
1);
