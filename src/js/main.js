// Essa variavel, representa um service http, com os header
var api = axios.create({
  // Alterar a URL do domínio da memed, quando for para produção
  baseURL: 'https://sandbox.api.memed.com.br/v1',
  headers: {
    'Accept': 'application/vnd.api+json',
    'Content-Type': 'application/json'
  }
});

/**
 *  Faz uma request para api da memed, capturando a prescrição gerada.
 */
function getPrescription (idPrescription) {
  var token = document.getElementById('token').value;

  api.get('/prescricoes/' + idPrescription + '?token=' + token)
    .then(function (response) {
      // Aqui enviará para a base do Tasy.
      // Pode ser enviado para um webservice alguma aplicação web com direto de escrita.
    });
}
/**
 * Cria o script do Memed Sinapse
 */
function initMemed () {
  var script = document.createElement('script');
  var tokenMedico = document.getElementById('token').value;
  script.setAttribute('type', 'text/javascript');
  script.setAttribute('data-color', '#576cff');
  script.setAttribute('data-token', tokenMedico);
  script.src = 'https://sandbox.memed.com.br/modulos/plataforma.sinapse-prescricao/build/sinapse-prescricao.min.js';
  script.onload = function() {
      initEventsMemed();
  };
  document.body.appendChild(script);
}

/**
 * Inicia os eventos de escuta e mostra o front da prescrição
 */
function initEventsMemed () {
  MdSinapsePrescricao.event.add('core:moduleInit', function moduleInitHandler (module) {

    if (module.name === 'plataforma.prescricao') {
      // Evento é chamado quando o usuário clica em "Emitir e enviar"
      // https://ajuda.memed.com.br/pt-BR/articles/2519513-formas-de-recuperacao-da-receita-medica
      MdHub.event.add('prescricaoImpressa', function(prescriptionData) {
        console.log(prescriptionData);
        // No objeto "prescriptionData" é retornado as informações da prescrição gerada.
        // Implementar ações, callbacks, etc. para salvar informações da prescrição em banco
      });

      /**
       * Evento é chamado em dois momentos:
       * - Usuário exclui uma receita;
       * - Usuário edita uma receita (é feita a exclusão da anterior e a criação de uma nova).
       * https://ajuda.memed.com.br/pt-BR/articles/2620604-capturando-o-evento-de-prescricao-excluida
       */
      MdHub.event.add('prescricaoExcluida', function(idPrescricao) {
        console.log('REMOVEU', idPrescricao)
        // Implementar ações, callbacks, etc. para excluir informações da prescrição em banco
      });

      // Definindo features que estarão inativas
      // https://ajuda.memed.com.br/pt-BR/articles/2519417-ativar-ou-desativar-recursos-via-feature-toggle
      MdHub.command.send('plataforma.prescricao', 'setFeatureToggle', {
        // Desativa a opção de excluir um paciente (obrigatório)
        deletePatient: false,
        // Desabilita a opção de remover/trocar o paciente (obrigatório)
        removePatient: false,
        // Esconde o formulário de edição do paciente (obrigatório)
        editPatient: false
      });

      // Define o pacientes
      MdHub.command.send('plataforma.prescricao', 'setPaciente', {
        nome: document.getElementById('namePatient').value,
        endereco: document.getElementById('enderecoPatient').value,
        cidade: document.getElementById('cidadePatient').value,
        telefone: document.getElementById('cellPhonePatient').value,
        idExterno: document.getElementById('externalId').value,
      }).then(function success() {
        // Definindo local de trabalho do médico
        // https://ajuda.memed.com.br/pt-BR/articles/3459202-definir-local-de-trabalho
        MdHub.command.send('plataforma.prescricao', 'setWorkplace', {
          city: 'São Paulo',
          state: 'SP',
          cnes: 1234,
          local_name: 'Clinica Memed',
          address: 'Rua Arthur Prado, 513',
          phone: 11999999999
        });

        // Definindo dados adicionais e configuração de cabeçalho
        // https://ajuda.memed.com.br/pt-BR/articles/2519497-metadados-e-informacoes-de-impressao-adicionais-no-cabecalho-e-rodape
        MdHub.command.send('plataforma.prescricao', 'setAdditionalData', {
          // Campos de exemplo para sair na impressão.
          "header": [
            // cada item do array se transforma em uma linha.
            {
              "Registro": "2911116",
              "Paciente": "Jose da Silva"
            },
            {
              "Sexo": "Masculino",
              "Estado Civil": "Solteiro",
              "Data de Nasc": "17\/09\/1991"
            },
            {
              "Endereço": "Rua  Arthur prado, 513"
            },
            {
              "Profissional": "Nome do medico(CRM: 33221100SP)"
            }
          ],
          // Para pular de linha no ropapé, envie a string com um "\n"
          'footer': "Rodapé da prescrição \n Segunda linha do rodapé",
          // Definindo dados adicionais
          'numeroProntuario': 123,
          'atendimento': 321,
          'outraInformacao': 'Campo retornado após a prescrição é gerada',
        });

        // Definindo alergia a amoxicilina
        // https://ajuda.memed.com.br/pt-BR/articles/2519488-definir-alergias
        MdHub.command.send('plataforma.prescricao', 'setAllergy', [174]);

        // Exibe a UI da Memed
        MdHub.module.show(module.name);
      });
    }
  });
}

initMemed();
