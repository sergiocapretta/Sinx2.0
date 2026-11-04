# Sinx for Association 2.0

Gestionale web per associazioni senza scopo di lucro, sviluppato in PHP e MySQL.

**Autore:** Sergio Capretta  
**Repository:** [sergiocapretta/Sinx2.0](https://github.com/sergiocapretta/Sinx2.0)  
**Licenza:** GNU General Public License v3 o successive (vedere [LICENSE](LICENSE)).

Sinx riunisce in un'unica applicazione la gestione anagrafica, associativa, amministrativa, contabile e documentale di un'associazione. L'interfaccia è disponibile in italiano e inglese.

> Questa documentazione descrive la struttura del repository e le funzioni individuate nel codice. Non sostituisce il manuale operativo PDF presente in `Download/Manuale_Sinx 2.pdf`. Alcune parti del programma sono legacy e sono in corso di verifica per la compatibilità con le versioni PHP più recenti.

---

## 1. Funzionalità principali

- **Anagrafica:** gestione di fondatori, associati e contatti extra; schede personali, ricerca, importazione CSV, rubriche e stampe.
- **Gestione associativa:** dati dell'associazione, anno sociale, tipologie e funzioni degli associati, regioni, province e comuni.
- **Contabilità:** prima nota, ricevute, quietanze, fatture, preventivi, conto economico, stato patrimoniale e rendiconti.
- **Gestione attività:** calendario e appuntamenti, progetti, moduli, blocco note e comunicazioni e-mail.
- **Archivio documentale:** caricamento, consultazione e download di file e immagini.
- **Amministrazione:** gestione utenti, registri di attività, backup e ripristino del database, diagnostica.
- **Assistente AI (opzionale):** interfaccia configurabile per provider esterni, con categorie di dati autorizzabili dall'amministratore.

Le funzioni effettivamente accessibili dipendono dal profilo dell'utente e dalle autorizzazioni applicate nelle singole pagine.

## 2. Requisiti tecnici

### Componenti applicativi

- Server web Apache (o ambiente PHP equivalente).
- PHP con estensione **mysqli**.
- MySQL o MariaDB.
- Estensioni PHP utilizzate dalle funzioni attive: OpenSSL, JSON e, in base alle operazioni, GD, mbstring, XML, SimpleXML e ZIP.
- Browser moderno con JavaScript abilitato.
- Permessi di lettura/scrittura sulle cartelle utilizzate per upload, documenti, log e backup.

### Ambiente verificato

La diagnostica eseguita sull'installazione in uso ha rilevato:

- PHP **8.4.27**
- MySQL **8.0.44**
- connessione MySQLi funzionante
- charset della connessione: **utf8mb4**

Questi dati descrivono l'ambiente verificato, non costituiscono una garanzia di compatibilità di ogni script. Il codice nasce in un periodo in cui erano diffuse versioni PHP precedenti e alcuni file conservano convenzioni legacy. Prima di aggiornare PHP o MySQL in produzione è opportuno eseguire test funzionali e un backup completo.

## 3. Struttura del progetto

I file PHP sono collocati prevalentemente nella cartella principale. Le cartelle contengono risorse, modelli, documenti e file di supporto.

| Percorso | Contenuto e funzione |
|---|---|
| `/` | Pagine applicative, procedure di elaborazione, configurazione e script di stampa |
| `Install/` | Procedura guidata di installazione e configurazione iniziale |
| `Download/` | Manuale PDF e documenti generati o resi disponibili al download |
| `ImmTemplate/` | Logo, sfondi, icone, pulsanti e risorse grafiche dell'interfaccia |
| `ImmTemplate/Pulsanti/` | Immagini dei pulsanti e delle voci del menu |
| `Immagini/` | Immagini utilizzate dall'applicazione |
| `Immagini/Utenti/` | Immagini associate alle schede anagrafiche |
| `DocTemplate/` | Modelli per documenti e importazioni |
| `lang/ita/` | Etichette e testi dell'interfaccia italiana |
| `lang/eng/` | Etichette e testi dell'interfaccia inglese |
| `clock/` | Script JavaScript dell'orologio |
| `menu_assets/` | Script JavaScript per il menu |

### 3.1 Avvio, autenticazione e struttura comune

| File | Funzione |
|---|---|
| `index.php` | Pagina iniziale di accesso; scelta della lingua e credenziali |
| `Conf_Login.php` | Verifica delle credenziali e inizializzazione della sessione |
| `index2.php` | Pagina principale dopo l'accesso; riepiloghi, note, appuntamenti e assistente AI |
| `logout.php` | Chiusura della sessione |
| `top.inc` | Intestazione comune, informazioni dell'associazione, menu principale, meteo e orologio; registra inoltre informazioni di accesso nel log |
| `menu.inc` | Impostazioni comuni di visualizzazione degli errori |
| `menusx.inc` | Menu secondario e inclusione del calendario mensile |
| `botton.inc` | Elementi comuni a fondo pagina |
| `style.css`, `style_stampa.css` | Stili dell'interfaccia e delle stampe |
| `Redirect_no_enter.php` | Pagina di gestione dei tentativi di accesso a funzioni non autorizzate |
| `Intestazione.php` | Elementi di intestazione utilizzati dalle pagine |
| `conferma.php`, `errore.php` | Pagine di esito delle procedure |

### 3.2 Anagrafica e associati

| File | Funzione |
|---|---|
| `InsAnagrIns.php` | Modulo di inserimento di un associato |
| `InsAnagrStud.php` | Modulo di inserimento di un fondatore |
| `InsAnagrExtra.php` | Modulo di inserimento di un contatto extra |
| `InsAnagrFile.php` | Pagina di importazione anagrafiche da CSV |
| `DatiComuni.inc` | Campi anagrafici condivisi dai moduli di inserimento |
| `conf_dati_ins.php` | Elaborazione e registrazione di un associato |
| `conf_dati_stud.php` | Elaborazione e registrazione di un fondatore |
| `conf_dati_extra.php` | Elaborazione e registrazione di un contatto extra |
| `conf_importacsv.php`, `importacsv.php` | Elaborazione e gestione dell'importazione CSV |
| `Scheda_associato.php` | Scheda dell'associato, documenti emessi e operazioni disponibili |
| `Scheda_associato_exp.php` | Versione precedente/alternativa della scheda associato |
| `modifica_cancella.inc` | Componenti per modifica e cancellazione delle schede |
| `conf_canc_associato.php` | Procedura di cancellazione di una scheda associato |
| `conf_mod_ass_attivo.php` | Modifica dello stato dell'associato |
| `ricerca.php` | Maschera di ricerca anagrafica e ricevute |
| `ricerca_nome_stud.php` | Ricerca per iniziale e tipologia anagrafica |
| `ricerca_classe_stud.php` | Ricerca per funzione/classe |
| `ricerca_tipo_ass.php` | Ricerca per tipologia di associato |
| `Rubrica.php` | Consultazione della rubrica |
| `pre_stampa_soci.php` | Preparazione delle stampe dell'elenco soci |

### 3.3 Dati dell'associazione e tabelle di supporto

| File | Funzione |
|---|---|
| `dati_Associaz.php` | Consultazione e gestione dei dati dell'associazione |
| `conf_logo_associaz.php` | Gestione del logo dell'associazione |
| `Scheda_regioni.php`, `conf_regioni.php` | Gestione delle regioni |
| `Scheda_province.php`, `conf_province.php` | Gestione delle province |
| `Scheda_comuni.php`, `conf_comuni.php` | Gestione dei comuni |
| `nclasse.php`, `nmateria.php` | Gestione delle funzioni/classi e delle tipologie degli associati |
| `nclasse_exp.php`, `nmateria_exp.php` | Versioni alternative/precedenti delle relative funzioni |
| `Nuovo_Anno_soc.php`, `Stampa_Anno.php` | Gestione e stampa delle operazioni relative all'anno sociale |
| `cancella_anno_sociale.php` | Procedura di cancellazione/azzeramento dell'anno sociale |

### 3.4 Contabilità e documenti amministrativi

| File | Funzione |
|---|---|
| `InsPrimanota.php`, `conf_dati_pnota.php` | Inserimento e registrazione dei movimenti di prima nota |
| `InsRicFisc.php`, `conf_Ric_Fiscale.php` | Emissione e registrazione di ricevute/quietanze |
| `InsFattura.php`, `conf_dati_fattura.php` | Inserimento e registrazione delle fatture |
| `fattura_form.php`, `visual_fattura.php` | Composizione e visualizzazione della fattura |
| `stampa_fattura.php`, `export_fattura_xml.php`, `genera_xml.php` | Stampa ed esportazione dei dati di fatturazione |
| `import_fattura_xml.php` | Importazione di fatture XML |
| `incasso_fattura.php` | Registrazione dell'incasso di una fattura |
| `InsPreventivo.php`, `ConfPreventivo.php` | Creazione e conferma dei preventivi |
| `InsContoEconomico.php`, `conf_dati_contoec.php` | Gestione del conto economico |
| `InsStatoPatrimoniale.php`, `conf_dati_patrim.php` | Gestione dello stato patrimoniale |
| `InsBilancioEconomicoAps.php`, `conf_dati_BilancioEcAps.php` | Bilancio economico per APS |
| `InsBilancioEconomicoOdv.php`, `conf_dati_BilancioEcOdv.php` | Bilancio economico per ODV |
| `Rendiconto.php` | Consultazione e composizione del rendiconto |
| `stampa_contoec.php`, `stampa_pnota.php`, `stampa_statopat.php` | Stampe contabili |
| `stampa_rfisc.php`, `stampa_ric_fisc.php`, `stampa_ric_fisc_a4.php` | Stampe e riepiloghi fiscali |
| `ricerca_ricevute.php` | Ricerca delle ricevute emesse |

### 3.5 Calendario, comunicazioni e gestione documentale

| File | Funzione |
|---|---|
| `Calendario.php`, `Calendario2.php` | Viste e gestione del calendario |
| `Mese.php` | Calendario mensile richiamato dall'interfaccia |
| `DettCal.php` | Dettaglio di un appuntamento |
| `Canc_Calendario.php`, `conf_canc_cal.php`, `conf_canc_all_cal.php` | Cancellazione di appuntamenti o eventi |
| `importa_calendario.php`, `stampa_calendario.php` | Importazione e stampa del calendario |
| `CompModuli.php`, `gen_moduli.php` | Composizione e generazione di moduli |
| `InsProgetto.php`, `conf_dati_progetto.php` | Gestione dei progetti |
| `InsNotepad.php`, `conf_note.php` | Blocco note della pagina principale |
| `Comp_email.php`, `email.php` | Composizione e invio di e-mail |
| `Posta_inviata.php`, `Posta_ricevuta.php` | Pagine di consultazione della posta |
| `Files.php`, `gest_files.php`, `conf_files.php` | Gestione dell'archivio documenti |
| `download_file.php`, `delete_file.php` | Download e cancellazione dei file archiviati |
| `stampa_extra.php`, `stampa_soci.php`, `stampa_lista_tessere.php`, `stampa_scheda_ass.php` | Stampe anagrafiche e associative |

### 3.6 Amministrazione, manutenzione e diagnostica

| File | Funzione |
|---|---|
| `InsUtente.php`, `conf_dati_utente.php` | Gestione degli utenti |
| `Log.php`, `CancLog.php` | Consultazione e cancellazione del log |
| `Backup_database.php` | Creazione del backup del database |
| `restore_db.php`, `conf_ripBack.php` | Ripristino del database da backup |
| `Azzera.php` | Funzioni di azzeramento dei dati |
| `aggiorna_db.php` | Aggiornamento del database |
| `diagnostica.php` | Controlli diagnostici su PHP, connessione, tabelle, scrittura e cartelle |
| `Licenza.php`, `Licenza_eng.php` | Informazioni sulla licenza |

### 3.7 Assistente AI (opzionale)

| File | Funzione |
|---|---|
| `ai_chat.php` | Riceve la domanda dalla pagina principale, verifica le autorizzazioni e prepara il contesto ammesso |
| `ai_config.php` | Pannello riservato all'amministratore per configurare provider, modello, chiave e categorie consultabili |
| `ai_provider.php` | Adattatore per le API dei provider supportati |
| `ai_dati_db.inc.example` | Esempio di configurazione per l'accesso ai dati AI |
| `ai_dati_db.inc` | Configurazione locale dell'accesso ai dati dell'assistente, se prevista |

Provider contemplati dal codice: OpenAI, Anthropic, Google Gemini e Qwen. L'uso richiede una chiave API valida e può comportare costi e trattamento dei dati da parte del provider selezionato.

La configurazione salvata dal pannello viene scritta nel file locale nascosto `.sinx_ai_settings.php`. Tale file contiene una chiave segreta: non deve essere pubblicato nel repository né distribuito in backup accessibili a terzi.

## 4. Configurazione

### 4.1 Connessione al database

La connessione principale è definita in `dati_db.inc`. Prima dell'avvio occorre configurare i parametri richiesti dal proprio hosting:

- host MySQL;
- nome utente;
- password;
- nome del database.

Il file presente nel repository contiene segnaposto vuoti. Inserire le credenziali effettive solo nell'installazione privata sul server. Non pubblicare mai password o credenziali reali su GitHub.

Alcuni script passano anche la variabile `$port` a `mysqli_connect()`: verificare il valore richiesto dal proprio hosting e la coerenza delle connessioni fra i diversi script.

### 4.2 Assistente AI

L'assistente è facoltativo. Se non viene configurato, le altre funzioni del gestionale possono essere utilizzate indipendentemente dal servizio AI.

La configurazione si effettua dal menu amministrativo **Gestione → Configurazione AI**. La pagina consente di scegliere il provider, il modello, l'endpoint ove necessario, la chiave API e le categorie consultabili.

Non inserire chiavi API in file versionati. Proteggere i file di configurazione e verificare le policy privacy e i termini del provider scelto.

## 5. Installazione e aggiornamento

Il repository include una procedura guidata nella cartella `Install/`. I passaggi visibili nel codice comprendono la creazione/configurazione dell'utente iniziale, il caricamento del logo e l'inserimento dei dati dell'associazione.

La procedura completa dipende anche dalle istruzioni e dalle strutture SQL contenute nell'installer e nel manuale. Prima di installare:

1. predisporre un database MySQL/MariaDB dedicato;
2. caricare i file sul server web;
3. configurare `dati_db.inc` con i parametri del database;
4. verificare i permessi di scrittura delle cartelle richieste;
5. avviare la procedura in `/Install` e seguire i passaggi;
6. completare l'installazione e verificare l'accesso;
7. proteggere o rimuovere l'accesso pubblico alla cartella `Install/` secondo le esigenze dell'hosting.

**Attenzione:** non eseguire una nuova installazione sopra un'installazione già operativa. Prima di aggiornare file o database, effettuare un backup sia del database sia dei file locali di configurazione e degli archivi.

Il manuale PDF incluso nel progetto contiene le istruzioni operative storiche. Alcune schermate o indicazioni potrebbero non corrispondere più esattamente alla versione corrente dell'interfaccia.

## 6. Database e dati

Sinx utilizza tabelle MySQL per anagrafiche, utenti, dati dell'associazione, movimenti contabili, documenti, calendario e tabelle di supporto.

I nomi delle tabelle effettivamente presenti possono variare in base alla versione installata e agli aggiornamenti applicati. Per questo motivo, prima di intervenire sul database:

- verificare la struttura reale con `SHOW CREATE TABLE`;
- non basarsi esclusivamente su vecchi dump o definizioni presenti in documentazione non aggiornata;
- eseguire backup prima di modifiche strutturali o cancellazioni;
- verificare i risultati delle query prima di considerare conclusa un'operazione.

La pagina `diagnostica.php` offre controlli di base sulla connessione, versione del server, tabelle, estensioni PHP, limiti e permessi delle cartelle. Il test di scrittura utilizza una tabella diagnostica dedicata: non sostituisce un test delle singole procedure applicative.

## 7. Ruoli e accessi

Il codice utilizza profili applicativi quali:

- `admin`: amministratore;
- `operatore`: operatore;
- `limitato`: utente con funzioni limitate;
- `associato`: accesso associato.

Le autorizzazioni non sono gestite da un unico modulo centrale: diverse pagine verificano il profilo direttamente. Quando si aggiungono o modificano funzioni, occorre controllare sia l'accesso alla pagina sia quello agli script che elaborano le richieste.

## 8. Cartelle e permessi

Verificare in particolare i permessi e la protezione di:

- `Immagini/Utenti/`: immagini delle schede;
- `Download/`: documenti e file generati;
- cartelle di backup previste dall'installazione;
- `log/`: registri di attività;
- `tmp/`: eventuali file temporanei e diagnostici.

Le cartelle che contengono backup, log, file temporanei o configurazioni non dovrebbero essere accessibili liberamente dal web. La configurazione di protezione dipende dal server (Apache, Nginx o pannello hosting).

## 9. Note tecniche e manutenzione

- Il progetto contiene script PHP scritti in epoche diverse: sono presenti procedure legacy e procedure aggiornate più recentemente.
- Prima di correggere uno script, ricostruire il flusso tra pagina del modulo, file di elaborazione, database e pagina di esito.
- Non considerare sufficiente il fatto che la connessione al database funzioni: ogni query e ogni tabella coinvolta devono essere verificate.
- Per gli errori HTTP 500 consultare i log PHP/server e verificare eventuali eccezioni MySQLi non gestite.
- I dati ricevuti via `GET`, `POST` e `FILES` devono essere validati; per le query usare statement preparati.
- Per le password e la gestione delle sessioni è opportuno verificare le procedure legacy prima di esporre l'applicazione a nuovi ambienti o utenti.

## 10. Licenza

Sinx è distribuito secondo i termini della **GNU General Public License versione 3 o successiva**. Consultare il file [LICENSE](LICENSE) per il testo completo.

---

*README ricostruito sulla base della struttura e dei sorgenti presenti nel repository. Le descrizioni dei singoli file indicano la funzione prevalente ricavata dal codice e dai collegamenti dell'applicazione; non costituiscono una certificazione di sicurezza o di compatibilità di ogni singola procedura.*
