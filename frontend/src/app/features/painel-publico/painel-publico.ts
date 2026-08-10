import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { TipoAtendimento } from '../../shared/models/tipo-atendimento.model';
import { Senha } from '../../shared/models/senha.model';
import { Balcao } from '../../shared/models/balcao.model';
import { TipoAtendimentoService } from '../tipos-atendimento/services/tipo-atendimento.service';
import { SenhaService } from '../senhas/services/senha.service';
import { BalcaoService } from '../balcoes/services/balcao.service';

@Component({
  selector: 'app-painel-publico',
  imports: [FormsModule],
  templateUrl: './painel-publico.html',
  styleUrl: './painel-publico.scss',
})
export class PainelPublico implements OnInit {
  // Service responsável por consultar os tipos de atendimento na API
  private readonly tipoAtendimentoService = inject(TipoAtendimentoService);

  // Service responsável pelas operações relacionadas às senhas
  private readonly senhaService = inject(SenhaService);

  // Service responsável por consultar os balcões na API
  private readonly balcaoService = inject(BalcaoService);

  // Permite solicitar a atualização da interface após uma resposta assíncrona
  private readonly changeDetector = inject(ChangeDetectorRef);

  // Guarda os tipos de atendimento disponíveis
  tiposAtendimento: TipoAtendimento[] = [];

  // Indica se os tipos de atendimento ainda estão sendo carregados
  carregandoTiposAtendimento = true;

  // Guarda o tipo de atendimento selecionado
  tipoAtendimentoSelecionado: TipoAtendimento | null = null;

  // Guarda a senha que acabou de ser emitida
  senhaEmitida: Senha | null = null;

  // Guarda a senha encontrada na consulta
  senhaConsultada: Senha | null = null;

  // Guarda o código digitado pelo utilizador
  codigoConsulta = '';

  // Controla a exibição da tela de consulta
  mostrarConsulta = false;

  // Guarda a mensagem de validação da consulta
  erroConsulta = '';

  // Guarda os balcões disponíveis
  balcoes: Balcao[] = [];

  // Guarda o temporizador responsável por voltar à tela inicial
  private temporizadorRetorno: ReturnType<typeof setTimeout> | null = null;

  // Executado automaticamente quando o componente é inicializado
  ngOnInit(): void {
    this.carregarTiposAtendimento();
    this.carregarBalcoes();
  }

  // Busca os tipos de atendimento disponíveis no backend
  private carregarTiposAtendimento(): void {
    this.tipoAtendimentoService.listar().subscribe({
      next: (resposta) => {
        // Guarda os tipos de atendimento recebidos
        this.tiposAtendimento = resposta.data;

        // Finaliza o carregamento dos tipos
        this.carregandoTiposAtendimento = false;

        // Atualiza a interface com os novos valores
        this.changeDetector.detectChanges();
      },
      error: (erro) => {
        // Registra o erro ocorrido na consulta
        console.error('Erro ao carregar tipos de atendimento:', erro);

        // Finaliza o carregamento mesmo quando ocorre erro
        this.carregandoTiposAtendimento = false;

        // Atualiza a interface após o erro
        this.changeDetector.detectChanges();
      },
    });
  }

  // Seleciona o tipo de atendimento escolhido pelo utilizador
  selecionarTipoAtendimento(tipo: TipoAtendimento): void {
    this.tipoAtendimentoSelecionado = tipo;
  }

  // Emite uma nova senha para o tipo de atendimento selecionado
  emitirSenha(): void {
    if (this.tipoAtendimentoSelecionado === null) {
      console.error('Selecione um tipo de atendimento.');
      return;
    }

    this.senhaService.emitirSenha(this.tipoAtendimentoSelecionado.id).subscribe({
      next: (resposta) => {
        // Guarda a senha devolvida pela API
        this.senhaEmitida = resposta.data;

        // Atualiza imediatamente a interface
        this.changeDetector.detectChanges();
        // Inicia o retorno automático para a tela inicial
        this.iniciarTemporizadorRetorno();
      },
      error: (erro) => {
        console.error('Erro ao emitir senha:', erro);
      },
    });
  }

  // Inicia o temporizador de 10 segundos
  private iniciarTemporizadorRetorno(): void {
    this.limparTemporizador();

    this.temporizadorRetorno = setTimeout(() => {
      this.voltarTelaInicial();
    }, 10000);
  }

  // Cancela o temporizador atual
  private limparTemporizador(): void {
    if (this.temporizadorRetorno !== null) {
      clearTimeout(this.temporizadorRetorno);
      this.temporizadorRetorno = null;
    }
  }

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

  // Abre a tela de consulta de senha
  abrirConsulta(): void {
    this.limparTemporizador();
    this.senhaEmitida = null;
    this.tipoAtendimentoSelecionado = null;
    this.senhaConsultada = null;
    this.codigoConsulta = '';
    this.erroConsulta = '';
    this.mostrarConsulta = true;
  }

  // Retorna o nome do tipo de atendimento pelo ID
  obterNomeTipoAtendimento(tipoAtendimentoId: number): string {
    const tipo = this.tiposAtendimento.find((tipo) => tipo.id === tipoAtendimentoId);

    return tipo?.nome ?? 'Tipo de atendimento não encontrado';
  }

  // Busca os balcões disponíveis no backend
  private carregarBalcoes(): void {
    this.balcaoService.listar().subscribe({
      next: (resposta) => {
        this.balcoes = resposta.data;
      },
      error: (erro) => {
        console.error('Erro ao carregar balcões:', erro);
      },
    });
  }

  // Retorna o balcão correspondente ao ID da senha
  obterBalcao(balcaoId: number | null): Balcao | null {
    if (balcaoId === null) {
      return null;
    }

    return this.balcoes.find((balcao) => balcao.id === balcaoId) ?? null;
  }

  // Busca uma senha pelo código informado
  buscarSenha(): void {
    this.erroConsulta = '';
    this.senhaConsultada = null;
    this.changeDetector.detectChanges();
    if (this.codigoConsulta.trim() === '') {
      this.erroConsulta = 'Digite o código de uma senha.';
      this.changeDetector.detectChanges();
      return;
    }

    this.senhaService.consultarPorCodigo(this.codigoConsulta.trim()).subscribe({
      next: (resposta) => {
        // Guarda a senha encontrada
        this.senhaConsultada = resposta.data;
        // Atualiza imediatamente a interface
        this.changeDetector.detectChanges();
      },
      error: (erro) => {
        this.erroConsulta = 'Senha não encontrada.';
        // Atualiza a interface após alterar o estado
        this.changeDetector.detectChanges();
        console.error('Erro ao consultar senha:', erro);
      },
    });
  }
}
