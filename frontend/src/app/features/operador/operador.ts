import {
  ChangeDetectorRef,
  Component,
  inject
} from '@angular/core';

import {
  FormsModule
} from '@angular/forms';

import {
  Router
} from '@angular/router';

import {
  HttpErrorResponse
} from '@angular/common/http';

import {
  Senha
} from '../../shared/models/senha.model';

import {
  Balcao
} from '../../shared/models/balcao.model';

import {
  SenhaService
} from '../senhas/services/senha.service';

import {
  BalcaoService
} from '../balcoes/services/balcao.service';

import {
  AuthService
} from '../../core/services/auth.service';


@Component({
  selector: 'app-operador',
  imports: [
    FormsModule
  ],
  templateUrl: './operador.html',
  styleUrl: './operador.scss'
})
export class Operador {

  // =====================================================
  // DEPENDÊNCIAS
  // =====================================================

  private readonly router =
    inject(Router);

  private readonly changeDetector =
    inject(ChangeDetectorRef);

  private readonly authService =
    inject(AuthService);

  private readonly senhaService =
    inject(SenhaService);

  private readonly balcaoService =
    inject(BalcaoService);


  // =====================================================
  // ESTADO DO ATENDIMENTO
  // =====================================================

  // Senha atualmente selecionada
  senhaAtual: Senha | null = null;

  // Nome informado pelo operador
  nomeCliente = '';

  // Telefone informado pelo operador
  telefoneContacto = '';


  // =====================================================
  // ESTADO DOS BALCÕES
  // =====================================================

  // Balcões disponíveis
  balcoes: Balcao[] = [];

  // Controla a exibição da seleção de balcão
  mostrandoBalcoes = false;


  // =====================================================
  // ESTADO DA INTERFACE
  // =====================================================

  // Carregamento de operações de senha
  carregando = false;

  // Carregamento relacionado aos balcões
  carregandoBalcoes = false;


  // =====================================================
  // MENSAGENS
  // =====================================================

  mensagem: string | null = null;

  erro: string | null = null;


  // =====================================================
  // NAVEGAÇÃO
  // =====================================================

  // Volta para o Dashboard
  voltarDashboard(): void {

    this.router.navigate([
      '/dashboard'
    ]);

    this.changeDetector.detectChanges();
  }


  // =====================================================
  // CHAMAR PRÓXIMA SENHA
  // =====================================================

  chamarProxima(): void {

    // Limpa mensagens anteriores
    this.limparMensagens();

    // Atualiza a interface imediatamente
    this.changeDetector.detectChanges();


    // Obtém o balcão existente no JWT
    const balcaoId =
      this.authService.obterBalcaoId();


    // =================================================
    // NÃO EXISTE BALCÃO
    // =================================================

    if (balcaoId === null) {

      this.erro =
        'Selecione um balcão para continuar.';

      // Abre a seleção
      this.mostrandoBalcoes = true;

      // Atualiza a interface ANTES da requisição
      this.changeDetector.detectChanges();

      // Carrega os balcões
      this.carregarBalcoes();

      return;
    }


    // =================================================
    // JÁ EXISTE ATENDIMENTO
    // =================================================

    if (
      this.senhaAtual &&
      (
        this.senhaAtual.status === 'chamada' ||
        this.senhaAtual.status === 'em_atendimento'
      )
    ) {

      this.erro =
        'Finalize o atendimento atual antes de chamar outra senha.';

      this.changeDetector.detectChanges();

      return;
    }


    // =================================================
    // INICIA CARREGAMENTO
    // =================================================

    this.carregando = true;

    this.changeDetector.detectChanges();


    // =================================================
    // CHAMA A API
    // =================================================

    this.senhaService
      .chamarProxima()
      .subscribe({

        // =============================================
        // SUCESSO
        // =============================================

        next: (resposta) => {

          // Guarda a senha retornada pela API
          this.senhaAtual =
            resposta.data;

          // Limpa os dados do cliente
          this.nomeCliente = '';

          this.telefoneContacto = '';

          // Guarda a mensagem
          this.mensagem =
            resposta.message;

          // Finaliza carregamento
          this.carregando = false;

          // IMPORTANTE:
          // atualiza a tela depois da resposta HTTP
          this.changeDetector.detectChanges();
        },


        // =============================================
        // ERRO
        // =============================================

        error: (erro: HttpErrorResponse) => {

          this.erro =
            erro?.error?.message ??
            'Não foi possível chamar a próxima senha.';

          this.carregando = false;

          // Atualiza a interface depois do erro
          this.changeDetector.detectChanges();
        }
      });
  }


