<?php
/*======================================================================+
 File name   : menu.inc
 Begin       : 2010-08-04
 Last Update : 2013-08-13

 Description : secondary menu

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

//session_start();
$user = $_SESSION['utente'];
$langmenusx = $_SESSION['lingua'];
$paginamenusx = "menusx.inc";
$linguamenusx = ($langmenusx.$paginamenusx);
include($linguamenusx);

if ($user) {

?>
&nbsp;
<?php
include ('./Mese.php');

}
// mysqli_close($connect);
?>&nbsp;
<div id="menuButton"><a class="icon-menu"></a></div>
