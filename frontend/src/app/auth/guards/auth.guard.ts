import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';

import { AuthService } from '../../core/services/auth.service';


// Protege as rotas que exigem autenticação
export const authGuard: CanActivateFn = () => {

  // Disponibiliza o serviço de autenticação
  const authService = inject(AuthService);

  // Disponibiliza o Router para redirecionamento
  const router = inject(Router);


  // Verifica se existe um JWT armazenado
  if (authService.estaAutenticado()) {

    // Utilizador autenticado:
    // permite continuar para a rota solicitada
    return true;
  }


  // Utilizador não autenticado:
  // redireciona para a página de login
  return router.createUrlTree([
    '/login'
  ]);
};