  // =====================================================
  // SELEÇÃO DE BALCÃO
  // =====================================================

  abrirSelecaoBalcao(): void {

    this.mensagem = null;

    this.erro = null;

    this.mostrandoBalcoes = true;

    this.changeDetector.detectChanges();

    this.carregarBalcoes();
  }


  // Busca os balcões disponíveis
  private carregarBalcoes(): void {

    this.carregandoBalcoes = true;

    this.changeDetector.detectChanges();


    this.balcaoService
      .listar()
      .subscribe({

        // =============================================
        // SUCESSO
        // =============================================

        next: (resposta) => {

          this.balcoes =
            resposta.data;

          this.carregandoBalcoes = false;

          this.changeDetector.detectChanges();
        },


        // =============================================
        // ERRO
        // =============================================

        error: (erro: HttpErrorResponse) => {

          this.erro =
            erro?.error?.message ??
            'Não foi possível carregar os balcões.';

          this.carregandoBalcoes = false;

          this.changeDetector.detectChanges();
        }
      });
  }


  // Seleciona o balcão escolhido pelo operador
  selecionarBalcao(
    balcaoId: number
  ): void {

    // Limpa mensagens
    this.limparMensagens();

    // Indica que o balcão está sendo selecionado
    this.carregandoBalcoes = true;

    this.changeDetector.detectChanges();


    this.balcaoService
      .selecionar(balcaoId)
      .subscribe({

        // =============================================
        // SUCESSO
        // =============================================

        next: (resposta) => {

          // Guarda o novo JWT
          this.authService.salvarToken(
            resposta.data.token
          );

          // Fecha a seleção
          this.mostrandoBalcoes = false;

          // Finaliza carregamento
          this.carregandoBalcoes = false;

          // Mostra confirmação
          this.mensagem =
            'Balcão selecionado com sucesso.';

          // Atualiza a interface
          this.changeDetector.detectChanges();
        },


        // =============================================
        // ERRO
        // =============================================

        error: (erro: HttpErrorResponse) => {

          this.erro =
            erro?.error?.message ??
            'Não foi possível selecionar o balcão.';

          this.carregandoBalcoes = false;

          this.changeDetector.detectChanges();
        }
      });
  }


  // =====================================================
  // INICIAR ATENDIMENTO
  // =====================================================

  iniciarAtendimento(): void {

    // Não existe senha
    if (!this.senhaAtual) {

      this.erro =
        'Não existe uma senha para iniciar o atendimento.';

      this.changeDetector.detectChanges();

      return;
    }


    // Limpa mensagens
    this.limparMensagens();

    // Inicia carregamento
    this.carregando = true;

    this.changeDetector.detectChanges();


    this.senhaService
      .iniciarAtendimento(
        this.senhaAtual.codigo
      )
      .subscribe({

        // =============================================
        // SUCESSO
        // =============================================

        next: () => {

          if (this.senhaAtual) {

            this.senhaAtual = {
              ...this.senhaAtual,
              status: 'em_atendimento'
            };
          }


          // Campos começam vazios
          // porque são opcionais
          this.nomeCliente = '';

          this.telefoneContacto = '';


          this.mensagem =
            'Atendimento iniciado com sucesso.';

          this.carregando = false;


          // Atualiza a interface
          this.changeDetector.detectChanges();
        },


        // =============================================
        // ERRO
        // =============================================

        error: (erro: HttpErrorResponse) => {
          console.log(
        'ERRO COMPLETO:',
        erro
      );

      console.log(
        'STATUS:',
        erro.status
      );

      console.log(
        'RESPOSTA DA API:',
        erro.error
      );

      console.log(
        'MENSAGEM DA API:',
        erro.error?.message
      );
      
          this.erro =
            erro?.error?.message ??
            'Não foi possível iniciar o atendimento.';

          this.carregando = false;

          this.changeDetector.detectChanges();
        }
      });
  }


