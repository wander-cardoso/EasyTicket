import { Routes } from '@angular/router';

import { PainelPublico } from './features/painel-publico/painel-publico';
import { Operador } from './features/operador/operador';
import { Gestor } from './features/gestor/gestor';
import { BalcaoAtendimento } from './features/balcao-atendimento/balcao-atendimento';
import { Dashboard } from './features/dashboard/dashboard';
import { Login } from './features/login/login';

import { authGuard } from './auth/guards/auth.guard';
import { guestGuard } from './auth/guards/guest.guard';
import { profileGuard } from './auth/guards/profile.guard';


export const routes: Routes = [

  // Página pública
  {
    path: '',
    component: PainelPublico
  },


  // Login disponível apenas para utilizadores não autenticados
  {
    path: 'login',
    component: Login,
    canActivate: [
      guestGuard
    ]
  },


  // Dashboard disponível para qualquer utilizador autenticado
  {
    path: 'dashboard',
    component: Dashboard,
    canActivate: [
      authGuard
    ]
  },


  // Área exclusiva do operador
  {
    path: 'operador',
    component: Operador,
    canActivate: [
      authGuard,
      profileGuard
    ],
    data: {
      perfisPermitidos: [
        'OPERADOR'
      ]
    }
  },


  // Área exclusiva do gestor
  {
    path: 'gestor',
    component: Gestor,
    canActivate: [
      authGuard,
      profileGuard
    ],
    data: {
      perfisPermitidos: [
        'GESTOR'
      ]
    }
  },


  // Área exclusiva do balcão
  {
    path: 'balcaoAtendimento',
    component: BalcaoAtendimento,
    canActivate: [
      authGuard,
      profileGuard
    ],
    data: {
      perfisPermitidos: [
        'BALCAO'
      ]
    }
  },


  // Qualquer rota desconhecida volta para a página inicial
  {
    path: '**',
    redirectTo: ''
  }
];