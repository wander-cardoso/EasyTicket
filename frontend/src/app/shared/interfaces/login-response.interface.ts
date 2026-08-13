// Representa os dados do utilizador devolvidos pelo backend
export interface UtilizadorLogin {

  // Identificador do utilizador
  id: number;

  // Nome completo do utilizador
  nome: string;

  // Nome utilizado para efetuar login
  nomeUtilizador: string;

  // Perfil de acesso do utilizador
  perfil: string;
}


// Representa os dados devolvidos pela API após o login
export interface LoginResponse {

  // JWT utilizado para autenticar as próximas requisições
  token: string;

  // Dados públicos do utilizador autenticado
  utilizador: UtilizadorLogin;
}