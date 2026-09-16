<?php
/*======================================================================+
 File name   : visual_fattura.php
 Begin       : 2010-08-04
 Last Update : 2011-04-08

 Description : View and edit bills

 Author: Sergio Capretta

 (c) Copyright:
               Sergio Capretta

               ITALY
               www.sinx.it
               info@sinx.it

Sinx for Association - Gestionale per Associazioni no-profit
    Copyright (C) 2011 by Sergio Capretta

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
=========================================================================+*/

session_start();
$user = $_SESSION['utente'];

if ($user == 'admin') {
  $limit = '';
} elseif ($user == 'limitato') {
  $limit = 'disabled';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port) or die("cannot connect DB");

// Funzione redirect
function redirect($url, $tempo = false) {
  if (!headers_sent()) {
    if ($tempo) {
      header("Refresh:$tempo;url=$url");
    } else {
      header("Location:$url");
    }
    exit;
  } else {
    echo "<meta http-equiv='refresh' content='" . ($tempo ?: 0) . ";url=$url'>";
  }
}

// Recupero ultimo ID
$Query = "SELECT MAX(id_tot_fatture) AS ultimo_id FROM tb_tot_fatture";
$Qultimoid = mysqli_query($connect, $Query);
$ultimoid = mysqli_fetch_assoc($Qultimoid)['ultimo_id'] ?? 0;

// Controllo parametri
$fattura = $_POST['id'] ?? '';
if ($fattura == "") {
  echo "<center><b>Numero fattura non selezionato</b><br>Inserisci un numero di fattura</center>";
  redirect('./InsFattura.php', 2);
  die();
}

$dataoggi = date('d-m-Y');

// Recupero nome cliente
$Query_nome = "SELECT nome FROM tb_fatture WHERE id_fatt = $fattura LIMIT 1";
$rs = mysqli_query($connect, $Query_nome);
$nomecl = mysqli_fetch_assoc($rs)['nome'] ?? 'Cliente non specificato';

// Recupero dati cliente
$query_cliente = "SELECT nome, indirizzo, cap, citta, provincia, nomerif FROM tb_anagrafe WHERE nome = '$nomecl'";
$comando = mysqli_query($connect, $query_cliente);
$cliente = mysqli_fetch_assoc($comando);

// Calcolo totale fattura
$query_tot = "SELECT SUM(totale) as totale_numero, MAX(modpaga) as modpagamento FROM tb_fatture WHERE id_fatt = $fattura";
$result = mysqli_query($connect, $query_tot);
$fatturaData = mysqli_fetch_assoc($result);
$totale = $fatturaData['totale_numero'] ?? 0;
$modpagamento = $fatturaData['modpagamento'] ?? 'N/D';
?>

