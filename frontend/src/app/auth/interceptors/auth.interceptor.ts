import {
  HttpInterceptorFn
} from '@angular/common/http';

import {
  inject
} from '@angular/core';

import {
  Router
} from '@angular/router';

import {
  catchError,
  throwError
} from 'rxjs';

import {
  AuthService
} from '../../core/services/auth.service';


// Intercepta todas as requisições HTTP do Angular
export const authInterceptor: HttpInterceptorFn = (
  req,
  next
) => {

  // Disponibiliza o serviço responsável pela autenticação
  const authService = inject(
    AuthService
  );

  // Disponibiliza o Router para redirecionamento
  const router = inject(
    Router
  );


  // Obtém o JWT armazenado no navegador
  const token = authService.obterToken();


  // Cria a requisição que será enviada para a API
  let requisicao = req;


  // Verifica se existe um JWT
  if (token) {

    // Cria uma cópia da requisição adicionando
    // o token no cabeçalho Authorization
    requisicao = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
  }


  // Envia a requisição para a API
  return next(requisicao).pipe(

    // Intercepta possíveis erros HTTP
    catchError((erro) => {

      // Verifica se o backend informou que a autenticação
      // não é mais válida
      if (erro.status === 401) {

        // Remove o JWT inválido ou expirado
        authService.removerToken();

        // Redireciona o utilizador para o Login
        router.navigate([
          '/login'
        ]);
      }


      // Devolve o erro para quem realizou a requisição
      return throwError(() => erro);
    })
  );
};