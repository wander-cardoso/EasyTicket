import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

import { AuthService } from '../../core/services/auth.service';


@Component({
  selector: 'app-login',

  // Permite utilizar [(ngModel)] no formulário
  imports: [
    FormsModule
  ],

  templateUrl: './login.html',
  styleUrl: './login.scss'
})

// Componente responsável pela tela de login
export class Login {

  // Disponibiliza o serviço de autenticação
  private readonly authService = inject(AuthService);

  // Permite redirecionar o utilizador após o login
  private readonly router = inject(Router);


  // Campo do nome de utilizador
  nomeUtilizador = '';

  // Campo da password
  password = '';

  // Indica se o login está sendo processado
  carregando = false;

  // Armazena uma mensagem de erro para a interface
  erro: string | null = null;


  // Executa o login
  entrar(): void {

    // Limpa uma mensagem de erro anterior
    this.erro = null;


    // Verifica se o nome de utilizador foi informado
    if (!this.nomeUtilizador.trim()) {

      this.erro =
        'Informe o nome de utilizador.';

      return;
    }


    // Verifica se a password foi informada
    if (!this.password) {

      this.erro =
        'Informe a password.';

      return;
    }


    // Indica que o login está sendo processado
    this.carregando = true;


    // Envia as credenciais para o backend
    this.authService.login(
      this.nomeUtilizador,
      this.password
    ).subscribe({

      // Executado quando o login foi realizado com sucesso
      next: () => {

        // Finaliza o estado de carregamento
        this.carregando = false;

        // Redireciona o utilizador para o Dashboard
        this.router.navigate([
          '/dashboard'
        ]);
      },


      // Executado quando o backend retorna um erro
      error: (resposta) => {

        // Finaliza o estado de carregamento
        this.carregando = false;

        // Obtém a mensagem devolvida pela API
        this.erro =
          resposta?.error?.message ??
          'Não foi possível realizar o login.';
      }

    });
  }
}