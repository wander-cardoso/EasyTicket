import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

import { TipoAtendimento } from '../../../shared/models/tipo-atendimento.model';
import { ApiResponse } from '../../../shared/interfaces/api-response.interface';

@Injectable({
  providedIn: 'root'
})
export class TipoAtendimentoService {

  private readonly http = inject(HttpClient);

  private readonly apiUrl =
    'http://localhost:8000/api/tipos-atendimento';

  listar(): Observable<ApiResponse<TipoAtendimento[]>> {
    return this.http.get<ApiResponse<TipoAtendimento[]>>(
      this.apiUrl
    );
  }
}