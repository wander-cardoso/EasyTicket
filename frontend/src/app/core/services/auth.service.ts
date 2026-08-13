import { Injectable } from '@angular/core';
import { JwtPayload } from '../../shared/interfaces/jwt-payload.interface';


@Injectable({
  providedIn: 'root'
})

// Centraliza o acesso e gerenciamento do token de autenticação
export class AuthService {

  // Nome utilizado para armazenar o JWT no navegador
  private readonly chaveToken = 'token';


  // Salva o JWT recebido pela API
  salvarToken(token: string): void {

    localStorage.setItem(
      this.chaveToken,
      token
    );
  }


  // Recupera o JWT armazenado
  obterToken(): string | null {

    return localStorage.getItem(
      this.chaveToken
    );
  }


  // Remove o JWT armazenado
  removerToken(): void {

    localStorage.removeItem(
      this.chaveToken
    );
  }


  // Verifica apenas se existe um token armazenado
  estaAutenticado(): boolean {

    return this.obterToken() !== null;
  }


  // Lê e converte o payload do JWT
  obterPayload(): JwtPayload | null {

    // Recupera o token armazenado
    const token = this.obterToken();

    // Se não existir token, não existe payload
    if (!token) {
      return null;
    }

    try {

      // Um JWT possui três partes:
      // header.payload.signature
      const partes = token.split('.');

      // Verifica a estrutura básica do JWT
      if (partes.length !== 3) {
        return null;
      }

      // Obtém somente a parte correspondente ao payload
      const payloadBase64 = partes[1];

      // Converte Base64URL para Base64 convencional
      const payloadBase64Normalizado = payloadBase64
        .replace(/-/g, '+')
        .replace(/_/g, '/');

      // Decodifica o Base64
      const payloadJson = atob(
        payloadBase64Normalizado
      );

      // Converte o JSON para objeto JavaScript
      return JSON.parse(payloadJson) as JwtPayload;

    } catch {

      // Retorna null caso o token esteja malformado
      return null;
    }
  }


  // Obtém o ID do utilizador autenticado
  obterUtilizadorId(): number | null {

    // Obtém o payload do JWT
    const payload = this.obterPayload();

    // Verifica se o ID existe
    if (!payload) {
      return null;
    }

    // Retorna o ID do utilizador
    return payload.sub;
  }


  // Obtém o perfil do utilizador autenticado
  obterPerfil(): string | null {

    // Obtém o payload do JWT
    const payload = this.obterPayload();

    // Verifica se o payload existe
    if (!payload) {
      return null;
    }

    // Retorna o perfil do utilizador
    return payload.perfil;
  }


  // Obtém o ID do balcão atualmente selecionado
  obterBalcaoId(): number | null {

    // Obtém o payload do JWT
    const payload = this.obterPayload();

    // Se não existir payload ou balcão, retorna null
    if (!payload || payload.balcaoId === undefined) {
      return null;
    }

    // Retorna o ID do balcão
    return payload.balcaoId;
  }
}