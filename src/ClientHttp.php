<?php
require_once(__DIR__ . '/log.php');

/**
 * @param      $uri
 * @param null $data
 * @param      $curlOptions
 *
 * @return bool|mixed
 * @throws Exception
 */
function execRequest($uri, $data = [], $curlOptions = [])
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => $uri,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Accept:application/json',
        ],
    ]);

    if (count($curlOptions) > 0) {
        curl_setopt_array($curl, $curlOptions);
    }

    if (count($data) > 0) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $response = curl_exec($curl);

    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        writeLog('Erro na request: ' . $uri . '   - Resposta: ' . $response . '   -  StatusCode: ' . $statusCode . "\n");
        return false;
    }
    curl_close($curl);

    if ($statusCode >= 400 && $statusCode <= 500) {

        // Trato essas mensagens para não escrever no log.
        if (strpos($response, 'O CRM enviado') === false &&
            strpos($response, 'encontrado com este este CRM ou Id') === false) {
                writeLog('Erro na request: ' . $uri . "\n   - Resposta: " . $response . '   -  StatusCode: ' . $statusCode . "\n");
        }

        return false;
    }

    return json_decode(utf8_encode($response), true);
}
