<?php
require_once(__DIR__ . '/Helpers.php');

$environment = 'integracao';
$baseUrl = 'http://' . $environment . '.api.memed.com.br/v1/sinapse-prescricao/usuarios';
$apiKey = 'iJGiB4kjDGOLeDFPWMG3no9VnN7Abpqe3w1jEFm6olkhkZD6oSfSmYCm';
$secretKey = 'Xe8M5GvBGCr4FStKfxXKisRo3SfYKI7KrTMkJpCAstzu2yXVN4av5nmL';

try {
    // Essa variavel representa o atendimento
   $attendance = [
       'paciente' => [
           'ddd'      => '11',
           'celular'  => '11956765676',
           'rg'       => '123433439',
           'nome'     => 'Susumu Koga',
           'cidade'   => 'Caraguatatuba',
           'endereco' => 'Rua  Jacarandá, 491  - Martim de Sá',
       ],
       'medico'   => [
           'dataNascimento' => '23/09/1992',
           'cpf'            => '34009454040',
           'email'          => 'meu@email.com',
           'estado'         => 'SP',
           'cidade'         => 'São Paulo',
           'sexo'           => 'M',
           'nome'           => 'Nome do medico',
           'crm'            => '1234',
           'especialidade'  => 62,
       ],
   ];

    // Primeiro eu tento pegar o médico na memed.
    // https://github.com/MemedDev/sinapse/blob/master/doc/prescricao.md#recuperando-o-token-de-um-usu%C3%A1rio-previamente-cadastrado
    $urlGetDoctor = $baseUrl . '/' . $attendance['medico']['cpf'] . '?api-key=' . $apiKey . '&secret-key=' . $secretKey;
    $user = execRequest($urlGetDoctor);

    // Se não retornar usuário, mandamos a request para criar
    if ($user === false) {
        // https://github.com/MemedDev/sinapse/blob/master/doc/prescricao.md#integrando-com-a-api
        $url = $baseUrl . '?api-key=' . $apiKey . '&secret-key=' . $secretKey;
        $user = createDoctor($attendance, $url, $environment);

        if ($user === false) {
            echo 'Não conseguimos cadastrar o médico. <br> Verifique as informações enviadas ou entre em contato pelo email: <b>suporte@memed.com.br</b>.';
            die();
        }
    }

    $token = $user['data']['attributes']['token'];
} catch (Exception $e) {
    echo 'Ocorreu um erro na aplicação: ' . $e->getMessage();
    die();
}