<div class="content-section">
  <div class="card">

    <!-- === HEADER RIASSUNTIVO FATTURA === -->
    <div class="card" style="background: linear-gradient(135deg, #778dff, #233592); color:white; text-align:center; padding:20px; border-radius:12px; margin-bottom:25px;">
      <h2 style="margin-bottom:10px;">Fattura n. <?php echo $fattura; ?></h2>
      <p style="margin:5px 0;">Data: <b><?php echo $dataoggi; ?></b></p>
      <p style="margin:5px 0;">Cliente: <b><?php echo htmlspecialchars($nomecl); ?></b></p>
      <p style="margin:5px 0;">Modalità di pagamento: <b><?php echo htmlspecialchars($modpagamento); ?></b></p>
      <p style="margin:5px 0; font-size:1.2em;">Totale: <b><?php echo number_format($totale, 2, ',', '.'); ?> €</b></p>
    </div>


    <div class="form-buttons" style="margin-bottom:20px;">
      <form action="./InsFattura.php" method="GET">
        <input type="submit" value="Ritorna gestione fatture" class="btn-edit">
      </form>
    </div>

    <!-- === BLOCCO CANCELLA === -->
    <h3 class="card-title">Cancella Riga</h3>
    <p class="card-subtitle">Inserisci l’ID della riga da eliminare</p>

    <form action="./conf_canc.php?Tabella=tb_fatture&Riferimento=id_riga_art" method="POST" class="sinx-form">
      <label for="id_mod">ID Riga*</label>
      <select name="id_mod" required>
        <option value="">Seleziona...</option>
        <?php
        $query = "SELECT id_riga_art FROM tb_fatture WHERE id_fatt = $fattura ORDER BY id_riga_art";
        $rs = mysqli_query($connect, $query);
        while ($row = mysqli_fetch_assoc($rs)) {
          echo "<option value='{$row['id_riga_art']}'>{$row['id_riga_art']}</option>";
        }
        ?>
      </select>
      <div class="form-buttons">
        <input type="submit" value="Cancella" class="btn-delete" <?php echo $limit; ?>>
      </div>
    </form>

    <hr class="divider">

    <!-- === BLOCCO MODIFICA === -->
    <h3 class="card-title">Modifica Riga</h3>
    <p class="card-subtitle">Seleziona la riga e aggiorna il campo desiderato</p>

    <form action="./conf_mod_fattura.php" method="POST" class="sinx-form">
      <label for="id_mod">ID Riga*</label>
      <select name="id_mod" required>
        <option value="">Seleziona...</option>
        <?php
        $rs = mysqli_query($connect, $query);
        while ($row = mysqli_fetch_assoc($rs)) {
          echo "<option value='{$row['id_riga_art']}'>{$row['id_riga_art']}</option>";
        }
        ?>
      </select>

      <label for="campo">Campo da modificare*</label>
      <select name="campo" required>
        <option value="">Scegli campo...</option>
        <option value="quantita">Quantità</option>
        <option value="descr">Descrizione</option>
        <option value="euro">Prezzo Unitario</option>
        <option value="iva">IVA</option>
        <option value="modpaga">Modalità di Pagamento</option>
      </select>

      <label for="record">Nuovo valore*</label>
      <input type="text" name="record" required>

      <div class="form-buttons">
        <input type="submit" value="Modifica" class="btn-edit" <?php echo $limit; ?>>
      </div>
    </form>

    <hr class="divider">

    <!-- === DATI CLIENTE === -->
    <?php if ($cliente): ?>
    <div class="card" style="background:#f9f9f9; margin-bottom:25px;">
      <h3>Committente</h3>
      <p><b><?php echo htmlspecialchars($cliente['nome']); ?></b></p>
      <p><?php echo htmlspecialchars($cliente['indirizzo']); ?><br>
      <?php echo $cliente['cap'] . " " . $cliente['citta'] . " (" . $cliente['provincia'] . ")"; ?><br>
      <small>Rif: <?php echo htmlspecialchars($cliente['nomerif']); ?></small></p>
    </div>
    <?php endif; ?>

    <!-- === DETTAGLIO ARTICOLI === -->
    <?php
    $Query_tab = "SELECT id_riga_art, quantita, descr, data, euro, iva, totale
                  FROM tb_fatture WHERE id_fatt = '$fattura'";
    $ris = mysqli_query($connect, $Query_tab);
    ?>

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>ID</span>
        <span>Quantità</span>
        <span>Descrizione</span>
        <span>Totale</span>
      </div>

      <?php while ($riga = mysqli_fetch_assoc($ris)): ?>
        <div class="appointment-item">
          <span><?php echo $riga['id_riga_art']; ?></span>
          <span><?php echo $riga['quantita']; ?></span>
          <span><?php echo htmlspecialchars($riga['data'] . " - " . $riga['descr']); ?></span>
          <span><?php echo number_format($riga['totale'], 2, ',', '.'); ?> €</span>
        </div>
      <?php endwhile; ?>
    </div>

  </div>
</div>

<!-- Pulsante di stampa -->
<form action="./stampa_fattura.php" method="post" target="_blank" style="text-align:center; margin-top:20px;">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($fattura); ?>">
  <input type="submit" value="🖨️ Stampa Fattura" class="btn-add">
</form>

<form action="./export_fattura_xml.php" method="post" style="text-align:center; margin-top:10px;">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($fattura); ?>">
  <input type="submit" value="📤 Esporta XML fattura elettronica" class="btn-edit">
</form>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