  // =====================================================
  // FINALIZAR ATENDIMENTO
  // =====================================================

  finalizarAtendimento(): void {

    // Não existe senha
    if (!this.senhaAtual) {

      this.erro =
        'Não existe um atendimento para finalizar.';

      this.changeDetector.detectChanges();

      return;
    }


    // Limpa mensagens
    this.limparMensagens();

    // Inicia carregamento
    this.carregando = true;

    this.changeDetector.detectChanges();


    this.senhaService
      .finalizarAtendimento(
        this.senhaAtual.codigo,
        this.nomeCliente,
        this.telefoneContacto
      )
      .subscribe({

        // =============================================
        // SUCESSO
        // =============================================

        next: () => {

          if (this.senhaAtual) {

            this.senhaAtual = {
              ...this.senhaAtual,

              status: 'finalizada',

              nomeCliente:
                this.nomeCliente || null,

              telefoneContacto:
                this.telefoneContacto || null
            };
          }


          this.mensagem =
            'Atendimento finalizado com sucesso.';

          this.carregando = false;


          // Atualiza a interface
          this.changeDetector.detectChanges();
        },


        // =============================================
        // ERRO
        // =============================================

        error: (erro: HttpErrorResponse) => {

          this.erro =
            erro?.error?.message ??
            'Não foi possível finalizar o atendimento.';

          this.carregando = false;

          this.changeDetector.detectChanges();
        }
      });
  }


  // =====================================================
  // CONSULTAR SENHA
  // =====================================================

  consultarSenha(
    codigo: string
  ): void {

    // Limpa mensagens
    this.limparMensagens();

    // Remove espaços
    codigo =
      codigo.trim();

    // Atualiza interface
    this.changeDetector.detectChanges();


    // Código vazio
    if (!codigo) {

      this.erro =
        'Informe o código da senha.';

      this.changeDetector.detectChanges();

      return;
    }


    // Inicia carregamento
    this.carregando = true;

    this.changeDetector.detectChanges();


    this.senhaService
      .consultar(codigo)
      .subscribe({

        // =============================================
        // SUCESSO
        // =============================================

        next: (resposta) => {

          this.senhaAtual =
            resposta.data;

          this.nomeCliente =
            resposta.data.nomeCliente ?? '';

          this.telefoneContacto =
            resposta.data.telefoneContacto ?? '';

          this.mensagem =
            resposta.message;

          this.carregando = false;

          this.changeDetector.detectChanges();
        },


        // =============================================
        // ERRO
        // =============================================

        error: (erro: HttpErrorResponse) => {

          this.erro =
            erro?.error?.message ??
            'Não foi possível consultar a senha.';

          this.carregando = false;

          this.changeDetector.detectChanges();
        }
      });
  }


  // =====================================================
  // MENSAGENS
  // =====================================================

  private limparMensagens(): void {

    this.mensagem = null;

    this.erro = null;

    this.changeDetector.detectChanges();
  }


  // =====================================================
  // SESSÃO
  // =====================================================

  sair(): void {

    this.authService.removerToken();

    this.changeDetector.detectChanges();

    this.router.navigate([
      '/login'
    ]);
  }
}