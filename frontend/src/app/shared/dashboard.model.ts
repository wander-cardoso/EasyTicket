// Representa os dados principais apresentados no Dashboard
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
  // É null quando o utilizador ainda não possui balcão
  balcao: {
    id: number;
    numero: number;
    nome: string;
  } | null;

  // Permite ao backend enviar informações específicas
  // conforme o perfil do utilizador
  perfilDados?: Record<string, unknown>;
}