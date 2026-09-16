<?php
/*
 ==========================================================================+
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
    =====================================================================+
*/
session_start();
$user = $_SESSION['utente'];
$langFiles = $_SESSION['lingua'];
$paginaFiles = "Files.inc";
$linguaFiles = ($langFiles . $paginaFiles);
include($linguaFiles);

if ($user == 'admin') {
  include('./top.inc');
  include('./menu.inc');

  // === FUNZIONE RICORSIVA PER ELENCO FILE ===
  function elenco_dir($base)
  {
    $dir_vuota = true;
    $lista = [];

    if ($handle_dir = opendir($base)) {
      echo '<ul class="file-list">';
      while (false !== ($dir = readdir($handle_dir))) {
        if ($dir != "." && $dir != "..") {
          $dir_vuota = false;
          $path = $base . "/" . $dir;

          if (is_dir($path)) {
            echo '<li class="folder-item"><img src="./ImmTemplate/designer.png" alt=""> <strong>' . htmlspecialchars($dir) . '</strong>';
            elenco_dir($path);
            echo '</li>';
          } else {
            $encodedPath = urlencode($path);
$nomeFile = htmlspecialchars(str_replace('_', ' ', pathinfo($dir, PATHINFO_FILENAME)));
$encodedPath = urlencode($path);
$nomeFile = htmlspecialchars(str_replace('_', ' ', pathinfo($dir, PATHINFO_FILENAME)));

$lista[] = '
<li class="file-item" style="display:flex; align-items:center; gap:8px;">

  <!-- Elimina -->
  <form action="delete_file.php" method="POST" style="margin:0;"
        onsubmit="return confirm(\'Eliminare definitivamente il file ' . $nomeFile . '?\');">
    <input type="hidden" name="file" value="' . $encodedPath . '">
    <button type="submit" class="btn-mini btn-delete" title="Elimina file">🗑️</button>
  </form>

  <!-- Download -->
  <a href="download_file.php?file=' . $encodedPath . '"
     class="btn-mini btn-edit"
     title="Scarica file">⬇️</a>

  <!-- Nome file -->
  <a href="' . htmlspecialchars($path) . '" target="_blank">' . $nomeFile . '</a>

</li>';
          }
        }
      }

      if ($dir_vuota) {
        echo '<i>Nessun file presente</i>';
      }

      echo '</ul>';
      closedir($handle_dir);

      sort($lista);
      foreach ($lista as $file) echo $file;
    } else {
      echo "<p class='error'>Percorso errato: <strong>$base</strong></p>";
    }
  }
?>

<!-- ======= SEZIONE PRINCIPALE ======= -->
<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ltitologestfiles; ?></h2>
    <hr class="divider">

    <?php if (isset($_GET['msg'])): ?>
  <p style="color:green; text-align:center; font-weight:bold;">
    <?php echo htmlspecialchars($_GET['msg']); ?>
  </p>
<?php endif; ?>

    <div class="form-buttons" style="margin-bottom:20px;">
      <form action="./gest_files.php" method="GET">
        <input type="submit" value="Caricamento file e immagini" class="btn-edit">
      </form>
    </div>

    <div class="file-section">
      <h3 class="card-title">🖼️ <?php echo $Lcaricaimmagine; ?></h3>
      <?php elenco_dir("./Immagini/Utenti"); ?>
    </div>

    <div class="file-section">
      <h3 class="card-title">📄 Moduli e files caricati</h3>
      <?php elenco_dir("./Download"); ?>
    </div>
  </div>
</div>

<?php
  include('./menusx.inc');
  echo "<div class='content-section'><small>$Lhelpgestimmagini</small></div>";
  include('./botton.inc');

} else {
  // === ACCESSO NEGATO ===
  function redirect($url, $tempo = FALSE)
  {
    if (!headers_sent() && $tempo == FALSE) {
      header('Location:' . $url);
    } elseif (!headers_sent() && $tempo != FALSE) {
      header('Refresh:' . $tempo . ';' . $url);
    } else {
      if ($tempo == FALSE) $tempo = 0;
      echo "<meta http-equiv='refresh' content='{$tempo};{$url}'>";
    }
  }

  echo "<div class='content-section'><p>$Llivello1gestimmagini</p></div>";
  redirect('./index2.php', 3);
}
?>
