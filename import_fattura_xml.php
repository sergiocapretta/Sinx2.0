<?php
session_start();

if ($_SESSION['utente'] !== 'admin') {
  header('Location: index.php');
  exit;
}

if (empty($_POST['nome_cliente'])) {
  die("Cliente non selezionato");
}

$nome_cliente = $_POST['nome_cliente'];

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("DB error");
mysqli_set_charset($connect, "utf8mb4");

$nome_cliente = mysqli_real_escape_string($connect, $nome_cliente);

// === FILE XML ===
if (!isset($_FILES['fattura_xml']) || $_FILES['fattura_xml']['error'] !== 0) {
  die("File XML non valido");
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($_FILES['fattura_xml']['tmp_name']);

if (!$xml) {
  die("Errore nel file XML");
}

// === DATI GENERALI ===
$datiDocumento = $xml->FatturaElettronicaBody
                  ->DatiGenerali
                  ->DatiGeneraliDocumento;

$data = (string)$datiDocumento->Data;
$tipoDocumento = (string)$datiDocumento->TipoDocumento;
$numeroDocumento = (string)$datiDocumento->Numero;
$tipoDocumentoDb = mysqli_real_escape_string($connect, $tipoDocumento);
$numeroDocumentoDb = mysqli_real_escape_string($connect, $numeroDocumento);
$segno = ($tipoDocumento === 'TD04') ? -1 : 1;

$totaleDoc = (float)$datiDocumento->ImportoTotaleDocumento;
if ($segno === -1) {
  $totaleDoc = -abs($totaleDoc);
}

$modpaga = (string)$xml->FatturaElettronicaBody
             ->DatiPagamento
             ->DettaglioPagamento
             ->ModalitaPagamento;

// === NUOVO ID FATTURA ===
$res = mysqli_query($connect, "SELECT MAX(id_tot_fatture)+1 AS id FROM tb_tot_fatture");
$id_fatt = mysqli_fetch_assoc($res)['id'] ?? 1;

// === RIGHE FATTURA ===
foreach ($xml->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee as $r) {

  $descr = mysqli_real_escape_string($connect, (string)$r->Descrizione);
  $qta   = (float)$r->Quantita;
  $prezzo = (float)$r->PrezzoUnitario * $segno;
  $iva   = (int)$r->AliquotaIVA;
  $imponibile = (float)$r->PrezzoTotale * $segno;
  $tot = $imponibile + ($imponibile * $iva / 100);

  mysqli_query($connect, "
    INSERT INTO tb_fatture
    (id_fatt, nome, data, euro, quantita, iva, descr, modpaga, totale)
    VALUES
    ($id_fatt, '$nome_cliente', '$data', $prezzo, $qta, $iva, '$descr', '$modpaga', $tot)
  ");
}

// === TOTALE FATTURA ===
mysqli_query($connect, "
  INSERT INTO tb_tot_fatture
  (id_tot_fatture, nome, data, tot_fattura, tipo_documento, numero_documento)
  VALUES
  ($id_fatt, '$nome_cliente', '$data', $totaleDoc, '$tipoDocumentoDb', '$numeroDocumentoDb')
");

mysqli_close($connect);

// ritorno alla pagina fatture
header("Location: InsFattura.php");
exit;
