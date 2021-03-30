<?php

require_once(__DIR__ . '/ClientHttp.php');

/**
 * CRIA UM MÉDICO NA MEMED
 *
 * @param $attendance
 * @param $url
 *
 * @return bool|mixed
 * @throws Exception
 */
function createDoctor($attendance, $url)
{
    $crm = preg_replace("/[^0-9]/", "", $attendance['medico']['crm']);

    $name = explode(' ', $attendance['medico']['nome']);
    $lastName = array_pop($name);

    $specialtyId = getSpecialty($attendance['medico']);

    $city = getCity($attendance['medico']);

    $payload = [
        'data' => [
            'type'          => 'usuarios',
            'attributes'    => [
                'external_id'     => $attendance['medico']['cpf'],
                'nome'            => $name[0],
                'data_nascimento' => $attendance['medico']['dataNascimento'],
                'cpf'             => $attendance['medico']['cpf'],
                'sobrenome'       => $lastName,
                'email'           => $attendance['medico']['email'],
                'uf'              => $attendance['medico']['estado'],
                'sexo'            => $attendance['medico']['sexo'],
                'crm'             => $crm,
            ],
            'relationships' => [
                'cidade'        => [
                    'data' => [
                        'type' => 'cidades',
                        'id'   => $city['id'], // Faça uma verificação antes de enviar o id da cidade
                    ],
                ],
                'especialidade' => [
                    'data' => [
                        'type' => 'especialidades',
                        'id'   => $specialtyId, // Faça uma verificação antes de enviar o id da especialidade
                    ],
                ],
            ],
        ],
    ];

    $response = execRequest($url, $payload, [
        CURLOPT_HTTPHEADER => [
            'Accept:application/vnd.api+json',
            'Cache-Control:no-cache',
            'Content-Type:application/json',
        ],
    ]);

    if ($response !== false) {
        // Alterar a url quando for para produção.
        createConfigurationPrescription('https://sandbox.api.memed.com.br/v1/opcoes-receituario/?token=' . $response['data']['attributes']['token']);
    }
    return $response;
}

/**
 * Cria uma configuração padrão para as o
 *
 * @param $doctor
 *
 * @return bool|mixed
 * @throws Exception
 */
function createConfigurationPrescription($url)
{
    $payload = [
        'data' => [
            'type'       => 'configuracoes-prescricao',
            'attributes' => [
                'ativo'                                   => true,
                'indice'                                  => '4',
                'altura_papel'                            => '29.7',
                'cidade_medico'                           => '',
                'endereco_medico'                         => '',
                'espacamento'                             => '30',
                'fonte'                                   => 'Arial',
                'imprimir_controle_especial'              => true,
                'imprimir_controle_especial_antibioticos' => true,
                'imprimir_controle_especial_c4'           => true,
                'imprimir_lme'                            => true,
                'largura_papel'                           => '21',
                'margem_direita'                          => '1.5',
                'margem_esquerda'                         => '1.5',
                'margem_inferior'                         => '0.7',
                'margem_superior'                         => '1',
                'modelo_cabecalho_rodape'                 => '0',
                'mostrar_cabecalho_rodape_simples'        => '1',
                'mostrar_cabecalho_rodape_especial'       => '1',
                'mostrar_data'                            => '1',
                'mostrar_label_nome_paciente'             => true,
                'mostrar_label_paciente_especial'         => true,
                'mostrar_nome_fabricante'                 => true,
                'mostrar_unidades'                        => 'false',
                'mostrar_unidades_especial'               => 'false',
                'nome_medico'                             => '',
                'separador_medicamento'                   => '0',
                'separador_uso'                           => '0',
                'separar_por_uso'                         => 'false',
                'subtitulo'                               => '',
                'subtitulo_cor'                           => '#000000',
                'subtitulo_fonte'                         => 'Arial',
                'subtitulo_tamanho_fonte'                 => '14',
                'tamanho_cabecalho'                       => '2',
                'tamanho_fonte'                           => '16',
                'tamanho_rodape'                          => '3',
                'telefone_medico'                         => '',
                'titulo'                                  => 'Receita Médica',
                'titulo_cor'                              => '#000000',
                'titulo_fonte'                            => 'Arial',
                'titulo_tamanho_fonte'                    => '22',
                'rodape'                                  => "Primeira linha \n Segunda linha \n Telefone: (11) 99999-99999 - Site: www.meu-site.com.br",
                'rodape_cor'                              => '#000000',
                'rodape_fonte'                            => 'Arial',
                'rodape_tamanho_fonte'                    => '12',
                'modelo_rodape'                           => '2',
                'mostrar_data_controle_especial'          => '1',
                'id'                                      => '',
                'linhas'                                  => '0',
                'width_logo'                              => '424',
                'logo_src'                                => 'LOGO_EM_BASE_64',
                'logo_nome'                               => 'logo.png',
                'zoom_logo'                               => '40',
                'height_logo'                             => '143.99999999999997',
            ],
        ],
    ];

    $options = [
        CURLOPT_HTTPHEADER     => [
            'Accept:application/vnd.api+json',
            'Content-Type:application/json'
        ],
    ];

    $response = execRequest($url, $payload, $options);

    if ($response === false) {
        echo 'Ocorreu um erro ao gravar as configurações padrões da receita.';
        exit;
    }

    return $response;
}

/**
 * @param $medico
 *
 * @return int|array
 */
function getSpecialty($medico)
{
   // Aqui você retorna o id especialidade do médico. 59 é generalista
   return 59;
}

/**
 * @param $medico
 *
 * @return bool|int|mixed
 */
function getCity($medico)
{
    try {
        // Alterar a url quando for para produção
        $city = execRequest('https://sandbox.api.memed.com.br/v1/cidades?filter[q]=' . urlencode($medico['cidade']));

        if (is_array($city)) {
            return $city['data'][0];
        }

        if ($city === false) {
            return false;
        }

        return $city;

    } catch (Exception $e) {
        writeLog('Erro ao pegar o id da cidade: ' . $e->getMessage());
        return 5213;
    }
}
