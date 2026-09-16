<?php
/*Sinx for Association - Gestionale per Associazioni no-profit
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
*/


session_start();

$user = $_SESSION['utente'];
if ($user) {
  include('./top.inc');
  include('./menu.inc');

  include('./dati_db.inc');
  $connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die("cannot connect DB");

  // Redirect helper (lasciata come avevi)
  function redirect($url, $tempo = FALSE) {
    if (!headers_sent() && $tempo === FALSE) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo !== FALSE) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      if ($tempo === FALSE) $tempo = 0;
      echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
    }
  }

  // Input
  $tipo  = $_POST['ricevute'] ?? '';
  $campo = $_POST['campo'] ?? '';

  // Controllo campo obbligatorio
  if (trim($tipo) === '') {
    echo "<div class='card' style='max-width:900px;margin:20px auto;'><p style='text-align:center;'><b>Il campo di ricerca &egrave; obbligatorio</b></p></div>";
    redirect('./ricerca.php', 2);
    exit;
  }

  // Whitelist dei campi ammessi (evita injection via nome colonna)
  $allowed_fields = ['nome','descr','euro','data'];
  if (!in_array($campo, $allowed_fields, true)) {
    echo "<div class='card' style='max-width:900px;margin:20px auto;'><p style='text-align:center;color:#c00;'><b>Campo di ricerca non valido</b></p></div>";
    exit;
  }



  // Prepared statement: usa LIKE con wildcard
  $fieldMap = [
  'nome' => 'tb_ricevute.nome',
  'descr' => 'tb_ricevute.descr',
  'euro' => 'tb_ricevute.euro',
  'data' => 'tb_ricevute.data'
];
$field = $fieldMap[$campo] ?? 'tb_ricevute.nome';

$sql = "SELECT tb_ricevute.*, tb_anagrafe.id_anagrafe
        FROM tb_ricevute
        JOIN tb_anagrafe ON tb_ricevute.nome = tb_anagrafe.nome
        WHERE $field LIKE ?
        ORDER BY tb_ricevute.data DESC, tb_ricevute.id_ric DESC";


  if (!$stmt = mysqli_prepare($connect, $sql)) {
    die("Errore nella preparazione della query: " . mysqli_error($connect));
  }

  $like = '%' . $tipo . '%';
  mysqli_stmt_bind_param($stmt, 's', $like);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  if ($res === false) {
    die("Errore nell'esecuzione della query: " . mysqli_error($connect));
  }

  // Output moderno
  echo "<div class='card' style='max-width:1100px; margin:20px auto;'>";
  echo "<h2 class='card-title'>Elenco Associati con <i>" . htmlspecialchars($tipo) . "</i></h2>";

  // Header griglia
  echo "<div class='appointments appointments-receipts'>";
  echo "<div class='appointments-header'>";
  echo "<span>Vedi</span><span>Formato</span><span>Scheda</span><span>Nome</span><span>Data</span><span>Euro</span><span>Descrizione</span>";
  echo "</div>";

  // Righe risultati
  while ($row = mysqli_fetch_assoc($res)) {
    $id_ric = htmlspecialchars($row['id_ric']);
    $id_an = htmlspecialchars($row['id_anagrafe']);
    $nome = htmlspecialchars($row['nome']);
    $data = htmlspecialchars($row['data']);
    $euro = htmlspecialchars($row['euro']);
    $descr = htmlspecialchars($row['descr']);

    echo "<div class='appointment-item receipt-item'>";

    // Form per stampa ricevuta (apre in nuova finestra)
    echo "<span>";
    echo "<form method='post' action='./stampa_rfisc.php' target='_blank' style='margin:0; display:inline-block;'>";
    echo "<input type='hidden' name='numero' value='{$id_ric}'>";
    echo "<button type='submit' class='btn-mini'>".$id_ric."</button>";
    echo "</form>";
    echo "</span>";

    // Scelta formato (radio) — separata in piccolo form che invia con JS se necessario, qui solo visuale
    echo "<span>";
    echo "<form style='margin:0; display:inline-block;'>";
    echo "<label style='margin-right:6px; font-size:0.9rem;'><input type='radio' name='tipo_{$id_ric}' value='immagine' checked> <small>immagine</small></label>";
    echo "<label style='font-size:0.9rem;'><input type='radio' name='tipo_{$id_ric}' value='foglio'> <small>pagina</small></label>";
    echo "</form>";
    echo "</span>";

    // Form per aprire scheda associato
    echo "<span>";
    echo "<form method='post' action='./Scheda_associato.php' style='margin:0; display:inline-block;'>";
    echo "<input type='hidden' name='associato' value='{$id_an}'>";
    echo "<button type='submit' class='btn-mini'>{$id_an}</button>";
    echo "</form>";
    echo "</span>";

    // Dati
    echo "<span>{$nome}</span>";
    echo "<span>{$data}</span>";
    echo "<span>{$euro}</span>";
    echo "<span>{$descr}</span>";

    echo "</div>"; // .appointment-item
  }

  echo "</div>"; // .appointments
  echo "</div>"; // .card

  // libera risorse
  mysqli_stmt_close($stmt);
  mysqli_close($connect);
}
   ?> <a href="./ricerca.php" class="btn-add">← Torna alla ricerca</a><?php
include('./menusx.inc');
?><hr><img src='./Immagini/suggerimento.png'><small><i>Per visualizzare la scheda di un associato, inserisci l'id (numero identificativo) in 'scheda associato' e premi 'visualizza'
<hr></i></small><?php
include('./botton.inc');

?>
