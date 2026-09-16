<?php
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="fattura.xml"');

// Dati generali
$numero = htmlspecialchars($_POST['numero']);
$data = htmlspecialchars($_POST['data']);
$descrizione = htmlspecialchars($_POST['descrizione']);
$imponibile = floatval($_POST['imponibile']);
$iva = floatval($_POST['iva']);
$imposta = round($imponibile * $iva / 100, 2);
$totale = $imponibile + $imposta;

// Cedente
$denCed = htmlspecialchars($_POST['denominazioneCedente']);
$pivaCed = preg_replace('/\D/', '', $_POST['pivaCedente']);
$indirizzoCed = htmlspecialchars($_POST['indirizzoCedente']);
$capCed = htmlspecialchars($_POST['capCedente']);
$comuneCed = htmlspecialchars($_POST['comuneCedente']);
$provCed = htmlspecialchars($_POST['provCedente']);

// Cliente
$denCli = htmlspecialchars($_POST['denominazioneCliente']);
$pivaCli = htmlspecialchars($_POST['pivaCliente']);
$indirizzoCli = htmlspecialchars($_POST['indirizzoCliente']);
$capCli = htmlspecialchars($_POST['capCliente']);
$comuneCli = htmlspecialchars($_POST['comuneCliente']);
$provCli = htmlspecialchars($_POST['provCliente']);

// Pagamento
$iban = htmlspecialchars($_POST['iban']);
$banca = htmlspecialchars($_POST['banca']);

// XML base
$fattura = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
<p:FatturaElettronica versione="FPR12"
 xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2">
</p:FatturaElettronica>');

// === HEADER ===
$header = $fattura->addChild('FatturaElettronicaHeader', null, $fattura->getNamespaces(true)['p']);
$datiTrasm = $header->addChild('DatiTrasmissione', null, $fattura->getNamespaces(true)['p']);
$idTrasm = $datiTrasm->addChild('IdTrasmittente', null, $fattura->getNamespaces(true)['p']);
$idTrasm->addChild('IdPaese', 'IT', $fattura->getNamespaces(true)['p']);
$idTrasm->addChild('IdCodice', $pivaCed, $fattura->getNamespaces(true)['p']);
$datiTrasm->addChild('ProgressivoInvio', '00001', $fattura->getNamespaces(true)['p']);
$datiTrasm->addChild('FormatoTrasmissione', 'FPR12', $fattura->getNamespaces(true)['p']);
$datiTrasm->addChild('CodiceDestinatario', '0000000', $fattura->getNamespaces(true)['p']); // per privati

// CedentePrestatore
$cedente = $header->addChild('CedentePrestatore', null, $fattura->getNamespaces(true)['p']);
$datiAnagCed = $cedente->addChild('DatiAnagrafici', null, $fattura->getNamespaces(true)['p']);
$idIVA = $datiAnagCed->addChild('IdFiscaleIVA', null, $fattura->getNamespaces(true)['p']);
$idIVA->addChild('IdPaese', 'IT', $fattura->getNamespaces(true)['p']);
$idIVA->addChild('IdCodice', $pivaCed, $fattura->getNamespaces(true)['p']);
$anag = $datiAnagCed->addChild('Anagrafica', null, $fattura->getNamespaces(true)['p']);
$anag->addChild('Denominazione', $denCed, $fattura->getNamespaces(true)['p']);
$datiAnagCed->addChild('RegimeFiscale', 'RF01', $fattura->getNamespaces(true)['p']);
$sedeCed = $cedente->addChild('Sede', null, $fattura->getNamespaces(true)['p']);
$sedeCed->addChild('Indirizzo', $indirizzoCed, $fattura->getNamespaces(true)['p']);
$sedeCed->addChild('CAP', $capCed, $fattura->getNamespaces(true)['p']);
$sedeCed->addChild('Comune', $comuneCed, $fattura->getNamespaces(true)['p']);
$sedeCed->addChild('Provincia', $provCed, $fattura->getNamespaces(true)['p']);
$sedeCed->addChild('Nazione', 'IT', $fattura->getNamespaces(true)['p']);

