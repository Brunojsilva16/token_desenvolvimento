<?php
// app/Models/TokenModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;
use Exception;

class TokenModel
{
    protected $conn;
    private $table = 'tokens'; 
    private $colunaData = 'data_cadastro'; 

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO {$this->table} (
                        token, id_user, id_prof, paciente, cpf, telefone, nome_resp, responsavel_f, 
                        nome_banco, origem,
                        valor, formapag, modalidadep, vencimento, 
                        statuspag, {$this->colunaData}
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'efetuado', NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->execute([
                $data['token'],
                $data['id_user'],
                $data['id_prof'],
                $data['paciente'],
                $data['cpf'] ?? null,
                $data['telefone'] ?? null,
                $data['nome_resp'] ?? null,
                $data['responsavel_f'] ?? null,
                $data['nome_banco'] ?? null,
                $data['origem'] ?? null,
                $data['valor'] ?? 0.00,
                $data['formapag'] ?? null,
                $data['modalidadep'] ?? null,
                $data['vencimento'] ?? date('Y-m-d')
            ]);

            $idToken = $this->conn->lastInsertId();

            if (!empty($data['sessoes']) && is_array($data['sessoes'])) {
                $sqlSessao = "INSERT INTO sessoes (id_token, data_sessao) VALUES (?, ?)";
                $stmtSessao = $this->conn->prepare($sqlSessao);

                foreach ($data['sessoes'] as $dataSessao) {
                    if (!empty($dataSessao)) {
                        $stmtSessao->execute([$idToken, $dataSessao]);
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

    /**
     * Busca tokens com detalhes, suporte a pesquisa e limite
     */
    public function getAllWithDetails($limit = 25, $search = '')
    {
        $sql = "SELECT t.id_token, t.token, t.paciente, t.cpf, t.statuspag, t.valor, t.formapag, 
                       t.nome_banco, t.vencimento, t.origem, t.nome_resp,
                       t.{$this->colunaData} as data_registro, p.nome as nome_profissional, u.nome as nome_usuario
                FROM {$this->table} t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user";
        
        $params = [];

        // Adiciona filtro de busca se houver
        if (!empty($search)) {
            $sql .= " WHERE (t.paciente LIKE :search OR t.token LIKE :search OR t.cpf LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // ALTERAÇÃO AQUI: Ordenar pela coluna de data (data_cadastro) DESC
        $sql .= " ORDER BY t.{$this->colunaData} DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', $params[':search']);
        }
        
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    /**
     * Busca tokens por usuário com suporte a pesquisa e limite
     */
    public function getByUserId($userId, $limit = 25, $search = '')
    {
        $sql = "SELECT t.id_token as id, t.token, t.paciente as nome_paciente, 
                       t.vencimento, t.valor, t.origem, t.nome_resp,
                       t.{$this->colunaData} as data_registro,
                       p.nome as nome_profissional 
                FROM {$this->table} t LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                WHERE t.id_user = :id_user";
        
        $params = [':id_user' => $userId];

        if (!empty($search)) {
            $sql .= " AND (t.paciente LIKE :search OR t.token LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // ALTERAÇÃO AQUI: Ordenar pela coluna de data (data_cadastro) DESC
        $sql .= " ORDER BY t.{$this->colunaData} DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_user', $userId);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', $params[':search']);
        }
        
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, p.nome as nome_profissional, u.nome as nome_usuario 
                FROM {$this->table} t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof 
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user
                WHERE t.id_token = :id";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getSessoes($idToken)
    {
        $sql = "SELECT data_sessao FROM sessoes WHERE id_token = :id ORDER BY data_sessao ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idToken]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); 
    }
    
    public function generateUniqueCode()
    {
        $limit = 0;
        do {
            // Gerando 12 dígitos (6 + 6)
            $part1 = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $part2 = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $code = $part1 . $part2;

            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE token = :token");
            $stmt->execute([':token' => $code]);
            $exists = $stmt->fetchColumn() > 0;

            $limit++;
        } while ($exists && $limit < 10);

        return $code;
    }
    
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET 
                        id_prof = ?, 
                        paciente = ?, 
                        cpf = ?, 
                        nome_resp = ?, 
                        responsavel_f = ?, 
                        nome_banco = ?, 
                        valor = ?, 
                        formapag = ?, 
                        modalidadep = ?, 
                        vencimento = ?,
                        origem = ?
                    WHERE id_token = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                $data['id_prof'],
                $data['paciente'],
                $data['cpf'] ?? null,
                $data['nome_resp'] ?? null,
                $data['responsavel_f'] ?? null,
                $data['nome_banco'] ?? null,
                $data['valor'] ?? 0.00,
                $data['formapag'] ?? null,
                $data['modalidadep'] ?? null,
                $data['vencimento'] ?? null,
                $data['origem'] ?? null,
                $id
            ]);

        } catch (Exception $e) {
            return false;
        }
    }
}