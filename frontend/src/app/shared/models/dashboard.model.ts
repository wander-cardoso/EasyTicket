// Representa os dados exibidos no Dashboard
export interface Dashboard {

  // Dados do utilizador autenticado
  utilizador: {
    id: number;
    nome: string;
    nomeUtilizador: string;
    perfil: string;
    criadoEm: string;
  };

  // Balcão atualmente selecionado
  // Pode ser null quando nenhum balcão estiver selecionado
  balcao: {
    id: number;
    numero: number;
    nome: string;
  } | null;

  // Dados adicionais específicos do perfil
  // Poderão ser enviados futuramente pelo backend
  perfilDados?: Record<string, unknown>;
}