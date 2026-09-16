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
$langdaticom = $_SESSION['lingua'];
$paginadaticom = "daticomuni.inc";
$linguadaticom = ($langdaticom.$paginadaticom);

include($linguadaticom);

	include ('./dati_db.inc');
	$connect = mysqli_connect("$host", "$username", "$password", "$db_name", $port ) or die("cannot connect DB");

?>

<!-- 🔹 DATI COMUNI DEL SOCIO / COLLABORATORE -->
<div class="sinx-form">

  <!-- Immagine -->
  <label for="immagine"><?php echo $Lpimmagine; ?></label>
  <input type="hidden" name="MAX_FILE_SIZE" value="30000">
  <input name="immagine" id="immagine" type="file" accept="image/*">
  <small><i><?php echo $Listrimmagine; ?></i></small>

<!-- Nome e Cognome -->
  <label for="nome"><span style="color:red">*</span> <?php echo $Lnomedaticomuni; ?></label>
  <input name="nome" id="nome" type="text" required placeholder="<?php echo $Listrnome; ?>">

  <label for="cognome"><?php echo $Lcognomedaticomuni; ?></label>
  <input name="cognome" id="cognome" type="text" placeholder="">

  <!-- Indirizzo -->
  <label for="indirizzo"><?php echo $Lindirizzo; ?></label>
  <input name="indirizzo" id="indirizzo" type="text" placeholder="<?php echo $Listrindirizzo; ?>">

  <!-- Regione -->
  <label for="regioni"><?php echo $Lregione; ?></label>
  <select name="regioni" id="regioni">
    <option value="" selected="selected"><?php echo $Lscegli; ?></option>
    <?php
    $query = "SELECT nome_regione FROM regioni ORDER BY nome_regione";
    $rs = mysqli_query($connect, $query) or die("<b>Errore:</b> Impossibile eseguire la query regioni");
    while ($row = mysqli_fetch_row($rs)) {
      echo "<option>" . htmlspecialchars($row[0]) . "</option>";
    }
    ?>
  </select>
  <small><i><?php echo $Lsuggregione; ?></i></small>

  <!-- Provincia -->
  <label for="provincie"><?php echo $Lprovincia; ?></label>
  <select name="provincie" id="provincie" disabled>
    <option value=""><?php echo $Lscegli; ?></option>
  </select>
  <small><i><?php echo $Lsuggprovincia; ?></i></small>

  <!-- Comune -->
  <label for="comuni"><?php echo $Lcomune; ?></label>
  <select name="comuni" id="comuni" disabled>
    <option value=""><?php echo $Lscegli; ?></option>
  </select>
  <small><i><?php echo $Lsuggcitta; ?></i></small>

  <!-- Telefoni -->
  <label for="tel"><?php echo $Ltel; ?></label>
  <input name="tel" id="tel" type="tel" pattern="[0-9]*" placeholder="<?php echo $Listrtel; ?>">

  <label for="tel2"><?php echo $Lcell; ?></label>
  <input name="tel2" id="tel2" type="tel" pattern="[0-9]*" placeholder="<?php echo $Listrtel; ?>">

  <!-- Email -->
  <label for="email">E-mail:</label>
  <input name="email" id="email" type="email" placeholder="email@socio.xxx">

  <!-- Data di nascita -->
  <label for="datangg"><?php echo $Ldatanasc; ?></label>
  <div style="display:flex; gap:5px;">
    <input name="datangg" id="datangg" type="number" min="1" max="31" placeholder="gg">
    <input name="datanmm" id="datanmm" type="number" min="1" max="12" placeholder="mm">
    <input name="datanaaaa" id="datanaaaa" type="number" min="1900" max="2100" placeholder="aaaa">
  </div>
  <small><i><?php echo $Listrdatan; ?></i></small>

  <!-- Codice Fiscale -->
  <label for="nomerif"><?php echo $Lcodfiscale; ?></label>
  <input name="nomerif" id="nomerif" type="text" placeholder="Codice fiscale">

  <!-- Associato -->
  <label>
    <input type="checkbox" name="check" checked value="1">
    <?php echo $Lassociatoattivo; ?>
  </label>

  <!-- Note -->
  <label for="note"><?php echo $Lnote; ?></label>
  <textarea name="note" id="note" rows="5" placeholder="Inserisci eventuali note"></textarea>

</div>

<!-- 🔹 JAVASCRIPT -->
<script type="text/javascript">
  // Verifica doppio numero tessera
  function checkDoubleCardNumber(variable, post) {
    $.ajax({
      type: "POST",
      url: "ajax_check_card_number.php",
      data: variable + "=" + post,
      dataType: "html",
      success: function (msg) {
        if (parseInt(msg) > 0) {
          alert("⚠️ Attenzione: numero tessera già presente! (solo avviso)");
        }
      },
      error: function () {
        alert("Errore AJAX: impossibile completare la richiesta.");
      }
    });
  }

  $(document).ready(function () {
    $('#ntessera').on('blur', function () {
      checkDoubleCardNumber("numeroCarta", this.value);
    });

    // Caricamento province e comuni
    function getEnteLocale(variable, post) {
      $.ajax({
        type: "POST",
        url: "ajax_get_ente_locale.php",
        data: variable + "=" + post,
        dataType: "html",
        success: function (msg) {
          if (variable === "regioni") {
            $('#provincie').prop("disabled", false).empty()
              .append('<option value=""><?php echo $Lscegli; ?></option>' + msg);
          } else {
            $('#comuni').prop("disabled", false).empty()
              .append('<option value=""><?php echo $Lscegli; ?></option>' + msg);
          }
        },
        error: function () {
          alert("Errore AJAX durante il caricamento dell'elenco.");
        }
      });
    }

    // Cambio regione → aggiorna province
    $('#regioni').on('change', function () {
      getEnteLocale(this.name, this.value);
      $('#provincie, #comuni').empty().prop("disabled", true);
    });

    // Cambio provincia → aggiorna comuni
    $('#provincie').on('change', function () {
      getEnteLocale(this.name, this.value);
      $('#comuni').empty().prop("disabled", true);
    });
  });
</script>
