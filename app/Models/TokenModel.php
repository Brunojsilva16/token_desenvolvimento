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

    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            // CORREÇÃO: 'nome_resp' alterado para 'nome_resp' para bater com o banco
            $sql = "INSERT INTO gtoken (
                        token, id_user, id_prof, paciente, cpf, nome_resp, responsavel_f, 
                        nome_banco, origem,
                        valor, formapag, modalidadep, parcelas, vencimento, 
                        statuspag, {$this->colunaData}
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'efetuado', NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->execute([
                $data['token'],
                $data['id_user'],
                $data['id_prof'],
                $data['paciente'],
                $data['cpf'] ?? null,
                $data['nome_resp'] ?? null, // Ajustado chave do array
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
            // Para debug: descomente a linha abaixo para ver o erro na tela se precisar
            // die($e->getMessage());
            return false;
        }
    }

    public function getAllWithDetails($limit = 100)
    {
        // CORREÇÃO: t.nome_resp no SELECT (se precisar usar)
        $sql = "SELECT t.id_token, t.token, t.paciente, t.cpf, t.statuspag, t.valorpag, t.formapag, 
                       t.nome_banco, t.vencimento, t.origem, t.nome_resp,
                       t.{$this->colunaData} as data_registro, p.nome_p as nome_profissional, u.nome as nome_usuario
                FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user
                ORDER BY t.id_token DESC 
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public function getByUserId($userId)
    {
        $sql = "SELECT t.id_token as id, t.token, t.paciente as nome_paciente, t.vencimento, t.{$this->colunaData} as data_registro,
                       p.nome_p as nome_profissional 
                FROM gtoken t LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                WHERE t.id_user = :id_user 
                ORDER BY t.id_token DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_user', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        // Busca completa com nome_resp correto
        $sql = "SELECT t.*, p.nome as nome_profissional FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof WHERE t.id_token = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function generateUniqueCode()
    {
        $limit = 0;
        do {
            $part1 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $part2 = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $code = $part1 . $part2;

            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM gtoken WHERE token = :token");
            $stmt->execute([':token' => $code]);
            $exists = $stmt->fetchColumn() > 0;

            $limit++;
        } while ($exists && $limit < 10);

        return $code;
    }
    
    public function update($id, $data)
    {
        try {
            // CORREÇÃO: nome_resp no UPDATE
            $sql = "UPDATE gtoken SET 
                        id_prof = ?, 
                        paciente = ?, 
                        cpf = ?, 
                        nome_resp = ?, 
                        responsavel_f = ?, 
                        nome_banco = ?, 
                        valor = ?, 
                        formapag = ?, 
                        modalidadep = ?, 
                        parcelas = ?, 
                        vencimento = ?,
                        origem = ?
                    WHERE id_token = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                $data['id_prof'],
                $data['paciente'],
                $data['cpf'] ?? null,
                $data['nome_resp'] ?? null, // Ajustado
                $data['responsavel_f'] ?? null,
                $data['nome_banco'] ?? null,
                $data['valor'] ?? 0.00,
                $data['formapag'] ?? null,
                $data['modalidadep'] ?? null,
                $data['parcelas'] ?? 1,
                $data['vencimento'] ?? null,
                $data['origem'] ?? null,
                $id
            ]);

        } catch (Exception $e) {
            return false;
        }
    }
}