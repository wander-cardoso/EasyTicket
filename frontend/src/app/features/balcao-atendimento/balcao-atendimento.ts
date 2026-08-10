import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';
import { Balcao } from '../../shared/models/balcao.model';
import { Senha } from '../../shared/models/senha.model';
import { BalcaoService } from '../balcoes/services/balcao.service';
import { SenhaService } from '../senhas/services/senha.service';

@Component({
  selector: 'app-balcao-atendimento',
  imports: [],
  templateUrl: './balcao-atendimento.html',
  styleUrl: './balcao-atendimento.scss',
})
export class BalcaoAtendimento implements OnInit {

  // Service responsável pelas operações dos balcões
  private readonly balcaoService = inject(BalcaoService);

  // Service responsável pelas operações das senhas
  private readonly senhaService = inject(SenhaService);

  // Permite atualizar a interface após operações assíncronas
  private readonly changeDetector = inject(ChangeDetectorRef);

  // Guarda os balcões disponíveis
  balcoes: Balcao[] = [];

  // Guarda o balcão selecionado pelo operador
  balcaoSelecionado: Balcao | null = null;

  // Guarda a senha que está sendo atendida
  senhaAtual: Senha | null = null;

  // Indica se os balcões estão sendo carregados
  carregandoBalcoes = true;

  // Executado quando o componente é inicializado
  ngOnInit(): void {
    this.carregarBalcoes();
  }

  // Busca os balcões disponíveis no backend
  private carregarBalcoes(): void {
    this.balcaoService.listar().subscribe({
      next: (resposta) => {
        // Guarda os balcões recebidos
        this.balcoes = resposta.data;

        // Finaliza o carregamento
        this.carregandoBalcoes = false;

        // Atualiza a interface
        this.changeDetector.detectChanges();
      },
      error: (erro) => {
        // Registra o erro ocorrido
        console.error('Erro ao carregar balcões:', erro);

        // Finaliza o carregamento mesmo com erro
        this.carregandoBalcoes = false;

        // Atualiza a interface
        this.changeDetector.detectChanges();
      },
    });
  }

  // Seleciona o balcão que será utilizado pelo operador
  selecionarBalcao(balcao: Balcao): void {
    this.balcaoSelecionado = balcao;
  }
}