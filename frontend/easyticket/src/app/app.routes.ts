import { Routes } from '@angular/router';
import { PainelPublico } from './features/painel-publico/painel-publico';
import { Operador } from './features/operador/operador';
import { Gestor } from './features/gestor/gestor';
import { BalcaoAtendimento } from './features/balcao-atendimento/balcao-atendimento';

export const routes: Routes = [
    {
        path: '',
        component: PainelPublico
    },
    {
        path: 'balcaoAtendimento',
        component: BalcaoAtendimento
    },
    {
        path: 'operador',
        component: Operador
    },
    {
        path: 'gestor',
        component: Gestor
    },
    {
        path: '*',
        redirectTo: ''
    }

];
