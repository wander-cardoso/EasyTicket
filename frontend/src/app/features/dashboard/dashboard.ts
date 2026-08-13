import { Component, OnInit, inject } from '@angular/core';

import { DashboardService } from '../../core/services/dashboard.service';

import { AuthService } from '../../core/services/auth.service';

import { Dashboard as DashboardModel } from '../../shared/models/dashboard.model';
import { Balcao } from '../../shared/models/balcao.model';
import { BalcaoService } from '../balcoes/services/balcao.service';

@Component({
  selector: 'app-dashboard',
  imports: [],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard implements OnInit {

  // Disponibiliza o serviço responsável pelo Dashboard
  private readonly dashboardService = inject(
    DashboardService
  );

  // Disponibiliza o serviço responsável pelos balcões
  private readonly balcaoService = inject(
    BalcaoService
  );

  // Disponibiliza o serviço responsável pelo JWT
  private readonly authService = inject(
    AuthService
  );


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

    this.carregando = true;
    this.erro = null;

    this.dashboardService.obter().subscribe({

      // Executado quando a API responde com sucesso
      next: (resposta) => {

        // Guarda os dados recebidos da API
        this.dados = resposta.data;

        // Finaliza o carregamento
        this.carregando = false;
      },

      // Executado quando ocorre algum erro
      error: () => {

        // Informa o utilizador sobre o erro
        this.erro =
          'Não foi possível carregar os dados do Dashboard.';

        this.carregando = false;
      }
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

    this.carregandoBalcoes = true;

    this.balcaoService.listar().subscribe({

      // Executado quando a API responde com sucesso
      next: (resposta) => {

        // Guarda a lista de balcões recebida
        this.balcoes = resposta.data;

        // Finaliza o carregamento
        this.carregandoBalcoes = false;
      },

      // Executado quando ocorre algum erro
      error: () => {

        // Informa o utilizador sobre o erro
        this.erroBalcao =
          'Não foi possível carregar os balcões.';

        this.carregandoBalcoes = false;
      }
    });
  }


  // Seleciona um balcão para o utilizador autenticado
  selecionarBalcao(balcaoId: number): void {

    // Limpa eventual erro anterior
    this.erroBalcao = null;

    this.balcaoService.selecionar(balcaoId).subscribe({

      // Executado quando o balcão foi selecionado
      next: (resposta) => {

        // Guarda o novo JWT no navegador
        this.authService.salvarToken(
          resposta.data.token
        );

        // Fecha o seletor
        this.mostrandoBalcoes = false;

        // Atualiza o Dashboard utilizando o novo JWT
        this.carregarDashboard();
      },

      // Executado quando ocorre algum erro
      error: () => {

        // Informa o utilizador sobre o erro
        this.erroBalcao =
          'Não foi possível selecionar o balcão.';
      }
    });
  }
}