<?php
/*
 =========================================================================+
Sinx for Association - Gestionale per Associazioni no-profit
    Copyright (C) 2011 - 2025 by Sergio Capretta

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
    ======================================================================+
*/
session_start();
$user = $_SESSION['utente'];

function modulo($limite = NULL)
{

  $langpreventivo = $_SESSION['lingua'];
  $paginapreventivo = "preventivo.inc";
  $linguapreventivo = ($langpreventivo . $paginapreventivo);
  include($linguapreventivo);

  include('./top.inc');
  include('./dati_db.inc');

  $connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die("cannot connect DB");
?>

  <div class="content-section">
    <div class="card">
      <h2 class="card-title"><?php echo $Ltitolomoduli; ?></h2>
      <p class="card-subtitle"><?php echo $Lsugg1moduli; ?></p>

      <form action="./ConfPreventivo.php" method="POST" class="sinx-form">
        <!-- Scelta modulo -->
        <label><?php echo $Lmodulo; ?></label>

<input type="text" name="modulo" readonly="readonly" value="<?php echo htmlspecialchars($Lpreventivilavori, ENT_QUOTES); ?>">
<small><i><?php echo $LSuggPreventivo; ?></i></small>


       <!-- Scelta socio -->
<label><?php echo $Lassociato; ?></label>
<select name="nomeass" required>
  <option value="" selected><?php echo $Lnome; ?></option>
  <?php
  $query = "SELECT id_anagrafe, nome, cognome FROM tb_anagrafe ORDER BY nome";
  $rs = mysqli_query($connect, $query) or die("<b>Errore:</b> Impossibile eseguire la query");
  while ($row = mysqli_fetch_assoc($rs)) {
    $id = htmlspecialchars($row['id_anagrafe']);
    $nome = htmlspecialchars($row['nome']);
    $cognome = htmlspecialchars($row['cognome']);
    echo "<option value='$id'>$nome $cognome</option>";
  }
  ?>
</select>

        <!-- Date -->
        <label><?php echo $Ldataoff; ?></label>
        <input type="text" name="data" value="<?php echo date('d-m-Y'); ?>">

        <!-- Suggerimenti formattazione -->
        <div class="card-subtitle" style="background:#fffece; padding:8px; border-radius:6px;">
          <small><i>
            <?php echo $Lsugg2moduli; ?><br>
            &lt;br&gt; = <?php echo $Lvacapo; ?>,
            &lt;b&gt;&lt;/b&gt; = <?php echo $Lgrassetto; ?>,
            &lt;i&gt;&lt;/i&gt; = <?php echo $Lcorsivo; ?>,
            &lt;hr&gt; = <?php echo $Llineaorr; ?>,
            &lt;li&gt;&lt;/li&gt; = <?php echo $Lelenco; ?>
          </i></small>
        </div>

        <!-- Campi testo -->
        <label><?php echo $Loggetto; ?></label>
        <textarea name="presenti" rows="4"></textarea>
        <small><i><?php echo ($Loggetto . " del " . $Lpreventivilavori); ?></i></small>

        <label><?php echo $Lverbale; ?></label>
        <textarea name="Verbale" rows="8"></textarea>
        <small><i><?php echo $LSuggDescrizione; ?> &lt;br&gt;</i></small>

        <div class="form-buttons">
        <label>
            <input type="checkbox" name="salvaDownload" value="1">
        <?php echo $LSalvaCopiaFiles; ?>
        </label>
          <input type="submit" value="- Crea Preventivo -" class="btn-add">
        </div>
      </form>

      <hr class="divider">


      <div style="margin-top:20px; text-align:center;">
        <img src="./Immagini/suggerimento.png" width="40" alt="Suggerimento">
        <small><i><?php echo $Lsugg3moduli; ?></i></small>
      </div>
    </div>
  </div>

<?php
  include('./menusx.inc');
  include('./botton.inc');
  mysqli_close($connect);
}

// Gestione accessi
if ($user == 'admin') {
  modulo();
} elseif ($user == 'limitato') {
  header('Location: ./Redirect_no_enter.php');
} elseif ($user == 'operatore') {
  modulo();
} elseif ($user == 'associato') {
  header('Location: ./Redirect_no_enter.php');
}
?>
