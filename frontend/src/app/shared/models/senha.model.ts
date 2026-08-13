export interface Senha {
  id: number;
  codigo: string;
  nomeCliente: string | null;
  telefoneContacto: string | null;
  tipoAtendimentoId: number;
  balcaoId: number | null;
  status: string;
  dataEmissao: string;
  dataChamada: string | null;
  dataInicioAtendimento: string | null;
  dataFinalizacao: string | null;
}
