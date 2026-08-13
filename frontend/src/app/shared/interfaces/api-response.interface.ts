// Representa o formato padrão das respostas da API
export interface ApiResponse<T> {

  // Indica se a operação foi realizada com sucesso
  success: boolean;

  // Mensagem enviada pelo backend
  message: string;

  // Dados específicos da resposta
  data: T;
}