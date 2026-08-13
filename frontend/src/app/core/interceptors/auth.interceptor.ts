import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';

import { AuthService } from '../services/auth.service';


// Intercepta todas as requisições HTTP do Angular
export const authInterceptor: HttpInterceptorFn = (req, next) => {

  // Obtém o serviço responsável pelo armazenamento do JWT
  const authService = inject(AuthService);

  // Obtém o token armazenado no Local Storage
  const token = authService.obterToken();

  // Se não existir token, envia a requisição sem autenticação
  if (!token) {
    return next(req);
  }

  // Cria uma cópia da requisição adicionando o JWT
  const requisicaoAutenticada = req.clone({
    setHeaders: {
      Authorization: `Bearer ${token}`
    }
  });

  // Envia a requisição com o cabeçalho Authorization
  return next(requisicaoAutenticada);
};