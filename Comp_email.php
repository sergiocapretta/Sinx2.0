<?php
/*======================================================================+
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
=========================================================================+
*/

session_start();

$user = $_SESSION['utente'];
$langemail = $_SESSION['lingua'];
$paginaemail = "email.inc";
$linguaemail = ($langemail . $paginaemail);
include($linguaemail);

// Permessi
if ($user == 'admin') {
  $limit = ''; $limite = '';
} elseif ($user == 'limitato') {
  $limit = 'disabled'; $limite = 'disabled';
} elseif ($user == 'associato' || $user == 'operatore') {
  $limit = 'disabled'; $limite = '';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitoloemail; ?></h2>

    <!-- Form principale -->
    <form action="./email.php" method="POST" class="sinx-form">

    <?php
$prefilled_subject = $_POST['subject'] ?? '';
$prefilled_content = $_POST['formcontent'] ?? '';
?>

      <!-- Campo destinatario -->
      <label><b><?php echo $Laemail; ?> *</b></label>
      <fieldset class="bordo" style="padding: 10px;">
        <legend><small><?php echo $Ldestinatarioemail; ?>:</small></legend>

        <label>
          <input type="radio" name="destinatario" value="esterno" checked="checked">
          <small>Email esterna:</small>
        </label>
        <input type="email" name="recipient" placeholder="nome@email.xxx" style="margin-left:10px;">

        <br><br>

        <label>
          <input type="radio" name="destinatario" value="associato">
          <small>Associato registrato:</small>
        </label>
        <select name="iscritto">
          <option value="" selected="selected"></option>
          <?php
          $a = 1;
          do {
            $query = "SELECT email FROM tb_anagrafe WHERE id_anagrafe = $a AND email != '' ORDER BY email ASC LIMIT 1";
            $rs = mysqli_query($connect, $query) or die("<b>Errore:</b> Impossibile eseguire la query.");
            while ($row = mysqli_fetch_row($rs)) {
              echo "<option>" . htmlspecialchars($row[0]) . "</option>";
            }
            $a++;
          } while ($a <= 500);
          ?>
        </select>

        <br><br>

        <label>
          <input type="radio" name="destinatario" value="tutti">
          <b><i><small><?php echo $Ltuttiemail; ?></small></i></b>
        </label>
        <div><small><sub><i><?php echo $Lesclusicollabemail; ?></i></sub></small></div>

        <br>

        <label>
          <input type="radio" name="destinatario" value="fondatori">
          <b><i><small><?php echo $Lfondatoriemail; ?></small></i></b>
        </label>
        <div><small><sub><i><?php echo $Llistafondatoriemail; ?></i></sub></small></div>
      </fieldset>

      <!-- Oggetto -->
      <label><?php echo $Loggemail; ?> *</label>
      <input type="text" name="subject" placeholder="Oggetto del messaggio"
       value="<?php echo htmlspecialchars($prefilled_subject); ?>" required>

      <!-- Corpo del messaggio -->
      <label><?php echo $Lmessgemail; ?> *</label>
<textarea name="formcontent" rows="8" class="note-area" placeholder="Scrivi il messaggio..."><?php
  echo htmlspecialchars($prefilled_content);
?></textarea>

      <small><i>
        <?php echo $Lsugg1email; ?> &lt;br&gt; <?php echo $Lacapoemail; ?>,
        &lt;b&gt;&lt;/b&gt; <?php echo $Lgrassettoemail; ?>,
        &lt;i&gt;&lt;/i&gt; <?php echo $Lcorsivoemail; ?>,
        &lt;hr&gt; <?php echo $Lorizzemail; ?>,
        &lt;li&gt;&lt;/li&gt; <?php echo $Lpuntatoemail; ?>,
        &lt;h1&gt;...&lt;h6&gt; <?php echo $Ltitoliemail; ?>
      </i></small>

      <!-- Opzione salvataggio -->
      <label style="margin-top:10px;">
        <input type="checkbox" name="check" value="salva" checked="yes"> <?php echo $Lsalvaemail; ?>
      </label>

      <!-- Pulsanti -->
      <div class="form-buttons">
        <input type="submit" value="Spedisci" class="btn-add" <?php echo $limite; ?>>
        <a href="./Posta_inviata.php" class="btn-edit"><?php echo $Lposta_inviata ?? 'Posta inviata'; ?></a>
      </div>
    </form>

    <hr class="divider">

    <!-- Ultime email inviate -->
    <h3 class="card-title">📬 Ultime email inviate</h3>
    <div class="appointments appointments-4col" style="margin-top:10px;">
      <div class="appointments-header">
        <span>ID</span>
        <span>Data</span>
        <span>Destinatario</span>
        <span>Oggetto</span>
      </div>

      <?php
      // Recupero ultime 5 email inviate (adatta il nome tabella se diverso)
      $queryLast = "SELECT * FROM tb_email ORDER BY data DESC LIMIT 5";
      $rsLast = @mysqli_query($connect, $queryLast);

      if ($rsLast && mysqli_num_rows($rsLast) > 0) {
        while ($row = mysqli_fetch_assoc($rsLast)) {
          echo "<div class='appointment-item'>";
          echo "<span>{$row['id_mail']}</span>";
          echo "<span>" . htmlspecialchars($row['data']) . "</span>";
          echo "<span>" . htmlspecialchars($row['dest']) . "</span>";
          echo "<span>" . $row['testo'] . "</span>";
          echo "</div>";
        }
      } else {
        echo "<div class='appointment-item'><span colspan='4'><i>Nessuna email recente trovata.</i></span></div>";
      }
      ?>
    </div>

    <hr class="divider">

    <div style="text-align:center;">
      <img src="./Immagini/suggerimento.png" alt="Suggerimento" style="width:40px;">
      <small><i><?php echo $Lnota2email; ?></i></small>
    </div>
  </div>
</div>

<?php
mysqli_close($connect);
include('./menusx.inc');
include('./botton.inc');
?>
