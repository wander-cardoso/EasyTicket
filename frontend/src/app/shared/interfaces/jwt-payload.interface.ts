// Representa o conteúdo do JWT utilizado pela aplicação
export interface JwtPayload {

  // Identificador do utilizador autenticado
  sub: number;

  // Nome de utilizador utilizado no login
  nomeUtilizador: string;

  // Perfil do utilizador
  perfil: string;

  // ID do balcão selecionado.
  // É opcional porque o utilizador pode ainda não ter selecionado um balcão.
  balcaoId?: number;

  // Data em que o JWT foi criado
  iat: number;

  // Data em que o JWT irá expirar
  exp: number;

  // Emissor do JWT
  iss: string;
}