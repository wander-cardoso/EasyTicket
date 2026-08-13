import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';

import { DashboardService } from '../../core/services/dashboard.service';
import { AuthService } from '../../core/services/auth.service';

import { Dashboard as DashboardModel } from '../../shared/models/dashboard.model';
import { Balcao } from '../../shared/models/balcao.model';
import { BalcaoService } from '../balcoes/services/balcao.service';

import { Router } from '@angular/router';
@Component({
  selector: 'app-dashboard',
  imports: [],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard implements OnInit {
  // Disponibiliza o Router para navegação
  private readonly router = inject(Router);

  // Disponibiliza o serviço responsável pelo Dashboard
  private readonly dashboardService = inject(DashboardService);

  // Disponibiliza o serviço responsável pelos balcões
  private readonly balcaoService = inject(BalcaoService);

  // Disponibiliza o serviço responsável pelo JWT
  private readonly authService = inject(AuthService);

  // Permite solicitar manualmente a atualização da interface
  private readonly changeDetectorRef = inject(ChangeDetectorRef);

  // Dados apresentados no Dashboard
  dados: DashboardModel | null = null;

  // Lista de balcões disponíveis para seleção
  balcoes: Balcao[] = [];

  // Controla o carregamento do Dashboard
  carregando = true;

  // Controla o carregamento da lista de balcões
  carregandoBalcoes = false;

  // Indica se o seletor de balcão está aberto
  mostrandoBalcoes = false;

  // Mensagem de erro do Dashboard
  erro: string | null = null;

  // Mensagem de erro relacionada aos balcões
  erroBalcao: string | null = null;

  // Executado quando o componente é inicializado
  ngOnInit(): void {
    // Carrega os dados do Dashboard
    this.carregarDashboard();
  }

  // Obtém os dados do utilizador autenticado
  private carregarDashboard(): void {
    // Ativa o estado de carregamento
    this.carregando = true;

    // Limpa qualquer erro anterior
    this.erro = null;

    console.log('1. Iniciando requisição do Dashboard...');

    // Solicita os dados do Dashboard ao backend
    this.dashboardService.obter().subscribe({
      // Executado quando a API responde com sucesso
      next: (resposta) => {
        console.log('2. Resposta recebida da API:', resposta);

        // Guarda os dados recebidos da API
        this.dados = resposta.data;

        console.log('3. Dados armazenados no componente:', this.dados);

        // Finaliza o estado de carregamento
        this.carregando = false;

        console.log('4. Carregamento finalizado:', this.carregando);

        // Solicita ao Angular uma nova verificação da interface
        this.changeDetectorRef.detectChanges();
      },

      // Executado quando ocorre algum erro HTTP
      error: (erro) => {
        // Registra o erro no Console
        console.error('ERRO ao carregar Dashboard:', erro);

        // Exibe uma mensagem de erro
        this.erro = 'Não foi possível carregar os dados do Dashboard.';

        // Finaliza o estado de carregamento
        this.carregando = false;

        // Solicita a atualização da interface
        this.changeDetectorRef.detectChanges();
      },

      // Executado quando a requisição termina
      complete: () => {
        console.log('5. Requisição do Dashboard concluída.');
      },
    });
  }

  // Abre o seletor de balcões
  abrirSelecaoBalcao(): void {
    // Limpa eventual erro anterior
    this.erroBalcao = null;

    // Abre a área de seleção
    this.mostrandoBalcoes = true;

    // Busca os balcões disponíveis
    this.carregarBalcoes();
  }

  // Obtém os balcões disponíveis na API
  private carregarBalcoes(): void {
    // Ativa o estado de carregamento dos balcões
    this.carregandoBalcoes = true;

    // Solicita os balcões ao backend
    this.balcaoService.listar().subscribe({
      // Executado quando a API responde com sucesso
      next: (resposta) => {
        // Guarda a lista de balcões recebida
        this.balcoes = resposta.data;

        // Finaliza o carregamento dos balcões
        this.carregandoBalcoes = false;
      },

      // Executado quando ocorre algum erro
      error: (erro) => {
        // Registra o erro no Console
        console.error('ERRO ao carregar balcões:', erro);

        // Informa o utilizador sobre o erro
        this.erroBalcao = 'Não foi possível carregar os balcões.';

        // Finaliza o carregamento dos balcões
        this.carregandoBalcoes = false;
      },
    });
  }

  // Seleciona um balcão para o utilizador autenticado
  selecionarBalcao(balcaoId: number): void {
    // Limpa eventual erro anterior
    this.erroBalcao = null;

    // Envia a seleção para o backend
    this.balcaoService.selecionar(balcaoId).subscribe({
      // Executado quando o balcão foi selecionado
      next: (resposta) => {
        // Guarda o novo JWT no navegador
        this.authService.salvarToken(resposta.data.token);

        // Fecha o seletor de balcões
        this.mostrandoBalcoes = false;

        // Recarrega o Dashboard utilizando o novo JWT
        this.carregarDashboard();
      },

      // Executado quando ocorre algum erro
      error: (erro) => {
        // Registra o erro no Console
        console.error('ERRO ao selecionar balcão:', erro);

        // Informa o utilizador sobre o erro
        this.erroBalcao = 'Não foi possível selecionar o balcão.';
      },
    });
  }

  // Encerra a sessão do utilizador
  sair(): void {
    // Remove o JWT armazenado no navegador
    this.authService.removerToken();

    // Redireciona o utilizador para o Login
    this.router.navigate(['/login']);
  }
}