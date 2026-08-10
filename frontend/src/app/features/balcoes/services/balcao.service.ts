import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { Balcao } from '../../../shared/models/balcao.model';
import { ApiResponse } from '../../../shared/interfaces/api-response.interface';

@Injectable({
  providedIn: 'root',
})
export class BalcaoService {
  // Disponibiliza o HttpClient para realizar requisições HTTP
  private readonly http = inject(HttpClient);

  // Endereço da API responsável pelos balcões
  private readonly apiUrl = 'http://localhost:8000/api/balcoes';

  // Lista os balcões disponíveis
  listar(): Observable<ApiResponse<Balcao[]>> {
    return this.http.get<ApiResponse<Balcao[]>>(this.apiUrl);
  }
}
