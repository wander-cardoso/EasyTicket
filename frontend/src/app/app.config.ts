import {
  ApplicationConfig,
  provideBrowserGlobalErrorListeners
} from '@angular/core';

import { provideRouter } from '@angular/router';

import { routes } from './app.routes';

import {
  provideHttpClient,
  withInterceptors
} from '@angular/common/http';

import { authInterceptor } from './auth/interceptors/auth.interceptor';


export const appConfig: ApplicationConfig = {

  providers: [

    // Permite que o Angular trate erros globais do navegador
    provideBrowserGlobalErrorListeners(),

    // Registra as rotas da aplicação
    provideRouter(routes),

    // Disponibiliza o HttpClient e registra o interceptor JWT
    provideHttpClient(
      withInterceptors([
        authInterceptor
      ])
    )
  ]
};