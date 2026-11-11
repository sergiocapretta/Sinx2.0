<?php
/*======================================================================+
 File name   : gest_files.php
 Begin       : 2012-07-08
 Last Update : 2012-07-08

 Description : Image and files upload

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
=========================================================================+*/

include('./top.inc');

?>
<style>
.steps{display:flex;gap:7px;margin:22px auto;max-width:900px;padding:0 12px}.step{flex:1;border-radius:7px;background:#e8edf3;padding:10px 5px;text-align:center;font:13px Arial,sans-serif;color:#5c6877}.step.active{background:#1f669e;color:#fff}
@media(max-width:650px){.steps{gap:3px;padding:0 5px}.step{font-size:10px;padding:8px 2px}}
</style>
      <form action='./conf_immagine_install.php' method='POST' enctype="multipart/form-data">
<div class="steps"><div class="step">1. Database</div><div class="step">2. Preparazione</div><div class="step">3. Amministratore</div><div class="step active">4. Logo</div><div class="step">5. Associazione</div></div>
<center><h2>Caricamento Logo Associazione</h2></center>
<center><small>L'inserimento del logo dell'Associazione serve per predisporre l'intestazione per le stampe</small></center>
<br>
<center><progress value="50" max="100">50%</progress></center>
<table align='center' width='80%'>

	    <tr>
	      <td width='150'>Carica Immagine:<input type="hidden" name="MAX_FILE_SIZE" value="30000"> </td>
	      <td> <input name="immagine" type="file" accept="image/png"><br><small><sub><i>Inserisci il logo dell'Associazione <b>massimo 30 kByte</b></small></i></sub></td>
	      <td><small><sub><i>L'mmagine deve essere di tipo png; le dimensioni ottimali sono di 60px di altezza e non pu&ograve superare i 30Kb;<br><b>Dove verr&agrave nominata: "logo.png" in modo automatico</b><br>L'immagine risiede nella cartella 'Immagini' di Sinx</sub></small></td>
	    </tr>
            <tr>
              <td colspan='2' align='center'>
              <input value='Carica logo e continua' type='submit'><br><small>Puoi anche proseguire senza caricare il logo: sarà possibile inserirlo successivamente.</small></td>
            </tr>
</table>
</form>
