<!--======================================================================+
 File name   : conf_mod_stud
 Begin       : 2010-08-04
 Last Update : 2012-10-27

 Description : form modification and deletion associated

 Author: Sergio Capretta

 (c) Copyright:
               Sergio Capretta
             
               ITALY
               www.sinx.it
               info@sinx.it

Sinx for Association - Gestionale per Associazioni no-profit
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
=========================================================================+
-->
<?php
$associato = $_POST['associato'];

$Query_nome = "SELECT * FROM tb_anagrafe WHERE id_anagrafe = $associato";
$rs = mysqli_query($connect, $Query_nome)
  or die("Errore nella query $Query_nome: " . mysqli_error($connect));

$row = mysqli_fetch_array($rs);
$idprec = $row['id_anagrafe'];
?>

<!-- === CARD CANCELLAZIONE === -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title">Cancella Associato</h3>
    <p class="card-subtitle">
      Inserisci il numero ID da cancellare e premi “Cancella”.
    </p>

    <form action="./conf_canc.php?Tabella=tb_anagrafe&Riferimento=id_anagrafe"
          method="POST"
          class="sinx-form"
          onsubmit="return confirm('⚠️ Sei sicuro di voler cancellare questo associato? L’operazione è irreversibile.');">

      <label for="id_mod">ID Associato:</label>
      <input name="id_mod" id="id_mod" type="text" value="<?php echo $idprec; ?>" readonly>

      <div class="form-buttons" style="justify-content: center;">
        <input value="- Cancella -" type="submit" class="btn-delete" <?php echo $limit; ?>>
      </div>
    </form>
  </div>
</div>

<hr class="divider">

<!-- === CARD MODIFICA === -->
<div class="content-section">
  <div class="card">
    <h3 class="card-title">Modifica Dati Associato</h3>
    <p class="card-subtitle">
      Inserisci l’ID, scegli il campo da modificare e scrivi il nuovo valore.<br>
      <small><i>Per modificare l’immagine, inserire solo il nome e l’estensione (es. <b>foto.jpg</b>).</i></small>
    </p>

    <form action="./conf_mod_stud.php" method="POST" class="sinx-form">

      <label for="id_mod">ID Associato:</label>
      <input name="id_mod" id="id_mod" type="text" value="<?php echo $idprec; ?>" readonly>

      <label for="campo">Campo da modificare:</label>
      <select name="campo" id="campo" required>
        <option value="" selected disabled>-- Seleziona un campo --</option>
        <option value="ntessera">Tessera</option>
        <option value="nome">Nome</option>
        <option value="cognome">Cognome</option>
        <option value="indirizzo">Indirizzo</option>
        <option value="cap">Comune</option>
        <option value="citta">Regione</option>
        <option value="provincia">Provincia</option>
        <option value="tel">Telefono</option>
        <option value="tel2">Telefono 2</option>
        <option value="datan">Data di Nascita</option>
        <option value="materia">Tipo di Socio</option>
        <option value="classe">Funzione</option>
        <option value="nomerif">Codice Fiscale</option>
        <option value="mansione">Mansione</option>
        <option value="email">E-mail</option>
        <option value="note">Note</option>
        <option value="immagine">Immagine</option>
        <option value="associato">Socio Attivo (sì / no)</option>
      </select>

      <label for="record">Nuovo valore:</label>
      <input name="record" id="record" type="text" required placeholder="Inserisci il nuovo valore">

      <div class="form-buttons" style="justify-content:center;">
        <input value="Modifica" type="submit" class="btn-edit" <?php echo $limit; ?>>
      </div>
    </form>
  </div>
</div>
