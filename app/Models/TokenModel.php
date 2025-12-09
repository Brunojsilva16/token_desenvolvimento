<?php
// app/Models/TokenModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;
use Exception;

class TokenModel
{
    protected $conn;
    private $colunaData = 'datacad';

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    /**
     * Cria um novo token e suas sessões (se houver)
     */
    public function create($data)
    {
        try {
            // Inicia a transação (Tudo ou Nada)
            $this->conn->beginTransaction();

            // 1. Inserir o Token (gtoken)
            // Usando placeholders posicionais (?) para simplificar
            $sql = "INSERT INTO gtoken (
                        token, id_user, id_prof, paciente, cpf, nome_resp, responsavel_f, nome_banco, 
                        modalidadepag, formapag, valorpag, vencimento, statuspag, {$this->colunaData}
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'efetuado', NOW())";

            $stmt = $this->conn->prepare($sql);

            // A ordem aqui DEVE ser exatamente a mesma das colunas acima
            $stmt->execute([
                $data['token'],
                $data['id_user'],
                $data['id_prof'],
                $data['paciente'],
                $data['cpf'] ?? null,
                $data['nome_resp'] ?? null,
                $data['responsavel_financeiro'] ?? null,
                $data['nome_banco'] ?? null, // Novo campo
                $data['valor'] ?? 0.00,
                $data['formapagamento'] ?? null,
                $data['modalidade'] ?? null,
                $data['vencimento'] ?? date('Y-m-d')
            ]);

            // Recupera o ID do token recém-criado
            $idToken = $this->conn->lastInsertId();

            // 2. Inserir as Sessões (se houver)
            if (!empty($data['sessoes']) && is_array($data['sessoes'])) {
                // Usando ? aqui também
                $sqlSessao = "INSERT INTO sessoes (id_token, data_sessao, status) VALUES (?, ?, 'efetuado')";
                $stmtSessao = $this->conn->prepare($sqlSessao);

                foreach ($data['sessoes'] as $dataSessao) {
                    if (!empty($dataSessao)) {
                        $stmtSessao->execute([
                            $idToken,
                            $dataSessao
                        ]);
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getAllWithDetails($limit = 100)
    {
        $sql = "SELECT t.id_token, t.token, t.paciente, t.cpf, t.statuspag, t.statuspag, t.valorpag, t.formapag, 
                       t.{$this->colunaData} as data_registro, p.nome_p as nome_profissional, u.nome as nome_usuario
                FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user
                ORDER BY t.{$this->colunaData} DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($userId)
    {
        $sql = "SELECT t.id_token as id, t.token, t.paciente as nome_paciente, t.statuspag, t.{$this->colunaData} as data_registro,
                       p.nome_p as nome_profissional 
                FROM gtoken t LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                WHERE t.id_user = :id_user ORDER BY t.{$this->colunaData} DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_user', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, p.nome_p as nome_profissional 
                FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof 
                WHERE t.id_token = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Gera um código único de 16 dígitos numéricos
     */
    public function generateUniqueCode()
    {
        $limit = 0;
        do {
            // Gera 16 dígitos (2 partes de 8 para garantir aleatoriedade e formato)
            $part1 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $part2 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $code = $part1 . $part2;

            // Verifica se já existe no banco
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM gtoken WHERE token = :token");
            $stmt->execute([':token' => $code]);
            $exists = $stmt->fetchColumn() > 0;

            $limit++;
        } while ($exists && $limit < 10); // Tenta 10 vezes antes de desistir (segurança contra loop infinito)

        return $code;
    }

    /**
     * Atualiza os dados de um token existente
     */
    public function update($id, $data)
    {
        try {
            // Usando placeholders (?) para segurança
            $sql = "UPDATE gtoken SET 
                        id_prof = ?, 
                        paciente = ?, 
                        cpf = ?, 
                        nome_resp = ?, 
                        responsavel_f = ?, 
                        nome_banco = ?, 
                        valorpag = ?, 
                        formapag = ?, 
                        modalidadepag = ?, 
                        vencimento = ?
                    WHERE id_token = ?";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                $data['id_prof'],
                $data['paciente'],
                $data['cpf'] ?? null,
                $data['nomeresp'] ?? null,
                $data['responsavel_f'] ?? null,
                $data['nome_banco'] ?? null,
                $data['valor'] ?? 0.00,
                $data['formapag'] ?? null,
                $data['modalidade'] ?? null,
                $data['vencimento'] ?? null,
                $id // O ID vai por último no WHERE
            ]);
        } catch (Exception $e) {
            // Logar erro se necessário
            return false;
        }
    }
}
