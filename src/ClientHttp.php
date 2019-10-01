<?php
/**
 *
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

    // Caso mande um payload.
    if (count($data) > 0) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $response = curl_exec($curl);

    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        // Tratar o erro quando retornar da API
        return false;
    }
    curl_close($curl);

    if ($statusCode >= 400 && $statusCode <= 500) {
        // Tratar o erro quando retornar da API
        return false;
    }

    return json_decode(utf8_encode($response), true);
}
