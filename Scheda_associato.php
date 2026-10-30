<?php
/*======================================================================+
 File name   : Scheda_associato.php
 Description : Scheda anagrafica associato con documenti emessi
=========================================================================+*/
session_start();

$user = $_SESSION['utente'] ?? null;

// Gestione permessi
$limite = '';
if ($user === 'limitato' || $user === 'operatore' || $user === 'associato') {
  $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');

$associato = (int)($_POST['associato'] ?? 0);

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// --- Recupero dati associato ---
$query_assoc = "SELECT * FROM tb_anagrafe WHERE id_anagrafe = $associato";
$rs = mysqli_query($connect, $query_assoc)
  or die("Errore nella query $query_assoc: " . mysqli_error($connect));
$row = mysqli_fetch_assoc($rs);

if (!$row) {
  echo "<p class='text-center'><b>Nessun associato trovato.</b></p>";
  mysqli_close($connect);
  exit;
}

// Gestione immagine
$foto = "./Immagini/Utenti/" . $row['immagine'];
if (!file_exists($foto) || empty($row['immagine'])) {
  $foto = "/ImmTemplate/nofoto.png";
}

$nome = $row['nome'];
$cognome = $row['cognome'];
$nome_completo = trim($cognome . ' ' . $nome);
$nome_db = mysqli_real_escape_string($connect, $nome);
$nome_completo_db = mysqli_real_escape_string($connect, $nome_completo);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Scheda Associato</title>
  <link rel="stylesheet" href="/style.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<div class="content-section">
  <div class="card">

    <h2 class="card-title"><i class="material-icons">person</i> Scheda Associato</h2>
    <hr class="divider">

    <form method="POST" action="./Scheda_associato_exp.php" class="text-center">
      <h4>
        Stampa | Cancella | Modifica
        <input type="submit" <?php echo $limite; ?> name="associato" value="<?php echo $associato; ?>" class="btn-mini">
      </h4>
    </form>

    <!-- === BLOCCO FOTO + DATI BASE === -->
    <div class="flex items-center justify-center gap-6 my-4">
      <div class="photo-box">
        <img src="<?php echo htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto associato" class="profile-photo">
      </div>
      <div class="text-left">
        <h3><?php echo htmlspecialchars($nome . ' ' . $cognome, ENT_QUOTES, 'UTF-8'); ?></h3>
        <p><b>ID:</b> <?php echo (int)$row['id_anagrafe']; ?> | <b>Tessera:</b> <?php echo htmlspecialchars($row['ntessera'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><b>Email:</b> <?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><b>Tipo Socio:</b> <?php echo htmlspecialchars($row['materia'], ENT_QUOTES, 'UTF-8'); ?></p>
      </div>
    </div>

    <hr class="divider">

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>Campo</span>
        <span>Valore</span>
        <span></span>
        <span></span>
      </div>

      <div class="appointment-item">
        <span><b>Indirizzo</b></span>
        <span><?php echo htmlspecialchars($row['indirizzo'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Comune:</b> <?php echo htmlspecialchars($row['cap'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Regione:</b> <?php echo htmlspecialchars($row['citta'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Provincia</b></span>
        <span><?php echo htmlspecialchars($row['provincia'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Telefono:</b> <?php echo htmlspecialchars($row['tel'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Telefono 2:</b> <?php echo htmlspecialchars($row['tel2'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Codice Fiscale</b></span>
        <span><?php echo htmlspecialchars($row['nomerif'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Data nascita:</b> <?php echo htmlspecialchars($row['datan'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Carica Amm.:</b> <?php echo htmlspecialchars($row['classe'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Mansione</b></span>
        <span><?php echo htmlspecialchars($row['mansione'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span><b>Note:</b></span>
        <span><?php echo htmlspecialchars($row['note'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>

      <div class="appointment-item">
        <span><b>Socio attivo</b></span>
        <span>
          <form method="post" action="./conf_mod_ass_attivo.php">
            <input type="hidden" name="id_associato" value="<?php echo (int)$row['id_anagrafe']; ?>">
            <input type="submit" <?php echo $limite; ?> name="attivo" value="<?php echo htmlspecialchars($row['associato'], ENT_QUOTES, 'UTF-8'); ?>" class="btn-mini">
          </form>
        </span>
        <span colspan="2"><small><i><-- clicca per cambiare</i></small></span>
      </div>
    </div>

    <hr class="divider">

    <?php
    // === RICEVUTE ===
    // Nella struttura attuale tb_ricevute non contiene id_anagrafe:
    // vengono quindi considerate entrambe le convenzioni storiche del campo nome.
    $query_ricevute = "SELECT * FROM tb_ricevute
                       WHERE nome IN ('$nome_completo_db', '$nome_db')
                       ORDER BY data DESC, id_ric DESC";
    $rb = mysqli_query($connect, $query_ricevute)
      or die("Errore nella query ricevute: " . mysqli_error($connect));

    if (mysqli_num_rows($rb) > 0) {
      echo "<h3 class='card-title'><i class='material-icons'>receipt</i> Ricevute Emesse</h3>";
      echo "<div class='appointments appointments-4col appointments-receipts'>";
      echo "<div class='appointments-header'>
              <span>Num.</span><span>Data</span><span>Importo</span><span>Descrizione</span>
            </div>";

      while ($ric = mysqli_fetch_assoc($rb)) {
        echo "<div class='receipt-item'>
                <span>" . (int)$ric['id_ric'] . "</span>
                <span>" . htmlspecialchars($ric['data'], ENT_QUOTES, 'UTF-8') . "</span>
                <span class='val-entrata'>" . htmlspecialchars($ric['euro'], ENT_QUOTES, 'UTF-8') . " €</span>
                <span>" . htmlspecialchars($ric['descr'], ENT_QUOTES, 'UTF-8') . "</span>
              </div>";
      }
      echo "</div><hr class='divider'>";
    }

    // === FATTURE ===
    // Le fatture storiche sono associate al nome presente in tb_anagrafe.
    $query_fatture = "SELECT id_tot_fatture, data, tot_fattura
                      FROM tb_tot_fatture
                      WHERE nome = '$nome_db'
                      ORDER BY data DESC, id_tot_fatture DESC";
    $rf = mysqli_query($connect, $query_fatture)
      or die("Errore nella query fatture: " . mysqli_error($connect));

    if (mysqli_num_rows($rf) > 0) {
      echo "<h3 class='card-title'><i class='material-icons'>description</i> Fatture Emesse</h3>";
      echo "<div class='appointments appointments-4col'>";
      echo "<div class='appointments-header'>
              <span>Numero</span><span>Data</span><span>Totale (IVA incl.)</span><span></span>
            </div>";

      while ($fatt = mysqli_fetch_assoc($rf)) {
        echo "<div class='appointment-item'>
                <span>" . htmlspecialchars($fatt['id_tot_fatture'], ENT_QUOTES, 'UTF-8') . "</span>
                <span>" . htmlspecialchars($fatt['data'], ENT_QUOTES, 'UTF-8') . "</span>
                <span>" . htmlspecialchars($fatt['tot_fattura'], ENT_QUOTES, 'UTF-8') . " €</span>
                <span></span>
              </div>";
      }
      echo "</div><hr class='divider'>";
    }

    // === PREVENTIVI SALVATI ===
    // ConfPreventivo.php salva i file come Preventivo_<id_anagrafe>_<data>.html.
    $cartella_preventivi = __DIR__ . '/Download';
    $preventivi = array();

    if (is_dir($cartella_preventivi)) {
      $elementi = scandir($cartella_preventivi);
      $prefisso = 'Preventivo_' . (int)$row['id_anagrafe'] . '_';

      foreach ($elementi as $elemento) {
        if (preg_match('/^' . preg_quote($prefisso, '/') . '([0-9]{8})\.html$/i', $elemento, $match)) {
          $preventivi[] = array(
            'file' => $elemento,
            'data' => $match[1]
          );
        }
      }
    }

    usort($preventivi, function ($a, $b) {
      return strcmp($b['data'], $a['data']);
    });

    if (count($preventivi) > 0) {
      echo "<h3 class='card-title'><i class='material-icons'>request_quote</i> Preventivi Emessi</h3>";
      echo "<div class='appointments appointments-4col'>";
      echo "<div class='appointments-header'>
              <span>Data</span><span>Documento</span><span></span><span></span>
            </div>";

      foreach ($preventivi as $preventivo) {
        $data_file = $preventivo['data'];
        $data_visualizzata = substr($data_file, 0, 2) . '-' . substr($data_file, 2, 2) . '-' . substr($data_file, 4, 4);
        $link = './Download/' . rawurlencode($preventivo['file']);

        echo "<div class='appointment-item'>
                <span>" . htmlspecialchars($data_visualizzata, ENT_QUOTES, 'UTF-8') . "</span>
                <span><a href='" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "' target='_blank' rel='noopener'>Apri preventivo</a></span>
                <span></span><span></span>
              </div>";
      }
      echo "</div><hr class='divider'>";
    }
    ?>

    <div class="text-center" style="margin-top:30px;">
      <a href="./ricerca.php" class="btn-add">← Vai alla ricerca</a>
    </div>

  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
?>
<hr>
<img src='./Immagini/suggerimento.png'>
<small><i>Scorrendo in fondo, è possibile visualizzare i documenti emessi all'associato.</i></small>
<?php include('./botton.inc'); ?>
