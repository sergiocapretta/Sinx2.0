<?php
session_start();

if (($_SESSION['utente'] ?? null) !== 'admin') {
  http_response_code(403);
  exit('Accesso non autorizzato.');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  header('Allow: POST');
  exit('Metodo non consentito.');
}

$sessionToken = $_SESSION['delete_associato_token'] ?? '';
$postedToken = $_POST['csrf_token'] ?? '';
if (!is_string($sessionToken) || $sessionToken === ''
    || !is_string($postedToken) || !hash_equals($sessionToken, $postedToken)) {
  http_response_code(403);
  exit('Richiesta non valida. Riapri la scheda e riprova.');
}

$id = filter_var($_POST['id_anagrafe'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
  http_response_code(400);
  exit('Identificativo associato non valido.');
}

include('./dati_db.inc');

try {
  $connect = isset($port)
    ? mysqli_connect($host, $username, $password, $db_name, $port)
    : mysqli_connect($host, $username, $password, $db_name);
  if (!$connect) {
    throw new RuntimeException('Connessione al database non riuscita.');
  }

  $stmt = mysqli_prepare($connect, 'DELETE FROM tb_anagrafe WHERE id_anagrafe = ?');
  if (!$stmt) {
    throw new RuntimeException('Preparazione della cancellazione non riuscita.');
  }

  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  $deleted = mysqli_stmt_affected_rows($stmt);
  mysqli_stmt_close($stmt);
  mysqli_close($connect);

  if ($deleted !== 1) {
    http_response_code(404);
    exit('La scheda non esiste più o è già stata cancellata.');
  }

  unset($_SESSION['delete_associato_token']);
  header('Location: ./conferma.php?rif=ricerca');
  exit;
} catch (Throwable $error) {
  error_log('Errore cancellazione scheda associato #' . $id . ': ' . $error->getMessage());
  http_response_code(500);
  exit('Non è stato possibile cancellare la scheda. Verifica i log del server.');
}
?>
