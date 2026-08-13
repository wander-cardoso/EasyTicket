import {
  Injectable,
  inject
} from '@angular/core';

import {
  HttpClient
} from '@angular/common/http';

import {
  Observable,
  tap
} from 'rxjs';

import {
  ApiResponse
} from '../../shared/interfaces/api-response.interface';

import {
  LoginResponse
} from '../../shared/interfaces/login-response.interface';


@Injectable({
  providedIn: 'root'
})

// Centraliza a autenticação e o gerenciamento do JWT
export class AuthService {

  // Disponibiliza o HttpClient
  private readonly http = inject(
    HttpClient
  );


  // Endereço da API de autenticação
  private readonly apiUrl =
    'http://localhost:8000/api/login';


  // Nome utilizado para armazenar o JWT
  private readonly chaveToken =
    'token';


  // Realiza o login
  login(
    nomeUtilizador: string,
    password: string
  ): Observable<ApiResponse<LoginResponse>> {

    return this.http.post<ApiResponse<LoginResponse>>(
      this.apiUrl,
      {
        nomeUtilizador,
        password
      }
    ).pipe(

      tap((resposta) => {

        this.salvarToken(
          resposta.data.token
        );

      })
    );
  }


  // Guarda o JWT
  salvarToken(token: string): void {

    localStorage.setItem(
      this.chaveToken,
      token
    );
  }


  // Obtém o JWT
  obterToken(): string | null {

    return localStorage.getItem(
      this.chaveToken
    );
  }


  // Remove o JWT
  removerToken(): void {

    localStorage.removeItem(
      this.chaveToken
    );
  }


  // Verifica se existe um JWT
  estaAutenticado(): boolean {

    return this.obterToken() !== null;
  }


  // Obtém o perfil armazenado no JWT
  obterPerfil(): string | null {

    const payload =
      this.obterPayload();


    if (!payload) {
      return null;
    }


    return payload['perfil'] ?? null;
  }


  // Obtém o ID do balcão armazenado no JWT
  obterBalcaoId(): number | null {

    const payload =
      this.obterPayload();


    if (!payload) {
      return null;
    }


    const balcaoId =
      payload['balcaoId'];


    if (
      balcaoId === null ||
      balcaoId === undefined
    ) {
      return null;
    }


    const numeroBalcaoId =
      Number(balcaoId);


    if (
      !Number.isInteger(numeroBalcaoId) ||
      numeroBalcaoId <= 0
    ) {
      return null;
    }


    return numeroBalcaoId;
  }


  // Obtém o Payload do JWT
  private obterPayload(): Record<string, any> | null {

    const token =
      this.obterToken();


    if (!token) {
      return null;
    }


    try {

      const partes =
        token.split('.');


      if (partes.length !== 3) {
        return null;
      }


      const payloadJson =
        atob(partes[1]);


      return JSON.parse(
        payloadJson
      );

    } catch {

      return null;
    }
  }
}