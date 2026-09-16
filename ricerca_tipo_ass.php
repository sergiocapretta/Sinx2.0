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

  $connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port)
    or die("cannot connect DB");

  // 🔹 Funzione redirect
  function redirect($url, $tempo = false)
  {
    if (!headers_sent() && $tempo == false) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo != false) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      if ($tempo == false) $tempo = 0;
      echo "<meta http-equiv=\"refresh\" content=\"" . $tempo . ";" . $url . "\">";
    }
  }

  // 🔹 Dati POST
  $tipo = $_POST['tipo'] ?? '';

  // 🔹 Validazione
  if (empty($tipo)) {
    echo "<center><b>Il campo Tipo è obbligatorio</b></center>";
    redirect('./ricerca.php', 2);
    die();
  }

  // 🔹 Query principale
  $query = "SELECT * FROM tb_anagrafe WHERE materia = '$tipo' AND tipologia = 'Ins' ORDER BY nome";
  $rs = mysqli_query($connect, $query) or die("Errore nella query: " . mysqli_error($connect));
?>

<!-- ===== CONTENUTO PRINCIPALE ===== -->
<link rel="stylesheet" href="/style.css">

<div class="content-section">
  <div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h2 class="card-title">Elenco Associati - Tipo <i><?php echo htmlspecialchars($tipo); ?></i></h2>
      <a href="./ricerca.php" class="btn-add">↩ Torna alla Ricerca</a>
    </div>

    <hr class="divider">

    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span><b>ID</b></span>
        <span><b>Nome</b></span>
        <span><b>Cognome</b></span>
        <span><b>Email</b></span>
      </div>

      <?php while ($row = mysqli_fetch_assoc($rs)) :
        $id = htmlspecialchars($row['id_anagrafe']);
        $nome = htmlspecialchars($row['nome']);
        $cognome = htmlspecialchars($row['cognome']);
        $email = htmlspecialchars($row['email']);
      ?>
        <div class="appointment-item">
          <span>
            <form action="./Scheda_associato.php" method="POST" style="display:inline;">
              <input type="hidden" name="associato" value="<?php echo $id; ?>">
              <button type="submit" class="btn-mini" title="Apri scheda associato"><?php echo $id; ?></button>
            </form>
          </span>
          <span><?php echo $nome; ?></span>
          <span><?php echo $cognome; ?></span>
          <span><?php echo $email; ?></span>
        </div>
      <?php endwhile; ?>
    </div>

    <hr class="divider">

    <div class="text-center">
      <img src="/Immagini/suggerimento.png" alt="Suggerimento" style="width:40px;">
      <small><i>Clicca sull’ID per aprire la scheda completa dell’associato.</i></small>
    </div>
  </div>
</div>

<?php
  include('./botton.inc');
  mysqli_close($connect);
} else {
  header('Location: ./index.php');
}
?>
