<?php
/*======================================================================+
 File name   : Install3.php
 Description : Ultima fase installazione - inserimento dati Associazione
 Author      : Sergio Capretta
=========================================================================+*/
include('./top.inc');
?>

<center><h2>Dati dell'Associazione</h2></center>
<center><small>Inserisci tutti i dati dell'Associazione</small></center>
<br>
<center><progress value="75" max="100">75%</progress></center>

<form action="./conf_Dati.php" method="POST">
  <table align="center" width="50%">
    <tbody>
      <tr>
        <td width="70%"><font color="red">Nome Associazione:</font></td>
        <td><input name="nome" size="30%" type="text" required></td>
      </tr>
      <tr>
        <td>Via:</td>
        <td><input name="indirizzo" size="30%" type="text"></td>
      </tr>
      <tr>
        <td>Numero:</td>
        <td><input name="numero" size="30%" type="text"></td>
      </tr>
      <tr>
        <td>CAP:</td>
        <td><input name="cap" size="30%" type="number"></td>
      </tr>
      <tr>
        <td>Citt&agrave;:</td>
        <td><input name="citta" size="30%" type="text"></td>
      </tr>
      <tr>
        <td>Provincia:</td>
        <td><input name="provincia" size="30%" type="text"></td>
      </tr>
      <tr>
        <td>Telefono:</td>
        <td><input name="tel" size="30%" type="tel"></td>
      </tr>
      <tr>
        <td>Fax:</td>
        <td><input name="fax" size="30%" type="tel"></td>
      </tr>
      <tr>
        <td><font color="red">Codice Fiscale ed eventuale Partita IVA:</font></td>
        <td><input name="cf" size="30%" type="text" required></td>
      </tr>
      <tr>
        <td>Indirizzo e-mail:</td>
        <td><input name="email" size="30%" type="email"></td>
      </tr>
      <tr>
        <td>Indirizzo webmail:</td>
        <td><input name="webmail" size="30%" type="url"></td>
      </tr>
      <tr>
        <td>Indirizzo sito internet:</td>
        <td><input name="sito" size="30%" type="url"></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input value="Invia" type="submit">
        </td>
      </tr>
    </tbody>
  </table>
</form>

<?php include('./botton.inc'); ?>
