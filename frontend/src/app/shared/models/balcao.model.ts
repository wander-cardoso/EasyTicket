export interface Balcao {
  id: number;
  numero: number;
  nome: string;
  tipoAtendimentoId: number;
}

// Representa os dados retornados quando um balcão é selecionado
export interface SelecaoBalcaoResponse {

  // Novo JWT gerado pelo backend
  token: string;

  // Dados do balcão selecionado
  balcao: {
    id: number;
    nome: string;
    numero: number;
  };
}