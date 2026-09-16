<?php
session_start();

$user = $_SESSION['utente'];
$langdatiass = $_SESSION['lingua'];
$paginadatiass = "datiassociaz.inc";
$linguadatiass = ($langdatiass . $paginadatiass);
include($linguadatiass);

// Permessi
if ($user == 'admin') {
  $limit = ''; $limite = '';
} elseif ($user == 'limitato' || $user == 'operatore') {
  $limit = 'disabled'; $limite = '';
} elseif ($user == 'associato') {
  $limit = 'disabled'; $limite = 'disabled';
}

include('./top.inc');
include('./menu.inc');
include('./dati_db.inc');

$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("cannot connect DB");

$Query_nome = "SELECT * FROM tb_anagrafe_associaz LIMIT 1";
$rs = mysqli_query($connect, $Query_nome)
  or die("<b>Errore:</b> Impossibile eseguire la query");
$row = mysqli_fetch_array($rs);
?>

<div class="content-section">
  <div class="card">
    <h2 class="card-title"><?php echo $Ldatiassociazione; ?></h2>
    <p class="card-subtitle"><?php echo $Linsomod; ?></p>

    <form action="./conf_mod_Associaz.php" method="POST" enctype="multipart/form-data" class="sinx-form">

      <div style="text-align:center; margin-bottom:20px;">
        <img src="./Immagini/logo.png" alt="Logo" width="120" class="bordo">
        <br><br>
        <input type="button" value="<?php echo $Lcambialogo; ?>"
               onclick="top.location.href='./gest_files.php'"
               <?php echo $limit . ' ' . $limite; ?>>
      </div>

      <label><?php echo $Lnomeassociaz; ?> *</label>
      <input type="text" name="nome" required value="<?php echo htmlspecialchars($row['nome']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lvia; ?></label>
      <input type="text" name="indirizzo" value="<?php echo htmlspecialchars($row['indirizzo']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lnumero; ?></label>
      <input type="text" name="numero" value="<?php echo htmlspecialchars($row['numero']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lcap; ?></label>
      <input type="text" name="cap" value="<?php echo htmlspecialchars($row['cap']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lcitta; ?></label>
      <input type="text" name="citta" value="<?php echo htmlspecialchars($row['citta']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lprovincia; ?></label>
      <input type="text" name="provincia" value="<?php echo htmlspecialchars($row['provincia']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Ltelefono; ?></label>
      <input type="text" name="tel" value="<?php echo htmlspecialchars($row['tel']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lfax; ?></label>
      <input type="text" name="fax" value="<?php echo htmlspecialchars($row['fax']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lcfpi; ?></label>
      <input type="text" name="cf" value="<?php echo htmlspecialchars($row['cf']); ?>" <?php echo $limit; ?>>

      <label>Email</label>
      <input type="email" name="email" placeholder="email@socio.xxx" value="<?php echo htmlspecialchars($row['email']); ?>" <?php echo $limit; ?>>

      <label>Webmail</label>
      <input type="text" name="webmail" placeholder="http://webmail.dominio.xxx" value="<?php echo htmlspecialchars($row['webmail']); ?>" <?php echo $limit; ?>>

      <label>PEC</label>
      <input type="text" name="PEC" value="<?php echo htmlspecialchars($row['PEC']); ?>" <?php echo $limit; ?>>

      <label>Web PEC</label>
      <input type="text" name="webPEC" value="<?php echo htmlspecialchars($row['webPEC']); ?>" <?php echo $limit; ?>>

      <label>Sito</label>
      <input type="text" name="sito" placeholder="http://www.sito.xxx" value="<?php echo htmlspecialchars($row['sito']); ?>" <?php echo $limit; ?>>

      <label>Facebook</label>
      <input type="text" name="facebook" value="<?php echo htmlspecialchars($row['facebook']); ?>" <?php echo $limit; ?>>

      <label>Instagram</label>
      <input type="text" name="instagram" value="<?php echo htmlspecialchars($row['instagram']); ?>" <?php echo $limit; ?>>

      <label>Twitter</label>
      <input type="text" name="twitter" value="<?php echo htmlspecialchars($row['twitter']); ?>" <?php echo $limit; ?>>

      <label>YouTube</label>
      <input type="text" name="youtube" value="<?php echo htmlspecialchars($row['youtube']); ?>" <?php echo $limit; ?>>

      <label><?php echo $Lbanca; ?></label>
      <input type="text" name="banca" value="<?php echo htmlspecialchars($row['banca']); ?>" <?php echo $limit; ?>>

      <label>IBAN</label>
      <input type="text" name="IBAN" value="<?php echo htmlspecialchars($row['IBAN']); ?>" <?php echo $limit; ?>>

      <label>BIC</label>
      <input type="text" name="BIC" value="<?php echo htmlspecialchars($row['BIC']); ?>" <?php echo $limit; ?>>

      <label>Home Banking</label>
      <input type="text" name="HomeBanking" value="<?php echo htmlspecialchars($row['HomeBanking']); ?>" <?php echo $limit; ?>>

      <label>Iscrizione ODV / APS</label>
      <input type="text" name="IscrizioneODVoAPS" value="<?php echo htmlspecialchars($row['IscrizioneODVoAPS']); ?>" <?php echo $limit; ?>>

      <div class="form-buttons">
        <button type="submit" class="btn-add" <?php echo $limit . ' ' . $limite; ?>><?php echo $Linvia; ?></button>
      </div>

    </form>
  </div>
</div>

    <?php
mysqli_close($connect);
include('./menusx.inc');
 echo $Lhelpdatiassociaz;
include('./botton.inc');

?>
