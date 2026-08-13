import { inject } from '@angular/core';
import {
  ActivatedRouteSnapshot,
  CanActivateFn,
  Router
} from '@angular/router';

import { AuthService } from '../../core/services/auth.service';


// Verifica se o utilizador possui o perfil necessário
// para acessar determinada rota
export const profileGuard: CanActivateFn = (
  route: ActivatedRouteSnapshot
) => {

  // Disponibiliza o serviço responsável pela autenticação
  const authService = inject(
    AuthService
  );

  // Disponibiliza o Router para redirecionamentos
  const router = inject(
    Router
  );


  // Obtém o perfil do utilizador armazenado no JWT
  const perfil = authService.obterPerfil();


  // Obtém os perfis permitidos configurados na rota
  const perfisPermitidos =
    route.data['perfisPermitidos'] as string[] | undefined;


  // Verifica se a rota não definiu perfis específicos
  if (!perfisPermitidos || perfisPermitidos.length === 0) {

    // Não existe restrição de perfil
    return true;
  }


  // Verifica se o perfil do utilizador está autorizado
  if (
    perfil !== null &&
    perfisPermitidos.includes(perfil)
  ) {

    // O perfil possui autorização
    return true;
  }


  // O utilizador está autenticado,
  // mas não possui autorização para esta área
  return router.createUrlTree([
    '/dashboard'
  ]);
};