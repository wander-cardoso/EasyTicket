import {
  Injectable,
  inject
} from '@angular/core';

import {
  HttpClient
} from '@angular/common/http';

import {
  Observable
} from 'rxjs';

import {
  ApiResponse
} from '../../../shared/interfaces/api-response.interface';

import {
  Senha
} from '../../../shared/models/senha.model';


@Injectable({
  providedIn: 'root'
})

// Centraliza a comunicação da aplicação com a API de Senhas
export class SenhaService {

  private readonly http = inject(HttpClient);

  private readonly apiUrl =
    'http://localhost:8000/api/senhas';


  // Consulta uma senha através do código
  consultar(
    codigo: string
  ): Observable<ApiResponse<Senha>> {

    return this.http.get<ApiResponse<Senha>>(
      `${this.apiUrl}/${codigo}`
    );
  }


  // Emite uma nova senha
  emitirSenha(
    tipoAtendimentoId: number
  ): Observable<ApiResponse<Senha>> {

    return this.http.post<ApiResponse<Senha>>(
      this.apiUrl,
      {
        tipoAtendimentoId
      }
    );
  }


  // Chama a próxima senha
  chamarProxima(): Observable<ApiResponse<Senha>> {

    return this.http.post<ApiResponse<Senha>>(
      `${this.apiUrl}/chamar-proxima`,
      {}
    );
  }


  // Inicia o atendimento
  iniciarAtendimento(
    codigo: string
  ): Observable<ApiResponse<boolean>> {

    return this.http.post<ApiResponse<boolean>>(
      `${this.apiUrl}/iniciar-atendimento`,
      {
        codigo
      }
    );
  }


  // Finaliza o atendimento
  finalizarAtendimento(
    codigo: string,
    nomeCliente: string,
    telefoneContacto: string
  ): Observable<ApiResponse<boolean>> {

    return this.http.post<ApiResponse<boolean>>(
      `${this.apiUrl}/finalizar-atendimento`,
      {
        codigo,
        nomeCliente,
        telefoneContacto
      }
    );
  }
}