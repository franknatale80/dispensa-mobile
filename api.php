<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$db = 'dispensa';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['error' => 'DB connection failed']));
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        $macro = $_GET['macro'] ?? 'Cucina';
        $isCucina = $macro === 'Cucina';
        $stmt = $pdo->prepare("SELECT nome, quantita, unita, scadenza FROM prodotti WHERE macro_categoria = ? ORDER BY quantita DESC");
        $stmt->execute([$macro]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;

    case 'POST': // Aggiungi
        $macro = $input['macro'] ?? 'Cucina';
        $scad = $input['scadenza'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO prodotti (nome, macro_categoria, quantita, unita, scadenza) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$input['nome'], $macro, $input['qta'], $input['unita'], $scad]);
        echo json_encode(['success' => true]);
        break;

    case 'PUT': // Modifica
        $nome = $input['nome_old'];
        $macro = $input['macro'];
        $scad = $input['scadenza'] ?? null;
        $stmt = $pdo->prepare("UPDATE prodotti SET nome=?, quantita=?, unita=?, scadenza=? WHERE nome=? AND macro_categoria=?");
        $stmt->execute([$input['nome'], $input['qta'], $input['unita'], $scad, $nome, $macro]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $nome = $_GET['nome'];
        $macro = $_GET['macro'];
        $stmt = $pdo->prepare("DELETE FROM prodotti WHERE nome=? AND macro_categoria=?");
        $stmt->execute([$nome, $macro]);
        echo json_encode(['success' => true]);
        break;

    case 'AVVISI':
        $stmt = $pdo->prepare("SELECT macro_categoria, nome, scadenza FROM prodotti WHERE scadenza BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) ORDER BY macro_categoria");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
}
?>
