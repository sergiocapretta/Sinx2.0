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

// Gestione permessi
if ($user == 'admin') {
  $limit = '';
  $limite = '';
} elseif ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled';
  $limite = '';
} elseif ($user == 'associato') {
  $limit = 'disabled';
  $limite = 'disabled';
}

// Include comuni
include('./top.inc');
include('./menu.inc');

// Lingua
$langricerca = $_SESSION['lingua'] ?? './lang/ita/';
$paginaricerca = "schedacomuni.inc";
$linguaricerca = ($langricerca . $paginaricerca);
include($linguaricerca);

// Connessione DB
include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

// === PARAMETRI RICERCA E PAGINAZIONE ===
$per_page = 30;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

$search = trim($_GET['search'] ?? '');
$filter_sql = "";

if ($search !== '') {
  $search_safe = mysqli_real_escape_string($connect, $search);
  $filter_sql = "WHERE c.comune LIKE '%$search_safe%' OR p.nome_provincia LIKE '%$search_safe%'";
}

// Conta totale righe filtrate
$count_query = "
  SELECT COUNT(*) as total
  FROM comuni AS c
  INNER JOIN province AS p ON c.id_pro = p.id_pro
  $filter_sql
";
$count_result = mysqli_query($connect, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

// Query principale
$Query_nome = "
  SELECT c.id_com, c.id_pro, p.nome_provincia, c.cap, c.comune
  FROM comuni AS c
  INNER JOIN province AS p ON c.id_pro = p.id_pro
  $filter_sql
  ORDER BY p.nome_provincia, c.comune
  LIMIT $per_page OFFSET $offset
";
$rs = mysqli_query($connect, $Query_nome)
  or die("Errore nella query $Query_nome: " . mysqli_error($connect));
?>

<!-- STILE -->
<link rel="stylesheet" href="/style.css">

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><i class="material-icons">location_city</i> <?php echo $Lcomuni; ?></h2>
    <p class="card-subtitle"><?php echo $Lelencocomuni; ?></p>

    <!-- 🔍 Ricerca -->
    <form method="GET" action="" class="sinx-form" style="margin-bottom: 15px;">
      <label for="search"><b>Cerca Comune o Provincia:</b></label>
      <div style="display: flex; gap: 10px;">
        <input type="text" name="search" id="search"
               placeholder="es. Firenze, Roma..."
               value="<?php echo htmlspecialchars($search); ?>"
               style="flex: 1; border-radius: 8px; padding: 8px;">
        <button type="submit" class="btn-edit">Cerca</button>
        <?php if ($search !== ''): ?>
          <a href="./Scheda_comuni.php" class="btn-delete">X</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- LISTA COMUNI -->
    <div class="appointments appointments-4col">
      <div class="appointments-header">
        <span><b><?php echo $LidComune; ?></b></span>
        <span><b><?php echo $LProvincia; ?></b></span>
        <span><b><?php echo $Lcap; ?></b></span>
        <span><b><?php echo $Lcomune; ?></b></span>
      </div>

      <?php if (mysqli_num_rows($rs) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($rs)): ?>
          <div class="appointment-item">
            <span><?php echo $row['id_com']; ?></span>
            <span><?php echo $row['id_pro'] . ' - ' . htmlspecialchars($row['nome_provincia']); ?></span>
            <span><?php echo htmlspecialchars($row['cap']); ?></span>
            <span><?php echo htmlspecialchars($row['comune']); ?></span>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; color:#666;">Nessun comune trovato.</p>
      <?php endif; ?>
    </div>

    <!-- PAGINAZIONE -->
    <?php if ($total_pages > 1): ?>
    <div class="form-buttons" style="margin-top: 20px;">
      <?php if ($page > 1): ?>
        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="btn-edit">← Precedente</a>
      <?php endif; ?>

      <span style="font-weight:bold;">Pagina <?php echo $page; ?> di <?php echo $total_pages; ?></span>

      <?php if ($page < $total_pages): ?>
        <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="btn-edit">Successiva →</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <hr class="divider">

    <!-- FORM OPERAZIONI -->
    <form action="./conf_comuni.php" method="POST" enctype="multipart/form-data" class="sinx-form">
      <h3 class="card-subtitle"><?php echo $Lgestionecomuni; ?></h3>

      <div class="form-grid">
        <label><?php echo $LidComune; ?>
          <input name="id_c" type="text" required <?php echo $limit; ?>>
        </label>

        <label><?php echo $Lidprovincia; ?>
          <input name="id_p" type="text" required <?php echo $limit; ?>>
        </label>

        <label><?php echo $Lcap; ?>
          <input name="cap" type="text" required <?php echo $limit; ?>>
        </label>

        <label><?php echo $Lnome; ?>
          <input name="nome" type="text" required <?php echo $limit; ?>>
        </label>
      </div>

      <fieldset>
        <legend><?php echo $Loperazione; ?></legend>
        <label><input type="radio" name="operazione" value="mod"> Modifica</label>
        <label><input type="radio" name="operazione" value="canc"> Cancella</label>
        <label><input type="radio" name="operazione" value="agg" checked> Aggiungi</label>
      </fieldset>

      <div class="form-buttons">
        <button type="submit" class="btn-add" <?php echo $limit . ' ' . $limite; ?>><?php echo $Linvia; ?></button>
      </div>
    </form>

    <hr class="divider">

    <!-- LINK RAPIDI -->
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
