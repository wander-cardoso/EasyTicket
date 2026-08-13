import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

import { AuthService } from '../../core/services/auth.service';


// Impede utilizadores autenticados de acessar páginas destinadas
// apenas a utilizadores não autenticados, como o Login
export const guestGuard: CanActivateFn = () => {

  // Disponibiliza o serviço de autenticação
  const authService = inject(AuthService);

  // Disponibiliza o Router para redirecionamento
  const router = inject(Router);


  // Verifica se já existe um JWT armazenado
  if (authService.estaAutenticado()) {

    // Utilizador já está autenticado:
    // não precisa voltar para o Login
    return router.createUrlTree([
      '/dashboard'
    ]);
  }


  // Utilizador ainda não está autenticado:
  // permite acessar o Login
  return true;
};