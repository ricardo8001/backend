<?php
header('Content-Type: application/json');

function getBinInfo($bin) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://bins.antipublic.cc/bins/" . $bin);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'accept: application/json',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
    ));
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200 || !$response) {
        return ['error' => true, 'message' => 'Erro ao consultar BIN'];
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => true, 'message' => 'Erro ao decodificar resposta do BIN'];
    }

    $pais = $data['country_name'] ?? 'Desconocido';
    $bandeira = $data['brand'] ?? 'Desconocido';
    $tipo = $data['type'] ?? 'Desconocido';
    $nivel = $data['level'] ?? 'Desconocido';
    $banco = $data['bank'] ?? 'Desconocido';

    $cardInfo = strtoupper(strtolower("$bandeira $banco $tipo $nivel ($pais)"));
    return ['error' => false, 'cardInfo' => $cardInfo];
}

if (isset($_POST['bin'])) {
    $bin = preg_replace('/[^0-9]/', '', $_POST['bin']);
    if (strlen($bin) >= 6) {
        echo json_encode(getBinInfo(substr($bin, 0, 6)));
    } else {
        echo json_encode(['error' => true, 'message' => 'BIN inválido']);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Nenhum BIN fornecido']);
}
?>