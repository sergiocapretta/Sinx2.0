<?php
session_start();

if ($_SESSION['utente'] !== 'admin') {
  header('Location: index.php');
  exit;
}

include('./dati_db.inc');
$connect = mysqli_connect($host, $username, $password, $db_name, $port)
  or die("Errore DB");
mysqli_set_charset($connect, "utf8mb4");

$id_fatt = (int)($_POST['id'] ?? 0);
if ($id_fatt <= 0) {
  die("Fattura non valida");
}

/* ======================================================
   PRESTATORE (Associazione)
====================================================== */
$q_assoc = mysqli_query($connect, "
  SELECT * FROM tb_anagrafe_associaz
  ORDER BY id_anagrafe ASC
  LIMIT 1
");
$assoc = mysqli_fetch_assoc($q_assoc);
if (!$assoc) {
  die("Dati associazione mancanti");
}

/* ======================================================
   CLIENTE
====================================================== */
$q_nome = mysqli_query($connect, "
  SELECT nome FROM tb_fatture
  WHERE id_fatt = $id_fatt
  LIMIT 1
");
$nome_cliente = mysqli_fetch_assoc($q_nome)['nome'] ?? '';

$q_cliente = mysqli_query($connect, "
  SELECT * FROM tb_anagrafe
  WHERE nome = '".mysqli_real_escape_string($connect, $nome_cliente)."'
  LIMIT 1
");
$cliente = mysqli_fetch_assoc($q_cliente);

/* ======================================================
   TOTALI
====================================================== */
$q_tot = mysqli_query($connect, "
  SELECT SUM(totale) AS totale
  FROM tb_fatture
  WHERE id_fatt = $id_fatt
");
$totale_fattura = mysqli_fetch_assoc($q_tot)['totale'] ?? 0;

/* ======================================================
   XML ROOT
====================================================== */
$xml = new SimpleXMLElement(
'<?xml version="1.0" encoding="UTF-8"?>
<p:FatturaElettronica
 xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 versione="FPA12"
 SistemaEmittente="SINX"/>'
);

/* ======================================================
   HEADER
====================================================== */
$header = $xml->addChild('FatturaElettronicaHeader');

/* --- DatiTrasmissione --- */
$dt = $header->addChild('DatiTrasmissione');
$idt = $dt->addChild('IdTrasmittente');
$idt->addChild('IdPaese', 'IT');
$idt->addChild('IdCodice', preg_replace('/\D/', '', $assoc['cf'] ?? '00000000000'));
$dt->addChild('ProgressivoInvio', $id_fatt);
$dt->addChild('FormatoTrasmissione', 'FPA12');
$dt->addChild('CodiceDestinatario', '0000000');

/* --- CedentePrestatore --- */
$cp = $header->addChild('CedentePrestatore');

$da = $cp->addChild('DatiAnagrafici');
$iva = $da->addChild('IdFiscaleIVA');
$iva->addChild('IdPaese', 'IT');
$iva->addChild('IdCodice', preg_replace('/\D/', '', $assoc['cf'] ?? '00000000000'));
$da->addChild('CodiceFiscale', preg_replace('/\D/', '', $assoc['cf'] ?? '00000000000'));

$an = $da->addChild('Anagrafica');
$an->addChild('Denominazione', $assoc['nome']);

$da->addChild('RegimeFiscale', 'RF01');

$sede = $cp->addChild('Sede');
$sede->addChild('Indirizzo', $assoc['indirizzo'] . ' ' . $assoc['numero']);
$sede->addChild('CAP', $assoc['cap']);
$sede->addChild('Comune', $assoc['citta']);
$sede->addChild('Provincia', $assoc['provincia']);
$sede->addChild('Nazione', 'IT');

$cont = $cp->addChild('Contatti');
$cont->addChild('Telefono', $assoc['tel']);
$cont->addChild('Email', $assoc['email']);

/* --- CessionarioCommittente --- */
$cc = $header->addChild('CessionarioCommittente');
$da = $cc->addChild('DatiAnagrafici');
$da->addChild('CodiceFiscale', $cliente['codice_fiscale'] ?? '00000000000');

$an = $da->addChild('Anagrafica');
$an->addChild('Denominazione', $cliente['nome']);

$sede = $cc->addChild('Sede');
$sede->addChild('Indirizzo', $cliente['indirizzo']);
$sede->addChild('CAP', $cliente['cap']);
$sede->addChild('Comune', $cliente['citta']);
$sede->addChild('Provincia', $cliente['provincia']);
$sede->addChild('Nazione', 'IT');

/* ======================================================
   BODY
====================================================== */
$body = $xml->addChild('FatturaElettronicaBody');

/* --- DatiGeneraliDocumento --- */
$dgd = $body->addChild('DatiGenerali')
            ->addChild('DatiGeneraliDocumento');

$dgd->addChild('TipoDocumento', 'TD01');
$dgd->addChild('Divisa', 'EUR');
$dgd->addChild('Data', date('Y-m-d'));
$dgd->addChild('Numero', $id_fatt);
$dgd->addChild('ImportoTotaleDocumento', number_format($totale_fattura, 2, '.', ''));
$dgd->addChild('Causale', 'Fattura emessa da Sinx');

/* --- DatiBeniServizi --- */
$bs = $body->addChild('DatiBeniServizi');

$q_righe = mysqli_query($connect, "
  SELECT * FROM tb_fatture
  WHERE id_fatt = $id_fatt
");

$linea = 1;
$imponibile = 0;
$iva_tot = 0;
$aliquota = 0;

while ($r = mysqli_fetch_assoc($q_righe)) {
  $det = $bs->addChild('DettaglioLinee');
  $det->addChild('NumeroLinea', $linea++);
  $det->addChild('Descrizione', $r['descr']);
  $det->addChild('Quantita', number_format($r['quantita'], 6, '.', ''));
  $det->addChild('PrezzoUnitario', number_format($r['euro'], 6, '.', ''));
  $det->addChild('PrezzoTotale', number_format($r['euro'] * $r['quantita'], 6, '.', ''));
  $det->addChild('AliquotaIVA', number_format($r['iva'], 2, '.', ''));

  $imponibile += $r['euro'] * $r['quantita'];
  $aliquota = $r['iva'];
}

$iva_tot = $imponibile * $aliquota / 100;

$riep = $bs->addChild('DatiRiepilogo');
$riep->addChild('AliquotaIVA', number_format($aliquota, 2, '.', ''));
$riep->addChild('ImponibileImporto', number_format($imponibile, 2, '.', ''));
$riep->addChild('Imposta', number_format($iva_tot, 2, '.', ''));
$riep->addChild('EsigibilitaIVA', 'S');
$riep->addChild('RiferimentoNormativo', 'IVA ' . $aliquota . '%');

/* --- DatiPagamento --- */
$dp = $body->addChild('DatiPagamento');
$dp->addChild('CondizioniPagamento', 'TP02');

$detp = $dp->addChild('DettaglioPagamento');
$detp->addChild('ModalitaPagamento', 'MP05');
$detp->addChild('ImportoPagamento', number_format($imponibile, 2, '.', ''));
$detp->addChild('IBAN', $assoc['IBAN']);

/* ======================================================
   SALVATAGGIO FILE
====================================================== */
$filename = "./Download/Fattura_$id_fatt.xml";
$xml->asXML($filename);

mysqli_close($connect);
header("Location: ./conferma.php?rif=Files");
exit;
