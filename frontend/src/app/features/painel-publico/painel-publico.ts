import {
  ChangeDetectorRef,
  Component,
  OnInit,
  inject
} from '@angular/core';

import {
  FormsModule
} from '@angular/forms';

import {
  HttpErrorResponse
} from '@angular/common/http';

import {
  TipoAtendimento
} from '../../shared/models/tipo-atendimento.model';

import {
  Senha
} from '../../shared/models/senha.model';

import {
  Balcao
} from '../../shared/models/balcao.model';

import {
  TipoAtendimentoService
} from '../tipos-atendimento/services/tipo-atendimento.service';

import {
  SenhaService
} from '../senhas/services/senha.service';

import {
  BalcaoService
} from '../balcoes/services/balcao.service';


@Component({
  selector: 'app-painel-publico',
  imports: [FormsModule],
  templateUrl: './painel-publico.html',
  styleUrl: './painel-publico.scss'
})

// Componente responsável pelo painel público de emissão e consulta de senhas
export class PainelPublico implements OnInit {

  // ============================================================
  // SERVICES
  // ============================================================

  // Service responsável pelos tipos de atendimento
  private readonly tipoAtendimentoService =
    inject(TipoAtendimentoService);

  // Service responsável pelas operações das senhas
  private readonly senhaService =
    inject(SenhaService);

  // Service responsável pelos balcões
  private readonly balcaoService =
    inject(BalcaoService);

  // Permite solicitar manualmente a atualização da interface
  private readonly changeDetector =
    inject(ChangeDetectorRef);


  // ============================================================
  // TIPOS DE ATENDIMENTO
  // ============================================================

  // Tipos de atendimento disponíveis
  tiposAtendimento: TipoAtendimento[] = [];

  // Controla o carregamento dos tipos de atendimento
  carregandoTiposAtendimento = true;

  // Tipo de atendimento atualmente selecionado
  tipoAtendimentoSelecionado:
    TipoAtendimento | null = null;


  // ============================================================
  // SENHAS
  // ============================================================

  // Senha que acabou de ser emitida
  senhaEmitida: Senha | null = null;

  // Senha encontrada através da consulta
  senhaConsultada: Senha | null = null;


  // ============================================================
  // CONSULTA
  // ============================================================

  // Código digitado pelo utilizador
  codigoConsulta = '';

  // Controla a exibição da tela de consulta
  mostrarConsulta = false;

  // Mensagem de erro da consulta
  erroConsulta = '';


  // ============================================================
  // BALCÕES
  // ============================================================

  // Balcões disponíveis
  balcoes: Balcao[] = [];


  // ============================================================
  // TEMPORIZADOR
  // ============================================================

  // Temporizador utilizado para retornar à tela inicial
  private temporizadorRetorno:
    ReturnType<typeof setTimeout> | null = null;


  // ============================================================
  // CICLO DE VIDA
  // ============================================================

  // Executado quando o componente é inicializado
  ngOnInit(): void {

    // Carrega os tipos de atendimento
    this.carregarTiposAtendimento();

    // Carrega os balcões
    this.carregarBalcoes();
  }


  // ============================================================
  // TIPOS DE ATENDIMENTO
  // ============================================================

  // Busca os tipos de atendimento disponíveis
  private carregarTiposAtendimento(): void {

    this.carregandoTiposAtendimento = true;

    this.tipoAtendimentoService.listar().subscribe({

      // API respondeu com sucesso
      next: (resposta) => {

        // Guarda os tipos recebidos
        this.tiposAtendimento = resposta.data;

        // Finaliza o carregamento
        this.carregandoTiposAtendimento = false;

        // Atualiza a interface
        this.atualizarInterface();
      },


      // API respondeu com erro
      error: (erro: HttpErrorResponse) => {

        // Registra o erro no console
        console.error(
          'Erro ao carregar tipos de atendimento:',
          erro
        );

        // Finaliza o carregamento
        this.carregandoTiposAtendimento = false;

        // Atualiza a interface
        this.atualizarInterface();
      }
    });
  }


  // Seleciona um tipo de atendimento
  selecionarTipoAtendimento(
    tipo: TipoAtendimento
  ): void {

    this.tipoAtendimentoSelecionado = tipo;
  }


  // ============================================================
  // EMISSÃO DE SENHA
  // ============================================================

