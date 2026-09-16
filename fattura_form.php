<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Generatore Fattura Elettronica XML</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: 30px auto; }
    h2 { border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-top: 30px; }
    label { display: block; margin-top: 8px; }
    input { width: 100%; padding: 6px; margin-top: 2px; }
    button { margin-top: 20px; padding: 10px 20px; }
  </style>
</head>
<body>
  <h1>Generatore Fattura Elettronica XML (FPR12)</h1>

  <form action="genera_xml.php" method="post">
    <h2>Dati Cedente / Prestatore</h2>
    <label>Denominazione: <input type="text" name="denominazioneCedente" required></label>
    <label>Partita IVA: <input type="text" name="pivaCedente" required></label>
    <label>Indirizzo: <input type="text" name="indirizzoCedente" required></label>
    <label>CAP: <input type="text" name="capCedente" required></label>
    <label>Comune: <input type="text" name="comuneCedente" required></label>
    <label>Provincia: <input type="text" name="provCedente" required></label>

    <h2>Dati Cessionario / Committente</h2>
    <label>Denominazione: <input type="text" name="denominazioneCliente" required></label>
    <label>Codice Fiscale / P.IVA: <input type="text" name="pivaCliente" required></label>
    <label>Indirizzo: <input type="text" name="indirizzoCliente" required></label>
    <label>CAP: <input type="text" name="capCliente" required></label>
    <label>Comune: <input type="text" name="comuneCliente" required></label>
    <label>Provincia: <input type="text" name="provCliente" required></label>

    <h2>Dati Documento</h2>
    <label>Numero Fattura: <input type="text" name="numero" required></label>
    <label>Data: <input type="date" name="data" required></label>
    <label>Descrizione: <input type="text" name="descrizione" required></label>
    <label>Importo imponibile (€): <input type="number" step="0.01" name="imponibile" required></label>
    <label>Aliquota IVA (%): <input type="number" step="0.01" name="iva" value="22" required></label>

    <h2>Dati Pagamento</h2>
    <label>IBAN: <input type="text" name="iban" value="IT00X0000000000000000000000"></label>
    <label>Banca: <input type="text" name="banca" value="Nome della banca"></label>
    <button type="submit">Genera XML</button>
  </form>
</body>
</html>
