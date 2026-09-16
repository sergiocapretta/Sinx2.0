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
  $langmoduli = $_SESSION['lingua'];
  $paginamoduli = "moduli.inc";
  $linguamoduli = ($langmoduli . $paginamoduli);
  include($linguamoduli);

  include('./top.inc');
  include('./menu.inc');
  include('./dati_db.inc');

  $connect = mysqli_connect($host, $username, $password, $db_name, $port)
    or die("cannot connect DB");
?>

  <div class="content-section">
    <div class="card">
      <h2 class="card-title"><?php echo $Ltitolomoduli; ?></h2>
      <p class="card-subtitle"><?php echo $Lsugg1moduli; ?></p>

      <form action="./gen_moduli.php" method="POST" class="sinx-form">
        <!-- Scelta modulo -->
        <label><?php echo $Lmodulo; ?> *</label>
        <select name="modulo" required>
          <option value="" selected><?php echo $Lmodulo; ?></option>

          <optgroup label="Associazione → Socio" <?php echo $limite; ?>>
            <option value="ammissione"><?php echo $Lammissionsocio; ?></option>
            <option value="ammissioneminore"><?php echo $Lammissionesociominore; ?></option>
            <option value="consenso"><?php echo $Lconsensoprivacy; ?></option>
          </optgroup>

          <optgroup label="Socio → Associazione">
            <option value="consenso"><?php echo $Lconsensoprivacy; ?></option>
            <option value="dimissioni"><?php echo $Ldimissionisocio; ?></option>
            <option value="rimborso"><?php echo $Lrimborsospese; ?></option>
            <option value="rapporto"><?php echo $Lrapportosoci; ?></option>
          </optgroup>

          <optgroup label="Interno Associazione" <?php echo $limite; ?>>
            <option value="consiglio"><?php echo $Lverbassemblea; ?>/<?php echo $Lriunione; ?></option>
            <option value="convocazione"><?php echo $Lconvocazioneconsiglio; ?></option>
            <option value="convocazioneassemblea"><?php echo $Lconvocazioneassemblea; ?></option>
            <option value="preventivo"><?php echo $Lpreventivilavori; ?></option>
          </optgroup>
        </select>

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
        <label><?php echo $Ldata; ?> documento</label>
        <input type="text" name="data" value="<?php echo date('d-m-Y'); ?>">
        <small><i><?php echo $Ltuttimoduli; ?></i></small>

        <label><?php echo $Ldata2; ?></label>
        <input type="text" name="data2" value="<?php echo date('d-m-Y-H:i'); ?>">
        <small><i><?php echo $Ldataritrovo; ?> - <?php echo $Lconvocazioneconsiglio; ?></i></small>

        <label><?php echo $Ldata3; ?></label>
        <input type="text" name="data3" value="<?php echo date('d-m-Y-H:i'); ?>">
        <small><i><?php echo $Ldataritrovo2; ?></i></small>

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
        <label><?php echo $Lsocipresenti; ?></label>
        <textarea name="presenti" rows="5">&lt;li&gt;<?php echo ($Lsocio . $Luno); ?>&lt;/li&gt;&lt;li&gt;<?php echo ($Lsocio . $Ldue); ?>&lt;/li&gt;</textarea>
        <small><i>"<?php echo $Lverbassemblea; ?>" - "<?php echo $Lrapportosoci; ?>" - "<?php echo ($Loggetto . " del " . $Lpreventivilavori); ?>"</i></small>

        <label><?php echo $Lordinegiorno; ?> / <?php echo $Lluogoattivitasoci; ?></label>
        <textarea name="OrdineGiorno" rows="5">&lt;li&gt;<?php echo $Lordinegiorno . $Luno; ?>&lt;/li&gt;&lt;li&gt;<?php echo $Lordinegiorno . $Ldue; ?>&lt;/li&gt;</textarea>
        <small><i>"<?php echo $Lverbassemblea; ?>" - "<?php echo $Lconvocazioneconsiglio; ?>" - "<?php echo $Lrapportosoci; ?>" - "<?php echo $Lconvocazioneassemblea; ?>"</i></small>

        <label><?php echo $Lverbale; ?></label>
        <textarea name="Verbale" rows="5"></textarea>
        <small><i>"<?php echo $Lverbassemblea; ?>" - "<?php echo $Lrimborsospese; ?>" - "<?php echo $Ldimissionisocio; ?>" - "<?php echo $Lrapportosoci; ?>" - "<?php echo $Lpreventivilavori; ?></i></small>

        <div class="form-buttons">
          <input type="submit" value="- Crea Modulo -" class="btn-add">
        </div>
      </form>

      <hr class="divider">

      <!-- Link a file -->
      <form action="./Files.php" method="post">
      <center>
        <button name="link" type="submit" class="btn-add" >
          <i class="material-icons">assignment_turned_in</i> <?php echo " - Files - " ?>
        </button>
      </center>
    </form>

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
  $limit = 'disabled';
  modulo($limit);
}
?>
