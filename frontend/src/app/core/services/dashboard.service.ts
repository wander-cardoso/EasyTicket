import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { Dashboard } from '../../shared/models/dashboard.model';
import { ApiResponse } from '../../shared/interfaces/api-response.interface';

@Injectable({
  providedIn: 'root'
})

// Responsável pela comunicação com a API do Dashboard
export class DashboardService {

  // Disponibiliza o HttpClient para realizar requisições HTTP
  private readonly http = inject(HttpClient);

  // Endpoint responsável pelos dados do Dashboard
  private readonly apiUrl =
    'http://localhost:8000/api/me/dashboard';


  // Obtém os dados do Dashboard do utilizador autenticado
  obter(): Observable<ApiResponse<Dashboard>> {

    // Faz a requisição para o backend
    return this.http.get<ApiResponse<Dashboard>>(
      this.apiUrl
    );
  }
}