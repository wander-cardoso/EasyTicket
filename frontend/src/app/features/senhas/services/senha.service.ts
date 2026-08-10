import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { Senha } from '../../../shared/models/senha.model';
import { ApiResponse } from '../../../shared/interfaces/api-response.interface';

@Injectable({ providedIn: 'root' })
export class SenhaService {
  // Disponibiliza o HttpClient para realizar requisições HTTP
  private readonly http = inject(HttpClient);

  // Endereço da API responsável pelas senhas
  private readonly apiUrl = 'http://localhost:8000/api/senhas';

  // Emite uma nova senha através da API
  emitirSenha(
    tipoAtendimentoId: number,
    nomeCliente?: string,
    telefoneContacto?: string,
  ): Observable<ApiResponse<Senha>> {
    const dados = { tipoAtendimentoId, nomeCliente, telefoneContacto };

    return this.http.post<ApiResponse<Senha>>(this.apiUrl, dados);
  }

  // Consulta uma senha pelo código
  consultarPorCodigo(codigo: string): Observable<ApiResponse<Senha>> {
    return this.http.get<ApiResponse<Senha>>(`${this.apiUrl}/${codigo}`);
  }

  // Chama a próxima senha para um balcão
  chamarProxima(balcaoId: number): Observable<ApiResponse<Senha>> {
    return this.http.post<ApiResponse<Senha>>(`${this.apiUrl}/chamar-proxima`, { balcaoId });
  }

  // Inicia o atendimento de uma senha
  iniciarAtendimento(codigo: string): Observable<ApiResponse<Senha>> {
    return this.http.post<ApiResponse<Senha>>(`${this.apiUrl}/iniciar-atendimento`, { codigo });
  }

  // Finaliza o atendimento de uma senha
  finalizarAtendimento(codigo: string): Observable<ApiResponse<Senha>> {
    return this.http.post<ApiResponse<Senha>>(`${this.apiUrl}/finalizar-atendimento`, { codigo });
  }
}
