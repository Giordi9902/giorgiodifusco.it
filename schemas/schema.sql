-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 31.11.39.237
-- Generation Time: Sep 25, 2026 at 05:11 PM
-- Server version: 8.0.44-35
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Sql92628_5`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_images`
--

CREATE TABLE `blog_images` (
  `id` int NOT NULL,
  `post_id` int DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blog_images`
--

INSERT INTO `blog_images` (`id`, `post_id`, `path`, `alt_text`, `created_at`) VALUES
(1, 2, 'public/uploads/blog_images/1368b66f0eb4681250aa86fb3205eab8.png', 'Server', '2026-09-19 13:59:56'),
(2, 3, 'public/uploads/blog_images/8b73ec5c4bff968efa7c977081bbf8e6.png', 'Schema di Ruffini', '2026-09-19 14:54:10'),
(3, 4, 'public/uploads/blog_images/9c5c32439740881621f9ad10c32e4ab5.png', 'Il linguaggio C', '2026-09-22 15:14:08'),
(4, NULL, 'public/uploads/blog_images/e2c3438f1114c151339d195032d35264.png', NULL, '2026-09-23 10:25:11'),
(5, NULL, 'public/uploads/blog_images/a6b59fa43ca1c6483c6ffd3a486d1717.png', 'Il ruolo del preprocessore', '2026-09-23 10:25:40'),
(6, NULL, 'public/uploads/blog_images/83335119fbf83d57a81d276f55a9443b.png', 'La compilazione', '2026-09-23 10:26:03'),
(7, NULL, 'public/uploads/blog_images/3264293b84202b23dfcb395b91343374.png', 'L\'assembler', '2026-09-23 10:26:19'),
(8, NULL, 'public/uploads/blog_images/168ad47d3cd58db65d334527f6b1dbac.png', 'Il linking', '2026-09-23 10:26:41'),
(9, NULL, 'public/uploads/blog_images/9761fc680993a89c4df24536175b1053.png', 'La fase di preprocessing', '2026-09-23 10:32:27'),
(10, NULL, 'public/uploads/blog_images/822ac7968bc9fb5eb8d0d81028de25c9.png', 'La fase di compilazione', '2026-09-23 10:32:52'),
(11, NULL, 'public/uploads/blog_images/0ffdde3f232acfac38a2118e6c6d17cf.png', 'L\'assembler', '2026-09-23 10:33:06'),
(12, NULL, 'public/uploads/blog_images/11b15056c8ff8044ecc9774701185384.png', 'Il linking', '2026-09-23 10:33:19'),
(13, 6, 'public/uploads/blog_images/503e6b7c0d4191481fc50de56adf4627.png', NULL, '2026-09-25 10:15:59'),
(14, 6, 'public/uploads/blog_images/bf4dd98de4a1d6d17b4c20900bdd60fe.png', 'Tipi interi in C', '2026-09-25 10:16:36'),
(15, 6, 'public/uploads/blog_images/9af95fdd621a5406704c3fc2ad19e64f.png', 'Operatori bitwise', '2026-09-25 10:17:01'),
(16, 6, 'public/uploads/blog_images/d8c6fbb8128288f24b7e7a8367fc28b0.png', 'Convertitore binario', '2026-09-25 10:17:43'),
(17, 6, 'public/uploads/blog_images/a45a277b1af8a538639105b6594782e1.png', 'Overflow', '2026-09-25 10:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int NOT NULL,
  `author_id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext NOT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `featured_image_id` int DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `author_id`, `course_id`, `subject_id`, `title`, `slug`, `excerpt`, `content`, `seo_title`, `seo_description`, `seo_keywords`, `featured_image_id`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(2, 3, 2, 1, 'Introduzione alle Basi di Dati e le Proprietà ACID', 'introduzione-alle-basi-di-dati-e-le-proprieta-acid', '', 'Ogni volta che apri Instagram e vedi i post dei tuoi amici, che controlli il registro elettronico per vedere i voti, o che il tuo videogioco preferito salva i tuoi progressi, dietro le quinte c\'è sempre la stessa cosa: un **database**.\n\nCon questo articolo iniziamo un percorso che ci porterà, passo dopo passo, a capire come funzionano le basi di dati e a scrivere le nostre prime query con **PostgreSQL**. Ma prima di lanciarci sul codice, fermiamoci un attimo a capire *cosa* stiamo davvero maneggiando quando parliamo di database.\n\n## Non è solo \"un posto dove si salvano le cose\"\n\nLa prima cosa da sfatare: un database **non è semplicemente un file dove butti dentro dei dati**, un po\' come una cartella piena di fogli Excel sparsi. È qualcosa di più organizzato.\n\nPossiamo definirlo così: **una collezione di dati collegati tra loro, salvati in modo permanente, che più persone e più programmi possono usare contemporaneamente.**\n\n\n![Illustrazione di un server](https://www.giorgiodifusco.it/public/uploads/blog_images/1368b66f0eb4681250aa86fb3205eab8.png)\n\n\nFacciamo un esempio concreto. Pensa al registro elettronico della tua scuola:\n\n- I tuoi voti sono collegati alla materia, e la materia è collegata al professore che la insegna;\n- Contemporaneamente, mentre il tuo professore inserisce un\'interrogazione, la segreteria sta controllando le assenze di un altro studente;\n- Se domani cambi scuola, i tuoi dati non spariscono: restano lì, salvati, anche se tu chiudi il browser.\n\nEcco, questo è un database in azione: **dati interconnessi**, **accessibili da più persone insieme**, e **permanenti** nel tempo.\n\nUn\'altra cosa interessante è che un database cerca sempre di essere lo specchio di un pezzetto di mondo reale (in gergo tecnico si parla di **minimondo**): se tu cambi numero di telefono, quel cambiamento deve riflettersi anche nel database della tua scuola o della tua banca. Se i dati nel database non corrispondono più alla realtà, il database ha \"fallito\" il suo compito.\n\nE non pensare che i database servano solo per gestire numeri e testo: oggi vengono usati anche per gestire enormi quantità di dati (i famosi **Big Data**, gestiti spesso con tecnologie **NoSQL**), immagini, video e persino informazioni geografiche, come le mappe di Google Maps (i sistemi **GIS**).\n\n## Chi tiene tutto in ordine? Il DBMS\n\nSe il database è \"l\'archivio\", chi si occupa di gestirlo, proteggerlo e farlo funzionare bene? Entra in gioco il **DBMS**, acronimo di **Database Management System**.\n\nIl DBMS è il software (pensalo come una sorta di \"guardiano\" molto rigoroso) che si occupa di garantire quattro cose fondamentali:\n\n- **Gestisce grandi quantità di dati** in modo efficiente, anche quando parliamo di milioni di righe, senza far esplodere la memoria del computer.\n- **Permette la condivisione**: più applicazioni (l\'app della scuola, il sito web, il gestionale della segreteria) possono accedere agli stessi dati senza doverli duplicare ovunque, evitando che le informazioni diventino incoerenti tra loro.\n- **Garantisce la persistenza**: i dati restano salvati anche quando il programma che li ha creati viene chiuso, o addirittura disinstallato.\n- **Protegge i dati**: da guasti hardware o software (un blackout improvviso non deve far sparire i tuoi dati) e da accessi indesiderati, grazie a un sistema di permessi e password.\n\nIn pratica, ogni volta che usi un\'app che \"ricorda\" qualcosa di te da una sessione all\'altra, dietro c\'è quasi sicuramente un DBMS a fare da intermediario tra l\'applicazione e i dati veri e propri.\n\n## Il concetto più importante: la transazione\n\nOra arriviamo al cuore pulsante di ogni DBMS moderno: la **transazione**.\n\nImmagina di dover trasferire 50 euro dal tuo conto a quello di un amico. Questa operazione, dal punto di vista del database, non è una singola azione: è composta da (almeno) due passaggi:\n\n1. Togliere 50 euro dal tuo conto;\n2. Aggiungere 50 euro al conto del tuo amico.\n\nUna **transazione** è proprio questo: un\'operazione (o un insieme di operazioni di lettura e scrittura) che il database tratta come un **blocco unico e indivisibile**. O va a buon fine completamente, o non succede nulla.\n\nPerché è così importante? Perché se il sistema andasse in crash proprio tra il passaggio 1 e il passaggio 2, i tuoi 50 euro sparirebbero nel nulla, senza mai arrivare a destinazione. Un incubo, no?\n\n## Le quattro regole d\'oro: ACID\n\nPer evitare disastri come quello appena descritto, ogni DBMS serio deve rispettare quattro proprietà fondamentali, conosciute con l\'acronimo **ACID**. Vediamole una per una, sempre con l\'esempio del trasferimento bancario.\n\n- **Atomicità (Atomicity)**: la transazione è \"tutto o niente\". O vengono eseguiti entrambi i passaggi (togliere e aggiungere i 50 euro), o non ne viene eseguito nessuno. Non esistono trasferimenti \"a metà\".\n- **Consistenza (Consistency)**: la transazione deve portare il database da uno stato corretto a un altro stato corretto, rispettando sempre le regole stabilite (ad esempio, non si può avere un saldo negativo se non è previsto un fido). Prima e dopo la transazione, i dati devono avere senso.\n- **Isolamento (Isolation)**: se in quel momento anche un altro utente sta usando la banca (magari sta controllando il suo saldo), la tua transazione non deve \"disturbarlo\" mostrandogli dati a metà elaborazione. Ogni transazione si comporta come se fosse l\'unica in esecuzione in quel momento.\n- **Durabilità (Durability)**: una volta che la transazione è confermata (in gergo si dice che ha fatto **commit**), quella modifica è definitiva. Anche se un secondo dopo salta la corrente, i tuoi 50 euro risultano trasferiti.\n\nQueste quattro regole sono quello che rende un database affidabile a livello professionale, e non un semplice foglio elettronico dove basta un click sbagliato per perdere tutto.\n\n## Cosa ci portiamo a casa\n\nRiassumendo, oggi abbiamo imparato che:\n\n- Un **database** è una collezione organizzata di dati, condivisa e permanente;\n- Il **DBMS** è il software che gestisce, protegge e rende disponibili questi dati;\n- La **transazione** è l\'unità di lavoro fondamentale di un database;\n- Le proprietà **ACID** sono le regole che garantiscono l\'affidabilità di ogni transazione.\n\nQuesti concetti sono le fondamenta su cui poggia tutto il resto: senza capirli bene, sarebbe difficile capire davvero *perché* i database sono progettati come sono.\n\nNel prossimo articolo faremo un passo avanti: scopriremo l\'**architettura a tre livelli** dei database e inizieremo a parlare di **modelli di dati**, il primo passo per iniziare finalmente a progettare le nostre tabelle. Alla prossima!', '', '', '', 1, 'published', '2026-02-27 00:39:24', '2026-02-26 23:39:16', '2026-09-19 14:06:30'),
(3, 3, 4, 3, 'Il metodo di Ruffini per la divisione tra polinomi', 'il-metodo-di-ruffini-per-la-divisione-tra-polinomi', '', 'Quando si deve dividere un polinomio $P(x)$ per un binomio del tipo $(x - a)$, eseguire la divisione polinomiale \"per esteso\" può essere lungo e macchinoso. Paolo Ruffini (1765-1822) ideò una procedura sintetica — nota oggi come regola di Ruffini — che permette di ottenere quoziente e resto lavorando solo con i coefficienti numerici del polinomio, all\'interno di una semplice tabella.\n\nIl metodo si applica principalmente quando il divisore è un binomio di primo grado con coefficiente direttore unitario:\n\n$$D(x) = x - a$$\n\ndove $a$ può essere un numero reale qualunque (positivo, negativo, intero o frazionario).\n\nNota: se il divisore ha la forma $(kx - a)$ con $k \\neq 1$, è comunque possibile usare la regola di Ruffini applicando una piccola correzione algebrica (spiegata nel dettaglio nella sezione 4).\n\n## Il teorema del resto\n\nAlla base del metodo di Ruffini c\'è il teorema del resto:\n\n$$\\text{Il resto della divisione di } P(x) \\text{ per } (x-a) \\text{ è uguale a } P(a).$$\n\nPer il teorema fondamentale della divisione tra polinomi, esistono un quoziente $Q(x)$ e un resto $R$ (costante, perché il divisore ha grado 1) tali che:\n\n$$P(x) = (x - a) \\cdot Q(x) + R$$\n\nSostituendo $x = a$:\n\n$$P(a) = (a - a) \\cdot Q(a) + R = 0 + R = R$$\n\nquindi $R = P(a)$.\n\nUn corollario immediato e fondamentale per la scomposizione con Ruffini è il teorema di Ruffini (o teorema della radice):\n\n$$P(x) \\text{ è divisibile per } (x-a) \\iff P(a) = 0$$\n\nQuando questo accade, significa che $a$ è radice del polinomio e che la divisione è esatta (resto nullo).\n\n## La regola pratica: come fare la divisione\n\nSia dato un polinomio di grado $n$, ordinato secondo le potenze decrescenti di $x$ (ricordati di inserire $0$ come coefficiente per i termini mancanti):\n\n$$P(x) = c_n x^n + c_{n-1}x^{n-1} + \\dots + c_1 x + c_0$$\n\nda dividere per $(x - a)$. La procedura passo-passo è la seguente:\n\nSi scrivono in riga i coefficienti $c_n, c_{n-1}, \\dots, c_1, c_0$.\n\nSi scrive $a$ (il termine noto del divisore cambiato di segno) a sinistra, separato da una linea verticale. L\'ultimo termine $c_0$ viene separato da un\'altra linea verticale.\n\nIl primo coefficiente $c_n$ si riporta invariato sotto la linea orizzontale.\n\nSi moltiplica l\'ultimo valore riportato per $a$ e si somma in colonna al coefficiente successivo, scrivendo il risultato sotto la linea.\n\nSi ripete il passo 4 fino ad esaurire i coefficienti.\n\nL\'ultimo valore ottenuto in basso a destra è il resto $R$; tutti i valori precedenti sono, nell\'ordine, i coefficienti del quoziente $Q(x)$, che avrà grado $n-1$.\n\n## Esempi di divisione con Ruffini\n\nVediamo due casi pratici per chiarire il meccanismo: una divisione esatta e una con resto.\n\n### Esempio 1 — Divisione esatta (resto zero)\n\nDividiamo:\n\n\n$$P(x) = 2x^3 - 3x^2 - 11x + 6 \\quad \\text{per} \\quad (x - 3)$$\n\nQui $a = 3$ e i coefficienti sono $2,\\ -3,\\ -11,\\ 6$. Tutti i gradi di $x$ sono presenti, quindi non dobbiamo aggiungere zeri.\n\nPasso per passo:\n\nSi abbassa il $2$.\n\n$2 \\times 3 = 6$; si somma in colonna: $-3 + 6 = 3$.\n\n$3 \\times 3 = 9$; si somma in colonna: $-11 + 9 = -2$.\n\n$-2 \\times 3 = -6$; si somma in colonna: $6 + (-6) = 0$.\n\nIl resto è $0$: quindi $x = 3$ è radice di $P(x)$ e $(x-3)$ divide esattamente $P(x)$. Il quoziente ha coefficienti $2, 3, -2$, cioè grado $3 - 1 = 2$:\n\n$$Q(x) = 2x^2 + 3x - 2, \\qquad R = 0$$\n\nVerifica per fattorizzazione:\n\n\n$$2x^3 - 3x^2 - 11x + 6 = (x - 3)(2x^2 + 3x - 2)$$\n\n\n@[video](https://www.youtube.com/watch?v=DOYzk0KiWuY)\n\n\n### Esempio 2 — Divisione con resto (e termini mancanti)\n\nDividiamo:\n\n\n$$P(x) = x^4 - 2x^2 + 5 \\quad \\text{per} \\quad (x + 2)$$\n\nAttenzione a due dettagli:\n\n$a = -2$ (dobbiamo cambiare di segno al termine noto del divisore).\n\nMancano i termini in $x^3$ e $x$, quindi i coefficienti saranno: $1, 0, -2, 0, 5$.\n\nProcediamo:\n\nSi abbassa l\'$1$.\n\n$1 \\times (-2) = -2$; somma in colonna: $0 - 2 = -2$.\n\n$-2 \\times (-2) = 4$; somma in colonna: $-2 + 4 = 2$.\n\n$2 \\times (-2) = -4$; somma in colonna: $0 - 4 = -4$.\n\n$-4 \\times (-2) = 8$; somma finale: $5 + 8 = 13$.\n\nIl quoziente scende di un grado (da 4 a 3):\n\n\n$$Q(x) = x^3 - 2x^2 + 2x - 4, \\qquad R = 13$$\n\n## Il caso generale: divisore nella forma (kx - a)\n\nCosa succede se dobbiamo dividere, ad esempio, per $(2x - 1)$?\nNon possiamo usare Ruffini direttamente, perché il coefficiente della $x$ non è $1$. Tuttavia, esiste un trucco.\n\nRiscriviamo il divisore raccogliendo $k$ (in questo caso $2$):\n\n\n$$2x - 1 = 2 \\left(x - \\frac{1}{2}\\right)$$\n\nPossiamo applicare Ruffini usando $a = \\frac{1}{2}$. Otterremo un quoziente apparente. Per trovare il quoziente reale, dovremo dividere i coefficienti del quoziente apparente per $k$ (cioè per $2$). Il resto, invece, rimane invariato.\n\nQuesto permette di utilizzare la regola di Ruffini con le frazioni in modo agevole e corretto.\n\n## Applicazioni notevoli\n\n### Scomposizione in fattori\n\nSe si conosce (o si trova per tentativi) una radice $a$ di $P(x)$, Ruffini permette di abbassare il grado del polinomio ad ogni passo, fino alla scomposizione completa in fattori lineari irriducibili:\n\n$$P(x) = (x-a_1)(x-a_2)\\cdots(x-a_k)\\cdot Q_f(x)$$\n\n### Ricerca delle radici razionali\n\nPer un polinomio a coefficienti interi, il teorema delle radici razionali restringe le candidate radici $a = \\frac{p}{q}$ ai rapporti tra i divisori del termine noto $c_0$ (i valori $p$) e i divisori del coefficiente direttore $c_n$ (i valori $q$).\n\nRuffini è lo strumento ideale per verificare rapidamente ciascun candidato: se l\'ultimo numero in basso a destra (il resto) è $0$, il candidato è una radice effettiva.\n\n## Esercizi sulla regola di Ruffini (con soluzioni)\n\nMettiti alla prova con questi esercizi. Una volta provato, controlla la soluzione cliccando o leggendo di seguito.\n\nEsercizio 1: $P(x) = x^3 - 6x^2 + 11x - 6$, dividere per $(x-1)$, $(x-2)$, $(x-3)$ e dedurre la fattorizzazione completa.\n\nEsercizio 2: $P(x) = 3x^4 - 5x^3 + x - 2$, dividere per $(x+1)$.\n\nEsercizio 3: $P(x) = x^5 - 1$, dividere per $(x-1)$: che relazione notevole si riottiene?\n\nEsercizio 4: Usando il teorema delle radici razionali, trovare tutte le radici intere di $P(x) = x^3 + 2x^2 - 5x - 6$ e scomporlo completamente.\n\n### Soluzioni dettagliate\n\nSoluzione 1: Il polinomio diviso per $(x-1)$ dà resto zero e quoziente $x^2 - 5x + 6$. Dividendo questo nuovo quoziente per $(x-2)$, otteniamo $(x-3)$. Le radici sono proprio $1, 2$ e $3$. La fattorizzazione è: $(x-1)(x-2)(x-3)$.\n\nSoluzione 2: Attenzione al termine in $x^2$ mancante! I coefficienti sono $3, -5, 0, 1, -2$ e $a = -1$. Procedendo con lo schema si ottiene: $Q(x) = 3x^3 - 8x^2 + 8x - 7$ con Resto $R = 5$.\n\nSoluzione 3: Anche qui servono gli zeri. Coefficienti: $1, 0, 0, 0, 0, -1$ e $a = 1$. Nello schema si riportano tutti $1$. Risultato: $Q(x) = x^4 + x^3 + x^2 + x + 1$ con $R = 0$. Questo dimostra il prodotto notevole della scomposizione della differenza di potenze di uguale grado!\n\nSoluzione 4: I divisori del termine noto ($-6$) sono $\\pm 1, \\pm 2, \\pm 3, \\pm 6$. Sostituendo $x = -1$, otteniamo $P(-1) = -1 + 2 + 5 - 6 = 0$. Usando Ruffini con $a = -1$, il quoziente abbassato di un grado è $x^2 + x - 6$. Scomponendo quest\'ultimo trinomio, troviamo le altre radici $2$ e $-3$. Scomposizione finale: $(x+1)(x-2)(x+3)$.\n\n## FAQ - Domande Frequenti\n\nQuando non si può usare la regola di Ruffini?\nLa regola di Ruffini classica è progettata esclusivamente per dividere un polinomio per un binomio di primo grado, come $(x-2)$ o $(x+5)$. Non può essere utilizzata per dividere per polinomi di secondo grado (es. $x^2 + 1$) o superiore; in quei casi è obbligatorio usare la divisione polinomiale tradizionale in colonna.\n\nChe differenza c\'è tra il Teorema del Resto e la Regola di Ruffini?\nIl Teorema del Resto serve a calcolare in anticipo quale sarà il resto di una divisione senza doverla eseguire (basta calcolare $P(a)$). La Regola di Ruffini, invece, è il metodo operativo, ovvero la tabella che ti permette di calcolare non solo il resto, ma anche i coefficienti del quoziente.\n\nCosa fare se nel polinomio mancano alcune lettere (gradi)?\nQuesta è una delle trappole più comuni negli esercizi. Se il polinomio \"salta\" un grado (ad esempio passa da $x^4$ direttamente a $x^2$), nello schema di Ruffini devi obbligatoriamente inserire uno $0$ nella colonna del grado mancante.', 'Regola e Metodo di Ruffini: Spiegazione ed Esercizi Svolti', 'Guida pratica al Metodo di Ruffini per la divisione tra polinomi: teorema del resto, regola pratica passo-passo ed esercizi svolti.', 'ripetizioni matematica, ruffini, algebra, aritmetica, scomposizione', 2, 'published', '2026-09-19 16:47:15', '2026-09-19 14:47:15', '2026-09-22 14:34:23'),
(4, 3, 2, 6, 'Perché imparare il linguaggio C nel 2026 è ancora la scelta più intelligente che puoi fare', 'perche-imparare-il-linguaggio-c-nel-2026-e-ancora-la-scelta-piu-intelligente-che-puoi-fare', '', 'Ammettiamolo. In un mondo in cui esce un nuovo framework JavaScript ogni martedì mattina e l\'ennesima pubblicità annuncia che l\'AI soppianterà il ruolo del developer da qui a pochi giorni, sentirsi dire \"ehi, sai che dovresti imparare il C?\" suona un po\' come un invito a usare il fax o a noleggiare una videocassetta.\n\nLa domanda è più che lecita: ha senso nel 2026 farsi venire il mal di testa su un linguaggio nato nei Bell Labs quando i computer occupavano intere stanze?\n\nSpoiler: sì. Assolutamente sì. Il C non è solo un linguaggio di programmazione. È il biglietto d\'oro per capire davvero cosa succede sotto il cofano del tuo computer.\n\n## Sta tutto nelle tue mani\n\nI linguaggi di oggi (Python, JS, Java) sono fantastici, ma sono un po\' come i resort all-inclusive: fanno tutto loro. Ti nascondono la gestione della memoria, i registri della CPU e come si chiacchiera col sistema operativo. È comodissimo se devi lanciare un\'app in produzione in tempi brevi, ma c\'è un enorme effetto collaterale se sei agli inizi: ti convincono che la RAM sia infinita e che le risorse del PC crescano sugli alberi.\n\nStudiare il C ti sbatte in faccia la dura realtà, ed è bellissimo. Qui non c\'è nessun Garbage Collector pronto a pulire i tuoi disastri. Vuoi della memoria? Te la chiedi, te la prendi, e quando hai finito ti ricordi di liberarla.\n\nInizi a capire che un numero intero non è magia nera, ma una scatoletta di (solitamente) 4 byte con limiti precisissimi. E i famigerati puntatori? Non sono mostri mitologici creati per bocciare gli studenti agli esami, ma sono semplicemente le coordinate esatte di dove la CPU sta andando a pescare i tuoi dati.\n\nIl vero trucco è questo: chi impara il C non diventa solo un \"programmatore C\". Diventa uno sviluppatore nettamente superiore in qualunque altro linguaggio. Se capisci il C, scriverai codice Python che non si incarta, capirai al volo perché la tua app Node.js si sta mangiando tutta la RAM e i misteri della concorrenza diventeranno improvvisamente logici.\n\n## Il pilastro dei software moderni\n\nMagari pensi che il C sia roba da musei, e invece è l\'infrastruttura su cui si poggia letteralmente tutto. Che tu stia usando un Mac, Windows o una distro Linux super customizzata, devi sapere che i loro kernel sono scritti in gran parte in C.\n\nMa non si ferma ai sistemi operativi. Pensa al mondo dell\'Internet of Things, ai microcontrollori o ai sensori industriali: lì le risorse sono così microscopiche che il C regna sovrano. Anche i database giganteschi su cui poggia mezzo web, come PostgreSQL o SQLite, hanno fondamenta in C, perché quando ti serve latenza zero non puoi permetterti strati di astrazione inutili.\n\nE l\'Intelligenza Artificiale? Stesso discorso. Certo, noi sviluppatori chiamiamo comodamente le API in Python, ma quando c\'è da fare il \"lavoro sporco\" e spremere la GPU fino all\'ultima goccia per addestrare un modello, Python passa in silenzio la palla ai runtime e alle librerie scritte in C e C++. Python fa stretching, il C solleva i pesi veri.\n\nSpesso si immagina il C come un vecchietto isolato in montagna, lontanissimo dal web moderno ma spesso si ignora il fatto che praticamente ogni linguaggio ad alto livello ha una \"porta sul retro\" (chiamata Foreign Function Interface) per parlare con il C. Se la tua app moderna deve fare un calcolo brutale e velocissimo, delegherà il lavoro a una libreria C.\n\nE col web? Grazie a WebAssembly (WASM), oggi il codice C può essere compilato per girare direttamente nel browser a velocità quasi nativa. Puoi prendere un intero motore grafico o un emulatore, compilarlo e farlo schizzare su Chrome lato client. Altro che linguaggio morto.\n\nDiamo un\'occhiata a un pezzo di codice. Nessuna black box, niente astrazioni: solo tu e la RAM nuda e cruda.\n\n```c\n#include <stdio.h>\n#include <stdlib.h>\n\nint main(void) {\n    // 1. Variabile comodamente allocata sullo Stack\n    int valore = 42;\n    \n    // 2. Ecco il famoso puntatore: contiene l\'indirizzo esatto di \'valore\'\n    int *ptr = &valore;\n\n    printf(\"Valore della variabile: %d\\n\", valore);\n    printf(\"Indirizzo di memoria fisico: %p\\n\", (void*)&valore);\n    printf(\"Valore visto tramite il puntatore: %d\\n\\n\", *ptr);\n\n    // 3. Chiediamo esplicitamente memoria sullo Heap per 3 interi\n    size_t dimensione = 3;\n    int *array_dinamico = (int *)malloc(dimensione * sizeof(int));\n\n    if (array_dinamico == NULL) {\n        fprintf(stderr, \"Bro, abbiamo finito la RAM!\\n\");\n        return 1;\n    }\n\n    // Riempiamo la memoria e andiamo a spiare gli indirizzi\n    for (size_t i = 0; i < dimensione; i++) {\n        array_dinamico[i] = (int)(i + 1) * 10;\n        printf(\"Elemento [%zu] = %d (Si trova qui: %p)\\n\", \n               i, array_dinamico[i], (void*)&array_dinamico[i]);\n    }\n\n    // 4. In C sei tu l\'adulto responsabile: hai sporcato? Ora pulisci.\n    free(array_dinamico);\n    \n    // Buona pratica: stacchiamo la spina al puntatore per evitare casini\n    array_dinamico = NULL; \n\n    return 0;\n}\n```\nIn queste poche righe c\'è la vera essenza dell\'informatica. Puoi letteralmente stampare a schermo gli indirizzi esadecimali gestiti dall\'hardware. Sei tu a decidere quanto spazio ti serve calcolandolo al byte `(sizeof(int))` e sei tu il responsabile delle pulizie finali con la `free()`.\n\n## Dove vogliamo andare\nImparare il C non vuol dire restare incastrati negli anni \'70. Vuol dire prendersi la pillola rossa di Matrix e acquisire i superpoteri per decifrare qualsiasi architettura moderna, senza farsi spaventare da ciò che succede dietro le quinte.\n\nNei prossimi articoli di questa guida non ci limiteremo alla teoria noiosa. Metteremo le mani in pasta: partiremo dalle basi della sintassi per arrivare a costruire roba vera, sbattendo la testa su strutture dati e programmazione di sistema. Preparatevi, ci sarà da divertirsi.', 'Imparare il Linguaggio C nel 2026: Ha Ancora Senso? | Guida', 'Perché studiare il C nel 2026? Scopri come padroneggiare la memoria, i suoi utilizzi moderni (kernel, AI, runtime) e il ponte verso le tecnologie moderne.', 'perché studiare C, guida C per principianti, utilizzi moderni linguaggio C, programmazione di sistema', 3, 'published', '2026-09-22 16:59:23', '2026-09-22 14:59:13', '2026-09-22 15:43:01'),
(5, 3, 2, 6, 'Da testo a file eseguibile: come compilare il tuo primo programma in C senza impazzire', 'da-testo-a-file-eseguibile-come-preparare-il-tuo-arsenale-c-senza-impazzire', '', 'Se vieni dal mondo di Python, JavaScript o PHP, sei abituato a una vita comoda. Scrivi il codice, premi invio e boom, il computer lo esegue. Magia? No, fanno solo il loro dovere da interpreti. Nel fantastico e spietato mondo del C, le cose funzionano diversamente. Il tuo processore non parla il C; parla solo di zeri, uni e registri.\n\nPer far capire alla macchina cosa diavolo hai scritto, hai bisogno di un traduttore. E non un traduttore qualsiasi, ma un\'intera catena di montaggio che prende il tuo codice leggibile e lo tritura fino a farlo diventare un file binario puro e crudo.\n\nBenvenuto nel processo di compilazione. Prepara il terminale (sì, qui si fa tutto da riga di comando, magari su una bella shell Zsh ben configurata), perché stiamo per sporcarci le mani.\n\n## I tuoi nuovi migliori amici: GCC e Clang\n\nPer tradurre il C, ti serve un compilatore. I due pesi massimi indiscussi sono [GCC](https://gcc.gnu.org/) (GNU Compiler Collection) e [Clang](https://clang.llvm.org/), il front-end C/C++ del progetto LLVM. Fanno praticamente la stessa cosa e accettano quasi le stesse identiche opzioni da riga di comando, al punto che in molti progetti puoi passare dall\'uno all\'altro cambiando solo il valore di una variabile (lo vedremo tra poco con i Makefile). Clang però è molto amato per i suoi messaggi di errore più leggibili e colorati, mentre GCC a volte ti urla contro in aramaico antico se sbagli una virgola.\n\nSe stai usando un ambiente Linux, è molto probabile che GCC sia già lì ad aspettarti: verificalo con `gcc --version`. Se manca, su Debian/Ubuntu ti basta `sudo apt install build-essential` (installa GCC, Make e le librerie di base tutte insieme). Su macOS trovi Clang preinstallato dietro le Xcode Command Line Tools (`xcode-select --install`); su Windows la strada più indolore è usare WSL e trattarlo come un Linux a tutti gli effetti. Ma non basta lanciare un comando a caso. Devi compilare con la cintura di sicurezza allacciata.\n\nQuando compili, usa sempre i flag `-Wall` e `-Wextra`. Questi comandi dicono al compilatore: \"Ehi, fammi da revisore spietato. Segnalami ogni minima cosa sospetta, anche se tecnicamente funziona\". Concretamente, una banalità come questa:\n\n```c\nint main(void) {\n    int risultato;\n    return 0;\n}\n```\n\ncompilata senza flag non produce nemmeno un fiato. Con `-Wall -Wextra` invece ottieni subito un bel `warning: unused variable \'risultato\'`. All\'inizio ti sembrerà frustrante vedere decine di warning per una variabile non usata o un casting ambiguo, ma fidati: ti salveranno da ore di debugging alle 3 di notte per un segmentation fault. Quando ti sentirai più sicuro, aggiungi anche `-std=c11` per fissare la versione dello standard C e, se vuoi essere spietato con te stesso, `-Werror` per trasformare ogni warning in un errore che blocca la compilazione: meglio litigare col compilatore ora che con un crash in produzione dopo.\n\n## Cosa succede dietro le quinte? Le 4 fasi della compilazione\n\nQuando digiti `gcc main.c -o programma` e premi invio, sembra che tutto avvenga in un istante. In realtà, sotto il cofano, il tuo file passa attraverso quattro gironi danteschi, e `gcc` fa da direttore d\'orchestra: richiama in sequenza programmi distinti (il preprocessore, il vero compilatore, l\'assembler `as`, il linker `ld`), passando l\'output dell\'uno come input del successivo. La buona notizia è che puoi fermare la pipeline a metà strada, con un flag dedicato per ogni fase, e curiosare nel risultato intermedio.\n\n### 1. Il Preprocessore\n\nPrima ancora che il codice venga tradotto, infatti, entra in gioco il preprocessore. Cerca tutte le righe che iniziano con `#` (come `#include <stdio.h>` o le macro `#define`) e le esegue con la sensibilità di un editor \"trova e sostituisci\": nessuna comprensione del linguaggio, solo taglia-e-incolla di puro testo.\n\nSe trova un `#include`, prende il contenuto di quel file e lo copia-incolla al posto di quella riga. Per questo un `#include <stdio.h>`, che sembra una singola riga innocua, può far esplodere il tuo file da 10 a diverse migliaia di righe: dentro `stdio.h` ci sono dichiarazioni, altri `#include` annidati e commenti che finiscono tutti nel calderone.\n\nLe macro funzionano allo stesso modo brutale. Se scrivi:\n\n```c\n#define MAX_STUDENTI 30\n\nint posti[MAX_STUDENTI];\n```\n\nil preprocessore non sa cosa sia `MAX_STUDENTI`: vede solo testo, e ne sostituisce ogni occorrenza con `30` prima ancora che il compilatore veda il file. Lo stesso vale per le direttive condizionali come `#ifdef` e `#ifndef`, usatissime per includere o escludere interi blocchi di codice (il caso classico è codice diverso per Windows e Linux).\n\nPuoi vedere il risultato di questa fase con i tuoi occhi fermando la pipeline con il flag `-E`:\n\n```bash\ngcc -E main.c -o main.i\n```\n\nApri `main.i` e preparati ad un trauma: anche il più semplice \"Ciao Mondo\" produce un file espanso di migliaia di righe, quasi tutte dichiarazioni che non hai scritto tu. È il prezzo da pagare per un `#include`. Questo mostro testuale resta comunque codice C valido, pronto per la vera compilazione.\n\n![La fase di preprocessing](https://www.giorgiodifusco.it/public/uploads/blog_images/9761fc680993a89c4df24536175b1053.png)\n\n\n### 2. La Compilazione\n\nQui avviene la vera magia, ed è anche il momento in cui il compilatore controlla che il tuo codice abbia senso: è in questa fase che entrano in gioco i warning di `-Wall -Wextra` di cui parlavamo prima, insieme ai veri e propri errori di sintassi o di tipo. Se hai dimenticato un punto e virgola, è qui che te lo fa notare.\n\nSuperato il controllo, il compilatore prende il file espanso dal preprocessore e lo traduce in Assembly. \n\nL\'Assembly è un linguaggio a bassissimo livello, specifico per l\'architettura del tuo processore (x86-64, ARM, RISC-V...): la stessa riga C compilata su un Mac con Apple Silicon e su un PC Intel produce Assembly completamente diverso, anche se il comportamento finale è identico. Un\'istruzione come `int x = 42;` inizia a somigliare a qualcosa del genere:\n\n```asm\nmov DWORD PTR [rbp-4], 42\n```\n\nCioè, in parole povere: \"sposta il valore 42 nella cella di memoria che sta 4 byte prima di `rbp`\". Puoi vedere questo output intermedio fermando la pipeline con `-S`:\n\n```bash\ngcc -S main.i -o main.s\n```\n\nIl file `main.s` è ancora testo semplice, leggibile con qualunque editor. Se sei curioso di vedere come cambia l\'Assembly generato attivando le ottimizzazioni (`-O1`, `-O2`, `-O3`), questa è la fase da guardare: con le ottimizzazioni attive il compilatore riscrive, elimina e riordina istruzioni per farle girare più in fretta, e il codice diventa spesso irriconoscibile rispetto alla versione \"ingenua\".\n\n\n![La fase di compilazione](https://www.giorgiodifusco.it/public/uploads/blog_images/822ac7968bc9fb5eb8d0d81028de25c9.png)\n\n\n### 3. L\'Assemblaggio\n\nIl codice Assembly è ancora testo leggibile (più o meno): è l\'ultimo momento della pipeline in cui un umano può capire qualcosa senza strumenti speciali. L\'Assembler prende questo testo e lo converte in codice oggetto (i famosi file `.o`), impacchettato in un formato binario standard (su Linux è ELF, Executable and Linkable Format; su macOS è Mach-O, su Windows COFF/PE). Da qui in poi il file non è più testo: sono sequenze di byte pensate per essere lette dalla macchina, non da te.\n\n```bash\ngcc -c main.s -o main.o\n```\n\n(nella pratica di tutti i giorni scriverai quasi sempre `gcc -c main.c -o main.o`: `gcc` capisce da solo che deve far passare il file per preprocessore e compilatore prima di arrivare qui). Se sei curioso di guardare dentro un file `.o` senza doverlo eseguire, comandi come `nm main.o` (elenca i simboli) o `objdump -d main.o` (mostra il disassemblato) fanno al caso tuo.\n\nQuesto è codice macchina puro, binario, ma non è ancora un programma completo perché gli mancano i collegamenti con il mondo esterno: se il tuo file usa una funzione definita altrove, come `printf`, l\'Assembler si limita ad annotare \"qui manca qualcosa, risolvilo più avanti\" e lascia il vuoto. Quel vuoto è il problema dell\'ultimo passaggio.\n\n\n![L\'assembler](https://www.giorgiodifusco.it/public/uploads/blog_images/0ffdde3f232acfac38a2118e6c6d17cf.png)\n\n\n### 4. Il Linker\n\nSe nel tuo codice hai usato la funzione `printf`, il tuo file `.o` ha un \"buco\". Sa che deve chiamare `printf`, ma non ha idea di dove sia il codice reale di quella funzione: quel codice vive nella libreria standard del C (`libc`), non nel tuo file. Il Linker prende il tuo file oggetto, va a pescare le librerie di sistema necessarie, unisce tutti i pezzi (compresi eventuali altri file `.o`, se il tuo progetto ha più file sorgente) e risolve uno per uno gli indirizzi mancanti.\n\nSe hai mai visto un errore del tipo `undefined reference to \'funzione\'`, congratulazioni: hai appena conosciuto il Linker. Non è un errore del compilatore, che a quel punto ha già finito il suo lavoro da un pezzo, ma del Linker che non è riuscito a trovare da nessuna parte il codice reale di quella funzione, di solito perché hai dimenticato di passare un file `.o` nel comando finale o di linkare una libreria con `-l`.\n\nLe librerie possono essere collegate in due modi. Con il **linking statico**, il codice della libreria (un file `.a`) viene copiato per intero dentro il tuo eseguibile: il file finale è più grande, ma gira anche senza quella libreria installata sul sistema di destinazione. Con il **linking dinamico**, di gran lunga il più comune (ed è quello che usa `gcc` di default con `libc`), l\'eseguibile si porta dietro solo un riferimento a una libreria condivisa (`.so` su Linux), caricata in memoria al momento dell\'esecuzione: file più piccoli e aggiornamenti condivisi da tutti i programmi, ma il tuo programma non parte se quella libreria manca sul sistema. Puoi vedere da quali librerie dinamiche dipende un tuo eseguibile con `ldd programma`.\n\nIl risultato finale di tutto questo lavoro? Il tuo amato file eseguibile.\n\n\n![Il linking](https://www.giorgiodifusco.it/public/uploads/blog_images/11b15056c8ff8044ecc9774701185384.png)\n\n\n## Smetti di ripeterti: i Makefile\n\nOra, immagina di avere un progetto serio, magari non il solito \"Ciao Mondo\", ma qualcosa con 5 o 6 file sorgente diversi. Compilare a mano scrivendo ogni volta `gcc main.c utils.c network.c -Wall -Wextra -o mia_app` diventa una tortura medievale. E se modifichi solo `utils.c`, perché dovresti ricompilare tutto il resto?\n\nÈ qui che entra in gioco [Make](https://www.gnu.org/software/make/). È uno strumento di automazione storico, un vero salvavita. Crei un file testuale chiamato **Makefile** nella root del tuo progetto, ci scrivi dentro le regole di compilazione, e da quel momento in poi ti basta digitare make nel terminale. Fa tutto lui, compilando solo i file che sono stati effettivamente modificati.\n\nEcco un template minimale, elegante e pronto all\'uso per i tuoi primi progetti.\n\n```make\n# Definiamo le variabili per comodità\nCC = gcc\nCFLAGS = -Wall -Wextra -std=c11\nTARGET = app\n\n# Troviamo in automatico tutti i file .c nella cartella\nSRCS = $(wildcard *.c)\n# Convertiamo i nomi da .c a .o\nOBJS = $(SRCS:.c=.o)\n\n# Regola principale: quando digiti \'make\', costruisce il TARGET\n$(TARGET): $(OBJS)\n	$(CC) $(CFLAGS) -o $(TARGET) $(OBJS)\n\n# Regola generica per compilare i file oggetto\n%.o: %.c\n	$(CC) $(CFLAGS) -c $< -o $@\n\n# \'clean\' non è un file: dichiararla .PHONY evita sorprese se un giorno\n# nella cartella comparisse per sbaglio un file chiamato davvero \"clean\"\n.PHONY: clean\n\n# Regola per pulire il progetto dai file generati\nclean:\n	rm -f $(OBJS) $(TARGET)\n```\n\nAttenzione al dettaglio fondamentale che fa impazzire tutti i dev alle prime armi: le rientranze sotto le regole (come quella prima di `$(CC)`) devono essere fatte con il tasto TAB, non con gli spazi. Se usi gli spazi, Make ti sputerà un errore incomprensibile e si rifiuterà di lavorare.\n\nNel template si nascondono un po\' di magie di sintassi che vale la pena capire, non solo copiare. `$(wildcard *.c)` chiede a Make di guardare nella cartella e restituire l\'elenco di tutti i file `.c` presenti, così non devi elencarli a mano. `$(SRCS:.c=.o)` è una sostituzione di pattern: prende quella lista e cambia l\'estensione da `.c` a `.o`, generando l\'elenco degli oggetti attesi. Dentro le regole trovi poi le variabili automatiche: `$@` significa \"il target di questa regola\" (il file che sto costruendo), mentre `$<` significa \"il primo prerequisito\" (il file `.c` di partenza). Sono scorciatoie, ma è grazie a loro che una singola regola generica (`%.o: %.c`) basta per compilare qualsiasi file `.c` del progetto, senza doverne scrivere una per ciascuno.\n\nUn\'ultima dritta da progetto vero: se le dipendenze del tuo Makefile sono dichiarate correttamente, puoi lanciare `make -j4` per compilare più file in parallelo su 4 core e velocizzare parecchio la build.\n\nCon questo arsenale configurato, sei pronto a smettere di combattere contro gli strumenti e iniziare a domare la memoria. Nel prossimo articolo entreremo finalmente nel vivo del codice: parleremo di tipi primitivi, di come manipolare i singoli bit e di come far fare al processore esattamente ciò che vogliamo. Prepara il terminale.', 'Il processo di compilazione nel linguaggio C', 'Scopri l\'ambiente di sviluppo C: come funziona il compilatore (GCC/Clang), le 4 fasi da codice a eseguibile e come automatizzare tutto con un Makefile.', 'ambiente di sviluppo C, compilatore GCC Clang, fasi compilazione C, tutorial Makefile, come compilare in C', 4, 'published', '2026-09-23 12:33:57', '2026-09-23 10:33:57', '2026-09-23 16:58:59'),
(6, 3, 2, 6, 'Numeri, bit e operatori: domare i tipi primitivi in C', 'numeri-bit-e-operatori-domare-i-tipi-primitivi-ed-il-flusso-di-un-programma-in-c', '', 'Nei linguaggi moderni siamo stati viziati. Se sei abituato a JavaScript, scrivi `let x = 10`, poi magari decidi che `x` deve diventare una stringa, l\'interprete annuisce e fa il lavoro sporco per te. In C, questa flessibilità semplicemente non esiste. Se vuoi dello spazio in memoria, devi dire al compilatore esattamente di quanti byte hai bisogno e cosa ci metterai dentro.\n\nSembra una scocciatura, ma è proprio questa rigidità che ti permette di far girare il tuo codice letteralmente ovunque: su un server da 128 core così come su un microcontrollore Arduino con 2KB di RAM.\n\n## La bugia dell\'int e la salvezza di stdint.h\n\nSe hai già sbirciato del codice C, avrai sicuramente visto dichiarazioni come `int contatore = 0;`. Sembra innocuo, ma c\'è un problema subdolo: lo standard del C non definisce esattamente quanto sia grande un `int`. Ti dice solo che deve essere almeno 2 byte. Su un vecchio sistema a 16 bit, un `int` occupa 2 byte. Sul tuo laptop moderno a 64 bit, ne occupa 4.\n\nSe stai scrivendo un protocollo di rete o stai leggendo i registri di un sensore, non puoi affidarti al caso. Hai bisogno di certezze assolute.\n\nEcco perché i veri sviluppatori C si affidano a un header introdotto con lo standard C99: `<stdint.h>`. Importando questa libreria, smetti di usare definizioni vaghe e inizi a parlare la lingua dell\'hardware:\n\n- `uint8_t`: Intero senza segno, esattamente 1 byte (da 0 a 255). Perfetto per manipolare singoli caratteri o dati raw.\n- `int32_t`: Intero con segno, esattamente 4 byte.\n- `uint64_t`: Intero senza segno, esattamente 8 byte. Ideale per timestamp o contatori astronomici.\n\n\n![Tipi interi in C](https://www.giorgiodifusco.it/public/uploads/blog_images/bf4dd98de4a1d6d17b4c20900bdd60fe.png)\n\nUsare questi tipi ti costringe a pensare ai limiti. Cosa succede se aggiungi 1 a un `uint8_t` che vale 255? Non si espande magicamente. Fa overflow. Torna a zero, esattamente come il contachilometri di una vecchia Fiat Panda che arriva a 999.999 e ricomincia da capo. Attenzione però: questo \"giro completo\" è garantito dallo standard solo per i tipi senza segno. Con i tipi con segno (`int8_t`, `int32_t`...) l\'overflow è *comportamento indefinito*: il compilatore è libero di assumere che non accada mai, e il risultato può essere qualsiasi cosa. In crittografia o nei sistemi embedded, non gestire un overflow significa creare una vulnerabilità critica.\n\n\n![Overflow](https://www.giorgiodifusco.it/public/uploads/blog_images/a45a277b1af8a538639105b6594782e1.png)\n\n\n## Gli operatori bitwise\n\nLa vera goduria del C sono gli operatori bitwise. Ti permettono di scavalcare il concetto di \"numero\" e andare a manipolare direttamente i singoli bit (gli 1 e gli 0) che compongono quel numero nella RAM.\n\nI fantastici quattro sono:\n\n- AND (`&`): Restituisce 1 solo se entrambi i bit sono 1. Ottimo per \"mascherare\" e spegnere bit specifici.\n- OR (`|`): Restituisce 1 se almeno uno dei bit è 1. Usato per accendere bit specifici.\n- XOR (`^`): Restituisce 1 solo se i bit sono diversi. È la base della crittografia leggera.\n- Shift (`<<` e `>>`): Sposta letteralmente tutti i bit a sinistra o a destra. Uno shift a sinistra (`<< 1`) equivale a moltiplicare per 2 in modo brutalmente veloce, a livello hardware.\n\n![Operatori bitwise](https://www.giorgiodifusco.it/public/uploads/blog_images/9af95fdd621a5406704c3fc2ad19e64f.png)\n\n## Costruiamo un convertitore da decimale a binario\n\nPer capire davvero i bitwise, non c\'è niente di meglio che usarli per guardare dentro la memoria. Costruiremo un piccolo programma che prende un numero intero e usa lo shift e una maschera bitwise per stampare a schermo la sua esatta rappresentazione binaria.\n\nApri il tuo editor, crea un file `bit.c` e inserisci questo codice:\n\n```c\n#include <stdio.h>\n#include <stdint.h>\n\nint main(void) {\n    // Usiamo un intero senza segno a 16 bit (2 byte)\n    uint16_t numero = 42;\n\n    printf(\"Il numero decimale è: %u\\n\", numero);\n    printf(\"La sua rappresentazione binaria è: \");\n\n    // Un uint16_t ha 16 bit. Partiamo dal bit più significativo (il 15esimo)\n    // e scendiamo fino al bit 0.\n    for (int i = 15; i >= 0; i--) {\n        // 1. Spostiamo il bit che ci interessa nella prima posizione a destra (posizione 0)\n        uint16_t bit_spostato = numero >> i;\n\n        // 2. Usiamo un AND bitwise con 1 (che in binario è ...00000001)\n        // Questo azzera tutti gli altri bit e ci lascia solo il valore dell\'ultimo bit (0 o 1)\n        uint16_t bit_reale = bit_spostato & 1;\n\n        // Stampiamo il singolo bit\n        printf(\"%u\", bit_reale);\n\n        // Aggiungiamo uno spazio ogni 4 bit per renderlo leggibile\n        if (i % 4 == 0) {\n            printf(\" \");\n        }\n    }\n\n    printf(\"\\n\");\n    return 0;\n}\n```\n\nCompilalo con\n\n```bash\ngcc bit.c -Wall -Wextra -o bit\n```\n\ned eseguilo con `./bit`. Vedrai il numero 42 trasformarsi in un nudo e crudo `0000 0000 0010 1010`.\n\n![Convertitore binario](https://www.giorgiodifusco.it/public/uploads/blog_images/d8c6fbb8128288f24b7e7a8367fc28b0.png)\n\n\nSenza usare conversioni magiche in stringa offerte da librerie di alto livello, abbiamo estratto l\'informazione direttamente dal silicio, bit dopo bit. Questo è il pattern esatto che useresti per leggere lo stato dei pin digitali su un microcontrollore o per impacchettare pixel in un formato immagine raw.\n\nNel prossimo articolo alzeremo la posta in gioco. Prenderemo questi tipi di dati primitivi e vedremo come il C ci permette di navigarci attraverso usando gli indirizzi di memoria. Preparati ad affrontare i puntatori e l\'aritmetica dei puntatori.', 'Tipi Primitivi e Operatori Bitwise in C | Guida Pratica', 'Dimentica il \"var\" o \"let\". Scopri come il C gestisce i tipi esatti con stdint.h, l\'overflow e come manipolare i singoli bit con gli operatori bitwise.', 'operatori bitwise C, stdint.h, overflow memoria C, manipolazione bit, convertitore decimale binario C', 13, 'published', '2026-09-25 12:19:21', '2026-09-24 19:51:13', '2026-09-25 10:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_categories`
--

CREATE TABLE `blog_post_categories` (
  `post_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `slug`, `description`, `sort_order`, `created_at`) VALUES
(2, 'Informatica', 'informatica', NULL, 0, '2026-02-26 23:32:29'),
(4, 'Matematica', 'matematica', NULL, 0, '2026-09-19 14:24:46');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `date` datetime NOT NULL,
  `duration` int NOT NULL COMMENT 'Duration in minutes',
  `price` decimal(10,2) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '0',
  `lesson_type` enum('single','package') DEFAULT 'single',
  `package_id` int DEFAULT NULL,
  `topic` varchar(255) NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `location_type` enum('online','in_person') NOT NULL DEFAULT 'in_person' COMMENT 'Modalità della lezione',
  `meeting_link` varchar(2048) DEFAULT NULL COMMENT 'Link per le lezioni online'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `student_id`, `date`, `duration`, `price`, `is_paid`, `lesson_type`, `package_id`, `topic`, `status`, `created_at`, `location_type`, `meeting_link`) VALUES
(1, 5, '2025-12-15 17:00:00', 120, NULL, 0, 'package', 3, 'Teoria degli insiemi', 'completed', '2026-02-24 23:41:17', 'in_person', NULL),
(2, 5, '2025-12-17 17:00:00', 60, NULL, 0, 'package', 3, 'Teoria degli insiemi', 'completed', '2026-02-24 23:42:27', 'in_person', NULL),
(3, 5, '2025-12-22 17:00:00', 60, NULL, 0, 'package', 3, 'Teoria degli insiemi', 'completed', '2026-02-24 23:43:23', 'in_person', NULL),
(4, 5, '2026-01-05 17:00:00', 120, NULL, 0, 'package', 3, 'Espressioni algebriche e geometria', 'completed', '2026-02-24 23:43:56', 'in_person', NULL),
(5, 5, '2026-01-12 17:00:00', 60, NULL, 0, 'package', 3, 'Logica proposizionale', 'completed', '2026-02-24 23:46:04', 'in_person', NULL),
(6, 5, '2026-01-14 16:00:00', 60, NULL, 0, 'package', 3, 'Fisica', 'completed', '2026-02-24 23:46:28', 'in_person', NULL),
(7, 5, '2026-01-19 17:00:00', 60, NULL, 0, 'package', 3, 'Logica e geometria', 'completed', '2026-02-24 23:46:48', 'in_person', NULL),
(8, 5, '2026-01-26 16:00:00', 60, NULL, 0, 'package', 3, 'Geometria', 'completed', '2026-02-24 23:47:33', 'in_person', NULL),
(9, 5, '2026-01-26 18:00:00', 60, NULL, 0, 'package', 2, 'Geometria', 'completed', '2026-02-24 23:48:24', 'in_person', NULL),
(10, 5, '2026-01-28 16:00:00', 60, NULL, 0, 'package', 2, 'Esercitazione di aritmetica', 'completed', '2026-02-24 23:48:42', 'in_person', NULL),
(11, 5, '2026-02-02 16:00:00', 60, NULL, 0, 'package', 2, 'Geometria', 'completed', '2026-02-24 23:49:01', 'in_person', NULL),
(12, 5, '2026-02-04 17:00:00', 120, NULL, 0, 'package', 2, 'Esercitazione di aritmetica', 'completed', '2026-02-24 23:49:31', 'in_person', NULL),
(13, 5, '2026-02-09 16:00:00', 120, NULL, 0, 'package', 2, 'Esercitazione di aritmetica', 'completed', '2026-02-24 23:49:52', 'in_person', NULL),
(14, 5, '2026-02-11 16:00:00', 60, NULL, 0, 'package', 2, 'Esercitazione di fisica', 'completed', '2026-02-24 23:50:14', 'in_person', NULL),
(15, 5, '2026-02-16 18:00:00', 60, NULL, 0, 'package', 2, 'Esercitazione di aritmetica', 'completed', '2026-02-24 23:51:38', 'in_person', NULL),
(16, 5, '2025-12-11 17:00:00', 120, NULL, 1, 'single', NULL, 'Teoria degli insiemi', 'completed', '2026-02-24 23:53:33', 'in_person', NULL),
(17, 5, '2026-02-18 17:00:00', 60, NULL, 0, 'package', 4, 'Esercitazione di aritmetica', 'completed', '2026-02-24 23:56:46', 'in_person', NULL),
(18, 5, '2026-02-20 16:00:00', 120, NULL, 0, 'package', 4, 'Esercitazione di aritmetica', 'completed', '2026-02-24 23:57:20', 'in_person', NULL),
(19, 5, '2026-02-25 17:00:00', 60, NULL, 0, 'package', 4, 'Lezione di Matematica: teoria', 'completed', '2026-02-25 11:37:33', 'in_person', NULL),
(24, 5, '2026-03-04 17:00:00', 120, NULL, 0, 'package', 4, 'Monomi e Polinomi', 'completed', '2026-03-04 14:38:57', 'in_person', NULL),
(25, 5, '2026-02-11 17:00:00', 60, NULL, 0, 'package', 2, 'Esercitazione per recupero di Matematica', 'completed', '2026-03-05 18:15:35', 'in_person', NULL),
(26, 5, '2026-03-06 16:00:00', 60, NULL, 0, 'package', 4, 'Esercizi prodotti notevoli', 'completed', '2026-03-06 15:03:30', 'in_person', NULL),
(28, 5, '2026-03-11 16:30:00', 120, NULL, 0, 'package', 4, 'Monomi e Polinomi', 'completed', '2026-03-11 14:39:32', 'in_person', NULL),
(29, 5, '2026-03-13 17:00:00', 60, NULL, 0, 'package', 4, 'Esercizi prodotti notevoli', 'completed', '2026-03-13 16:36:13', 'in_person', NULL),
(30, 5, '2026-03-18 17:00:00', 120, NULL, 0, 'package', 6, 'Divisione polinomiale: Ruffini. Esercizi di Geometria', 'completed', '2026-03-18 14:33:59', 'in_person', NULL),
(31, 5, '2026-03-23 18:00:00', 60, NULL, 0, 'package', 6, 'Esercizi divisione polinomiale', 'completed', '2026-03-18 18:23:53', 'in_person', NULL),
(32, 5, '2026-03-25 17:00:00', 120, NULL, 0, 'package', 6, 'Matematica e Geometria', 'completed', '2026-03-18 18:24:16', 'in_person', NULL),
(38, 5, '2026-04-22 17:00:00', 120, NULL, 0, 'package', 6, 'Ripasso prodotti notevoli', 'completed', '2026-04-23 16:07:23', 'in_person', NULL),
(39, 5, '2026-04-20 18:00:00', 60, NULL, 0, 'package', 6, 'Informatica: pacchetto office e writer', 'completed', '2026-04-23 16:07:50', 'in_person', NULL),
(40, 5, '2026-04-15 16:00:00', 60, NULL, 0, 'package', 6, 'Scomposizione di polinomi', 'completed', '2026-04-23 16:08:33', 'in_person', NULL),
(41, 5, '2026-04-29 17:00:00', 60, NULL, 0, 'package', 6, 'Scomposizione polinomiale', 'completed', '2026-04-29 16:19:39', 'in_person', NULL),
(42, 5, '2026-05-04 18:15:00', 60, NULL, 0, 'package', 7, 'Ripasso geometria', 'completed', '2026-05-06 22:30:32', 'in_person', NULL),
(43, 5, '2026-05-06 17:00:00', 60, NULL, 0, 'package', 7, 'Ripasso geometria', 'completed', '2026-05-06 22:30:54', 'in_person', NULL),
(44, 5, '2026-05-14 17:30:00', 30, NULL, 0, 'package', 7, 'Svolgimento compiti per casa', 'completed', '2026-05-18 08:23:28', 'in_person', NULL),
(45, 5, '2026-05-13 17:00:00', 120, NULL, 0, 'package', 7, 'Esercizi sulla scomposizione di polinomi', 'completed', '2026-05-18 08:24:22', 'in_person', NULL),
(46, 5, '2026-05-18 18:00:00', 60, NULL, 0, 'package', 7, 'Ripasso scomposizione polinomiale', 'completed', '2026-05-18 08:25:38', 'in_person', NULL),
(47, 5, '2026-05-20 16:00:00', 60, NULL, 0, 'package', 7, 'Ripasso scomposizione polinomiale', 'completed', '2026-05-23 07:18:56', 'in_person', NULL),
(48, 5, '2026-05-23 16:00:00', 90, NULL, 0, 'package', 7, 'Ripasso scomposizione polinomiale', 'completed', '2026-05-23 07:19:14', 'in_person', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_packages`
--

CREATE TABLE `lesson_packages` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `total_minutes` int NOT NULL DEFAULT '600' COMMENT 'Totale minuti acquistati (es. 10 ore = 600)',
  `price` decimal(10,2) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lesson_packages`
--

INSERT INTO `lesson_packages` (`id`, `student_id`, `total_minutes`, `price`, `is_paid`, `description`, `status`, `created_at`) VALUES
(2, 5, 600, 120.00, 1, 'Secondo pacchetto', 'completed', '2026-01-25 23:00:00'),
(3, 5, 600, 120.00, 1, 'Primo pacchetto', 'completed', '2025-12-11 23:33:00'),
(4, 5, 600, 120.00, 1, 'Terzo pacchetto', 'completed', '2026-02-18 16:00:00'),
(6, 5, 600, 120.00, 1, 'Quarto pacchetto 10 ore di Matematica', 'completed', '2026-03-18 16:00:00'),
(7, 5, 480, 90.00, 1, 'Quinto pacchetto matematica e geometria', 'completed', '2026-05-04 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `type` enum('file','link','text') NOT NULL DEFAULT 'file',
  `title` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filepath` varchar(255) DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `content` longtext,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `student_id`, `type`, `title`, `filename`, `filepath`, `url`, `content`, `uploaded_at`) VALUES
(1, 5, 'file', 'Correzione_esercizi.pdf', 'Correzione_esercizi.pdf', '/uploads/materials/699e45b2bfb8e_Correzione_esercizi.pdf', NULL, NULL, '2026-02-25 00:43:30'),
(5, 5, 'file', 'Lezione 11_12_25 (Teoria insiemi).pdf', 'Lezione 11_12_25 (Teoria insiemi).pdf', '/uploads/materials/69b194bf571e4_Lezione 11_12_25 (Teoria insiemi).pdf', NULL, NULL, '2026-03-11 16:13:51'),
(6, 5, 'file', 'SCAN_20260313_174414995.pdf', 'SCAN_20260313_174414995.pdf', '/uploads/materials/69b469bd85f9d_SCAN_20260313_174414995.pdf', NULL, NULL, '2026-03-13 19:47:09'),
(7, 5, 'file', 'Dimostrazione_Geometria.pdf', 'Dimostrazione_Geometria.pdf', '/uploads/materials/69bdad089d672_Dimostrazione_Geometria.pdf', NULL, NULL, '2026-03-20 20:24:40'),
(8, 5, 'file', 'Esercizi_Criteri_Congruenza.pdf', 'Esercizi_Criteri_Congruenza.pdf', '/uploads/materials/69bdad2d2327a_Esercizi_Criteri_Congruenza.pdf', NULL, NULL, '2026-03-20 20:25:17'),
(9, 5, 'file', 'Scomposizione_Polinomiale.pdf', 'Scomposizione_Polinomiale.pdf', '/uploads/materials/6a13f8b27ab25_Scomposizione_Polinomiale.pdf', NULL, NULL, '2026-05-25 07:22:26'),
(10, 5, 'file', 'Geometria_Parallelismo_Esercizi.pdf', 'Geometria_Parallelismo_Esercizi.pdf', '/uploads/materials/6a13f8ff9f2b1_Geometria_Parallelismo_Esercizi.pdf', NULL, NULL, '2026-05-25 07:23:43');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `package_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `package_id`, `amount`, `date`, `method`, `notes`, `created_at`) VALUES
(1, 5, 3, 120.00, '2025-12-15', 'Contanti', 'Pagamento primo pacchetto di 10 ore', '2026-02-24 23:52:24'),
(2, 5, NULL, 30.00, '2025-12-11', 'Contanti', 'Pagamento prima lezione, fuori pacchetto', '2026-02-24 23:53:58'),
(3, 5, 2, 120.00, '2026-01-26', 'Contanti', 'Pagamento secondo pacchetto', '2026-02-24 23:58:07'),
(9, 5, 4, 120.00, '2026-03-18', 'Contanti', 'Pagamento terzo pacchetto', '2026-03-18 17:59:28'),
(10, 5, 6, 120.00, '2026-05-04', 'Contanti', 'Pagamento quarto pacchetto', '2026-05-06 22:28:47'),
(11, 5, 7, 90.00, '2026-05-20', 'Contanti', 'Saldo ultimo pacchetto', '2026-09-24 18:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `lesson_id` int NOT NULL,
  `report_text` text,
  `homework` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_notes`
--

CREATE TABLE `student_notes` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_notes`
--

INSERT INTO `student_notes` (`id`, `student_id`, `content`, `created_at`, `updated_at`) VALUES
(2, 5, 'Durante le lezioni l\'allieva dimostra molta attenzione e, una volta correttamente indirizzata ed equipaggiata degli strumenti necessari, riesce a risolvere i vari problemi in modo adeguato.   Secondo me i nodi principali sui quali dover lavorare sono quelli del metodo e della fiducia ma sono certo che facendo sempre più esercizi potrà consolidare i vari concetti e acquisire maggiore fiducia nelle proprie capacità.', '2026-03-13 19:15:11', NULL),
(3, 5, 'L\'allieva dimostra un\'eccellente capacità di apprendimento rapido e un\'ottima comprensione dei nuovi concetti. Tuttavia, persistono alcuni errori di distrazione che ne compromettono la correttezza nello svolgimento delle espressioni polinomiali. Per migliorare la precisione, si consiglia vivamente lo svolgimento di esercizi supplementari rispetto a quelli scolastici, da effettuare con costanza anche al di fuori delle ore di doposcuola.', '2026-03-13 19:18:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `course_id`, `name`, `slug`, `description`, `sort_order`, `created_at`) VALUES
(1, 2, 'Basi di Dati', 'basi-di-dati', NULL, 0, '2026-02-26 23:32:38'),
(2, 4, 'Algebra', 'algebra', NULL, 0, '2026-09-19 14:24:53'),
(3, 4, 'Aritmetica', 'aritmetica', NULL, 0, '2026-09-19 14:25:00'),
(4, 4, 'Geometria', 'geometria', NULL, 0, '2026-09-19 14:25:07'),
(5, 2, 'Algoritmi e Strutture Dati', 'algoritmi-e-strutture-dati', NULL, 0, '2026-09-19 14:25:39'),
(6, 2, 'Linguaggio C', 'linguaggio-c', NULL, 0, '2026-09-22 14:37:31'),
(7, 2, 'Python', 'python', NULL, 0, '2026-09-22 14:37:35'),
(8, 2, 'Java', 'java', NULL, 0, '2026-09-22 14:37:38'),
(9, 2, 'Angular', 'angular', NULL, 0, '2026-09-22 14:37:50'),
(10, 2, 'React', 'react', NULL, 0, '2026-09-22 14:37:54'),
(11, 2, 'Javascript', 'javascript', NULL, 0, '2026-09-22 14:37:57'),
(12, 2, 'Git', 'git', NULL, 0, '2026-09-25 16:45:44'),
(13, 2, 'PHP', 'php', NULL, 0, '2026-09-25 16:46:05'),
(14, 2, 'C++', 'c', NULL, 0, '2026-09-25 16:46:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('ADMIN','STUDENT') DEFAULT 'STUDENT',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(3, 'Giorgio Di Fusco', 'admin@giorgiodifusco.it', '$2y$10$xgCYp5KMQ7XIaPfAmBJsxOvUdajJkEbAyciWj.SGEr9vuf8sug/FW', 'ADMIN', '2026-02-23 21:49:29'),
(5, 'Emma Bevilacqua', 'emma.bevilacqua@giorgiodifusco.it', '$2y$10$j8KXocJ9.aY8lGGHq9wzVumQ9rzfn0ZzMJg5lBDP0dlbjyQmOseqq', 'STUDENT', '2026-02-24 10:10:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_images`
--
ALTER TABLE `blog_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `fk_blog_posts_course` (`course_id`),
  ADD KEY `fk_blog_posts_subject` (`subject_id`),
  ADD KEY `fk_blog_posts_featured_image` (`featured_image_id`);

--
-- Indexes for table `blog_post_categories`
--
ALTER TABLE `blog_post_categories`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_courses_sort` (`sort_order`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fk_lessons_package` (`package_id`);

--
-- Indexes for table `lesson_packages`
--
ALTER TABLE `lesson_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_subject_course_slug` (`course_id`,`slug`),
  ADD KEY `idx_subjects_sort` (`sort_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_images`
--
ALTER TABLE `blog_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `lesson_packages`
--
ALTER TABLE `lesson_packages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_notes`
--
ALTER TABLE `student_notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_images`
--
ALTER TABLE `blog_images`
  ADD CONSTRAINT `blog_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_blog_posts_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_blog_posts_featured_image` FOREIGN KEY (`featured_image_id`) REFERENCES `blog_images` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_blog_posts_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_post_categories`
--
ALTER TABLE `blog_post_categories`
  ADD CONSTRAINT `blog_post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `fk_lessons_package` FOREIGN KEY (`package_id`) REFERENCES `lesson_packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_packages`
--
ALTER TABLE `lesson_packages`
  ADD CONSTRAINT `lesson_packages_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `lesson_packages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD CONSTRAINT `student_notes_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