  // Emite uma nova senha
  emitirSenha(): void {

    // Verifica se existe um tipo selecionado
    if (
      this.tipoAtendimentoSelecionado === null
    ) {

      console.error(
        'Selecione um tipo de atendimento.'
      );

      return;
    }


    // Solicita a emissão da senha
    this.senhaService
      .emitirSenha(
        this.tipoAtendimentoSelecionado.id
      )
      .subscribe({

        // Senha emitida com sucesso
        next: (resposta) => {

          // Guarda a senha recebida
          this.senhaEmitida = resposta.data;

          // Atualiza a interface
          this.atualizarInterface();

          // Inicia o retorno automático
          this.iniciarTemporizadorRetorno();
        },


        // Erro ao emitir a senha
        error: (erro: HttpErrorResponse) => {

          console.error(
            'Erro ao emitir senha:',
            erro
          );
        }
      });
  }


  // ============================================================
  // TEMPORIZADOR
  // ============================================================

  // Inicia o temporizador de retorno
  private iniciarTemporizadorRetorno(): void {

    this.limparTemporizador();

    this.temporizadorRetorno =
      setTimeout(() => {

        this.voltarTelaInicial();

      }, 10000);
  }


  // Cancela o temporizador atual
  private limparTemporizador(): void {

    if (
      this.temporizadorRetorno !== null
    ) {

      clearTimeout(
        this.temporizadorRetorno
      );

      this.temporizadorRetorno = null;
    }
  }


  // ============================================================
  // NAVEGAÇÃO DA INTERFACE
  // ============================================================

  // Volta para a tela inicial
  voltarTelaInicial(): void {

    this.limparTemporizador();

    this.senhaEmitida = null;

    this.tipoAtendimentoSelecionado = null;

    this.senhaConsultada = null;

    this.codigoConsulta = '';

    this.erroConsulta = '';

    this.mostrarConsulta = false;
  }


  // Abre a tela de consulta
  abrirConsulta(): void {

    this.limparTemporizador();

    this.senhaEmitida = null;

    this.tipoAtendimentoSelecionado = null;

    this.senhaConsultada = null;

    this.codigoConsulta = '';

    this.erroConsulta = '';

    this.mostrarConsulta = true;
  }


  // ============================================================
  // TIPOS DE ATENDIMENTO
  // ============================================================

  // Obtém o nome de um tipo de atendimento pelo ID
  obterNomeTipoAtendimento(
    tipoAtendimentoId: number
  ): string {

    const tipo =
      this.tiposAtendimento.find(
        (tipo) =>
          tipo.id === tipoAtendimentoId
      );

    return (
      tipo?.nome ??
      'Tipo de atendimento não encontrado'
    );
  }


  // ============================================================
  // BALCÕES
  // ============================================================

  // Busca os balcões disponíveis
  private carregarBalcoes(): void {

    this.balcaoService.listar().subscribe({

      // API respondeu com sucesso
      next: (resposta) => {

        this.balcoes = resposta.data;
      },


      // API respondeu com erro
      error: (erro: HttpErrorResponse) => {

        console.error(
          'Erro ao carregar balcões:',
          erro
        );
      }
    });
  }


  // Retorna o balcão correspondente ao ID
  obterBalcao(
    balcaoId: number | null
  ): Balcao | null {

    if (balcaoId === null) {
      return null;
    }

    return (
      this.balcoes.find(
        (balcao) =>
          balcao.id === balcaoId
      ) ?? null
    );
  }


  // ============================================================
  // CONSULTA DE SENHA
  // ============================================================

  // Busca uma senha pelo código
  buscarSenha(): void {

    // Limpa o estado anterior
    this.erroConsulta = '';

    this.senhaConsultada = null;

    // Atualiza a interface
    this.atualizarInterface();


    // Remove espaços do código
    const codigo =
      this.codigoConsulta.trim();


    // Verifica se o código foi informado
    if (codigo === '') {

      this.erroConsulta =
        'Digite o código de uma senha.';

      this.atualizarInterface();

      return;
    }


    // Consulta a senha na API
    this.senhaService
      .consultar(codigo)
      .subscribe({

        // Senha encontrada
        next: (resposta) => {

          this.senhaConsultada =
            resposta.data;

          this.atualizarInterface();
        },


        // Senha não encontrada ou erro na API
        error: (erro: HttpErrorResponse) => {

          this.erroConsulta =
            'Senha não encontrada.';

          this.atualizarInterface();

          console.error(
            'Erro ao consultar senha:',
            erro
          );
        }
      });
  }


  // ============================================================
  // INTERFACE
  // ============================================================

  // Solicita manualmente a atualização da interface
  private atualizarInterface(): void {

    this.changeDetector.detectChanges();
  }
}