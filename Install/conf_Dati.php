<?php
/*======================================================================+
 File name   : conf_dati.php
 Last Update : 2025-11-07
 Description : Conferma dati e scrittura su DB
 Author      : Sergio Capretta
=========================================================================+*/

include ('../dati_db.inc');

$link = mysqli_connect($host, $username, $password, $db_name)
    or die("Connessione fallita: " . mysqli_connect_error());

// Prelevo i dati dal form
$nnome     = trim($_POST['nome'] ?? '');
$indirizzo = trim($_POST['indirizzo'] ?? '');
$numero    = trim($_POST['numero'] ?? '');
$cap       = trim($_POST['cap'] ?? '');
$citta     = trim($_POST['citta'] ?? '');
$provincia = trim($_POST['provincia'] ?? '');
$tel       = trim($_POST['tel'] ?? '');
$fax       = trim($_POST['fax'] ?? '');
$cf        = trim($_POST['cf'] ?? '');
$email     = trim($_POST['email'] ?? '');
$webmail   = trim($_POST['webmail'] ?? '');
$sito      = trim($_POST['sito'] ?? '');

if ($nnome === '' || $cf === '') {
    echo "<h3>Errore: Nome e Codice Fiscale sono obbligatori.</h3>";
    exit;
}

$provenienza = $_SERVER['HTTP_REFERER'] ?? 'Sconosciuta';
$data = date("d-m-y"); $ora = date("G:i:s");
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

// Preparo la query
$sql = "INSERT INTO tb_anagrafe_associaz
    (nome, indirizzo, numero, cap, citta, provincia, tel, fax, cf, email, webmail, sito)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Preparo lo statement
$stmt = mysqli_prepare($link, $sql);
if (!$stmt) {
    die("Errore nella preparazione della query: " . mysqli_error($link));
}

mysqli_stmt_bind_param($stmt, "ssssssssssss",
    $nnome, $indirizzo, $numero, $cap, $citta, $provincia,
    $tel, $fax, $cf, $email, $webmail, $sito
);

if (!mysqli_stmt_execute($stmt)) {
    die("<h3>Errore durante l'inserimento: " . mysqli_error($link) . "</h3>");
}

mysqli_stmt_close($stmt);
mysqli_close($link);

// Conferma e redirect

$recipient = "sergio.capretta@gmail.com";
$subject   = "Installazione Sinx da $ip";

$formcontent  = "È stata eseguita una nuova installazione di Sinx.\n\n";
$formcontent .= "Nome associazione: $nnome\n";
$formcontent .= "Provenienza: $provenienza\n";
$formcontent .= "Data: $data $ora\n";
$formcontent .= "IP: $ip\n";
$formcontent .= "Browser: $browser\n";

$headers  = "From: installazioni@sinx.it\r\n";
$headers .= "Reply-To: installazioni@sinx.it\r\n";

mail($recipient, $subject, $formcontent, $headers);

echo "<center><h2>Installazione effettuata con successo</h2></center>";

function redirect($url, $tempo = 2) {
    if (!headers_sent()) {
        header("Refresh: {$tempo}; url={$url}");
    } else {
        echo "<meta http-equiv=\"refresh\" content=\"{$tempo}; url={$url}\">";
    }
}
redirect('../index.php', 2);
?>
