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
=========================================================================+*/
session_start();
$user = $_SESSION['utente'] ?? null;

if (!$user) {
  header("Location: ./login.php");
  exit;
}

$langricerca = $_SESSION['lingua'] ?? './lang/ita/';
$paginaricerca = "schedaprovince.inc";
$linguaricerca = ($langricerca . $paginaricerca);
include($linguaricerca);

// Gestione permessi
$limit = $limite = '';
if ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled';
}
if ($user == 'associato') {
  $limit = $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');

// Connessione DB
include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// --- PARAMETRI DI RICERCA E PAGINAZIONE ---
$per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

$search = trim($_GET['search'] ?? '');
$filter_sql = "";
if ($search !== '') {
  $search_safe = mysqli_real_escape_string($connect, $search);
  $filter_sql = "WHERE p.nome_provincia LIKE '%$search_safe%' OR r.nome_regione LIKE '%$search_safe%'";
}

// Conta totale province filtrate
$count_query = "
  SELECT COUNT(*) as total
  FROM province AS p
  INNER JOIN regioni AS r ON p.id_reg = r.id_reg
  $filter_sql
";
$count_result = mysqli_query($connect, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

// Query province filtrate e limitate
$Query_nome = "
  SELECT p.id_pro, p.id_reg, r.nome_regione, p.nome_provincia
  FROM province AS p
  INNER JOIN regioni AS r ON p.id_reg = r.id_reg
  $filter_sql
  ORDER BY r.nome_regione, p.nome_provincia
  LIMIT $per_page OFFSET $offset
";
$rs = mysqli_query($connect, $Query_nome)
  or die("Errore nella query $Query_nome: " . mysqli_error($connect));
?>

<!-- === CONTENUTO PRINCIPALE === -->
<div class="content-section">
  <div class="card">
    <h2 class="card-title"><i class="material-icons">map</i> <?php echo $LProvincia; ?></h2>
    <p class="card-subtitle"><?php echo $Lidprovincia; ?> / <?php echo $LidRegione; ?> / <?php echo $LProvincia; ?></p>

    <!-- 🔍 Ricerca -->
    <form method="GET" action="" class="sinx-form" style="margin-bottom: 15px;">
      <label for="search"><b>Cerca Provincia o Regione:</b></label>
      <div style="display: flex; gap: 10px;">
        <input type="text" name="search" id="search" placeholder="es. Lombardia, Milano..."
               value="<?php echo htmlspecialchars($search); ?>"
               style="flex: 1; border-radius: 8px; padding: 8px;">
        <button type="submit" class="btn-edit">Cerca</button>
        <?php if ($search !== ''): ?>
          <a href="./Scheda_province.php" class="btn-delete">X</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Lista Province -->
    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span>ID Prov.</span>
        <span>ID Reg.</span>
        <span><?php echo $Lregione; ?></span>
        <span><?php echo $LProvincia; ?></span>
      </div>

      <?php if (mysqli_num_rows($rs) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($rs)): ?>
          <div class="appointment-item">
            <span><?php echo $row['id_pro']; ?></span>
            <span><?php echo $row['id_reg']; ?></span>
            <span><?php echo htmlspecialchars($row['nome_regione']); ?></span>
            <span><?php echo htmlspecialchars($row['nome_provincia']); ?></span>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; color:#666;">Nessuna provincia trovata.</p>
      <?php endif; ?>
    </div>

    <!-- Controlli paginazione -->
    <?php if ($total_pages > 1): ?>
    <div class="form-buttons" style="margin-top: 20px;">
      <?php if ($page > 1): ?>
        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="btn-edit">← Precedente</a>
      <?php endif; ?>

      <span style="font-weight: bold;">Pagina <?php echo $page; ?> di <?php echo $total_pages; ?></span>

      <?php if ($page < $total_pages): ?>
        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="btn-edit">Successiva →</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <hr class="divider">

    <!-- FORM Gestione Province -->
    <form action="./conf_province.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <h3 class="card-subtitle"><?php echo $Lgestione; ?> Province</h3>

      <div class="form-grid">
        <label><?php echo $Lidprovincia; ?></label>
        <input name="id_p" type="text" required <?php echo $limit; ?>>

        <label><?php echo $LidRegione; ?></label>
        <input name="id_r" type="text" required <?php echo $limit; ?>>

        <label><?php echo $Lnome; ?></label>
        <input name="nome" type="text" required <?php echo $limit; ?>>
      </div>

      <fieldset>
        <legend>Operazione</legend>
        <label><input type="radio" name="operazione" value="mod"> Modifica</label>
        <label><input type="radio" name="operazione" value="canc"> Cancella</label>
        <label><input type="radio" name="operazione" value="agg" checked> Aggiungi</label>
      </fieldset>

      <div class="form-buttons">
        <button type="submit" class="btn-add" <?php echo $limit . ' ' . $limite; ?>>Vai</button>
      </div>
    </form>

    <hr class="divider">

    <!-- Navigazione tra schede -->
    <div class="form-buttons">
      <form action="./Scheda_regioni.php" method="POST">
        <button class="btn-edit">Regioni</button>
      </form>
      <form action="./Scheda_province.php" method="POST">
        <button class="btn-edit">Province</button>
      </form>
      <form action="./Scheda_comuni.php" method="POST">
        <button class="btn-edit">Comuni</button>
      </form>
    </div>
  </div>
</div>
<center>
<?php
mysqli_close($connect);
include('./menusx.inc');
echo $Lsuggerimento;
include('./botton.inc');
?>
</center>
