<?

include "index.php";

$date = new DateTime();
// echo $date->getTimestamp();
$leads['add'] = array(
    array(
        'name' => 'новая сделка',
        'created_at' => $date->getTimestamp(),
        'pipeline_id' => 3185971 , // Кальян на дом
        'status_id' => 32478000, // Позвонили/написали 
        'sale' => 1000, // Бюджет сделки
        'responsible_user_id' => 3921547, // Шерзод Фозилов
        'tags' => '', #Теги
        'custom_fields' => array(
            array(
                'id' => 450965, // выпадающий список
                'values' => array( 
                    array(
                        'value' => 632865, // Источник Сайт
                    ),
                ),
            ),
            array(
                'id' => 451399, // текстовое поле
                'values' => array(
                    array(
                        'value' => 13, // Кол-во
                    ),
                ),
            ),
        ),
    ),
/*    array(
        'name' => 'Бумага',
        'created_at' => 1298904164,
        'status_id' => 7087609,
        'sale' => 600200,
        'responsible_user_id' => 215309,
        'custom_fields' => array(
            array(
                #Нестандартное дополнительное поле типа "мультисписок", которое мы создали
                'id' => 426106,
                'values' => array(
                    1237756,
                    1237758,
                ),
            ),
        ),
    ),*/
);
/* Теперь подготовим данные, необходимые для запроса к серверу */
$subdomain = 'admin'; #Наш аккаунт - поддомен
#Формируем ссылку для запроса
$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads';
/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
работе с этой
библиотекой Вы можете прочитать в мануале. */
$curl = curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($leads));
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$lead = json_decode($out, true);

if(isset($lead['_embedded']['items'][0]['id'])){
    print_r($lead['_embedded']['items'][0]);
    $lead_id = $lead['_embedded']['items'][0]['id'];
    include 'contacts_add.php';
}

$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
$code = (int) $code;
$errors = array(
    301 => 'Moved permanently',
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
);
try
{
    #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
    if ($code != 200 && $code != 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
    }
} catch (Exception $E) {
    die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
}