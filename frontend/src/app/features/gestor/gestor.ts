import {
  Component,
  inject
} from '@angular/core';

import {
  Router
} from '@angular/router';

import {
  AuthService
} from '../../core/services/auth.service';


@Component({
  selector: 'app-gestor',
  imports: [],
  templateUrl: './gestor.html',
  styleUrl: './gestor.scss'
})

// Componente responsável pela área de gestão
export class Gestor {

  // Disponibiliza o Router para navegação
  private readonly router = inject(
    Router
  );

  // Disponibiliza o serviço de autenticação
  private readonly authService = inject(
    AuthService
  );


  // Volta para o Dashboard
  voltarDashboard(): void {

    // Navega para o Dashboard
    this.router.navigate([
      '/dashboard'
    ]);
  }


  // Abre a área de gestão dos utilizadores
  gerirUtilizadores(): void {

    // Navega para a gestão de utilizadores
    this.router.navigate([
      '/gestor/utilizadores'
    ]);
  }


  // Abre a área de gestão dos balcões
  gerirBalcoes(): void {

    // Navega para a gestão de balcões
    this.router.navigate([
      '/gestor/balcoes'
    ]);
  }


  // Abre a área de gestão dos tipos de atendimento
  gerirTiposAtendimento(): void {

    // Navega para a gestão dos tipos de atendimento
    this.router.navigate([
      '/gestor/tipos-atendimento'
    ]);
  }


  // Encerra a sessão do utilizador
  sair(): void {

    // Remove o JWT do navegador
    this.authService.removerToken();

    // Redireciona para o Login
    this.router.navigate([
      '/login'
    ]);
  }
}