// CessionarioCommittente
$cliente = $header->addChild('CessionarioCommittente', null, $fattura->getNamespaces(true)['p']);
$datiAnagCli = $cliente->addChild('DatiAnagrafici', null, $fattura->getNamespaces(true)['p']);
$anagCli = $datiAnagCli->addChild('Anagrafica', null, $fattura->getNamespaces(true)['p']);
$anagCli->addChild('Denominazione', $denCli, $fattura->getNamespaces(true)['p']);
$datiAnagCli->addChild('CodiceFiscale', $pivaCli, $fattura->getNamespaces(true)['p']);
$sedeCli = $cliente->addChild('Sede', null, $fattura->getNamespaces(true)['p']);
$sedeCli->addChild('Indirizzo', $indirizzoCli, $fattura->getNamespaces(true)['p']);
$sedeCli->addChild('CAP', $capCli, $fattura->getNamespaces(true)['p']);
$sedeCli->addChild('Comune', $comuneCli, $fattura->getNamespaces(true)['p']);
$sedeCli->addChild('Provincia', $provCli, $fattura->getNamespaces(true)['p']);
$sedeCli->addChild('Nazione', 'IT', $fattura->getNamespaces(true)['p']);

// === BODY ===
$body = $fattura->addChild('FatturaElettronicaBody', null, $fattura->getNamespaces(true)['p']);

// DatiGeneraliDocumento
$datiGenerali = $body->addChild('DatiGenerali', null, $fattura->getNamespaces(true)['p']);
$datiDoc = $datiGenerali->addChild('DatiGeneraliDocumento', null, $fattura->getNamespaces(true)['p']);
$datiDoc->addChild('TipoDocumento', 'TD01', $fattura->getNamespaces(true)['p']);
$datiDoc->addChild('Divisa', 'EUR', $fattura->getNamespaces(true)['p']);
$datiDoc->addChild('Data', $data, $fattura->getNamespaces(true)['p']);
$datiDoc->addChild('Numero', $numero, $fattura->getNamespaces(true)['p']);

// DatiBeniServizi
$beni = $body->addChild('DatiBeniServizi', null, $fattura->getNamespaces(true)['p']);
$linea = $beni->addChild('DettaglioLinee', null, $fattura->getNamespaces(true)['p']);
$linea->addChild('NumeroLinea', '1', $fattura->getNamespaces(true)['p']);
$linea->addChild('Descrizione', $descrizione, $fattura->getNamespaces(true)['p']);
$linea->addChild('Quantita', '1.00', $fattura->getNamespaces(true)['p']);
$linea->addChild('PrezzoUnitario', number_format($imponibile, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$linea->addChild('PrezzoTotale', number_format($imponibile, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$linea->addChild('AliquotaIVA', number_format($iva, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$riepilogo = $beni->addChild('DatiRiepilogo', null, $fattura->getNamespaces(true)['p']);
$riepilogo->addChild('AliquotaIVA', number_format($iva, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$riepilogo->addChild('ImponibileImporto', number_format($imponibile, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$riepilogo->addChild('Imposta', number_format($imposta, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$riepilogo->addChild('EsigibilitaIVA', 'I', $fattura->getNamespaces(true)['p']);

// DatiPagamento
$pagamento = $body->addChild('DatiPagamento', null, $fattura->getNamespaces(true)['p']);
$pagamento->addChild('CondizioniPagamento', 'TP01', $fattura->getNamespaces(true)['p']);
$dett = $pagamento->addChild('DettaglioPagamento', null, $fattura->getNamespaces(true)['p']);
$dett->addChild('ModalitaPagamento', 'MP05', $fattura->getNamespaces(true)['p']);
$dett->addChild('DataScadenzaPagamento', $data, $fattura->getNamespaces(true)['p']);
$dett->addChild('ImportoPagamento', number_format($totale, 2, '.', ''), $fattura->getNamespaces(true)['p']);
$dett->addChild('IstitutoFinanziario', $banca, $fattura->getNamespaces(true)['p']);
$dett->addChild('IBAN', $iban, $fattura->getNamespaces(true)['p']);

// Output finale
echo $fattura->asXML();
?>
