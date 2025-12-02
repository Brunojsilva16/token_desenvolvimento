<?php
// app/Models/TokenModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;

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
        // SQL ATUALIZADO: Incluindo nomeresp e responsavel_f
        $sql = "INSERT INTO gtoken (token, id_user, id_prof, paciente, nomeresp, responsavel_f, statuspag, {$this->colunaData}) 
                VALUES (:token, :id_user, :id_prof, :paciente, :nomeresp, :responsavel_f, 'AGUARDANDO', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            ':token' => $data['token'],
            ':id_user' => $data['user_id'],
            ':id_prof' => $data['profissional_id'],
            ':paciente' => $data['nome_paciente'],
            ':nomeresp' => $data['nomeresp'] ?? null,
            ':responsavel_f' => $data['responsavel_f'] ?? null
        ]);
    }

    public function getByUserId($userId)
    {
        $sql = "SELECT t.id_token as id, t.token, t.paciente as nome_paciente, t.statuspag, t.{$this->colunaData} as data_registro,
                       p.profissional as nome_profissional 
                FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                WHERE t.id_user = :id_user 
                ORDER BY t.{$this->colunaData} DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_user', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getStats($userId)
    {
        $sqlTotal = "SELECT COUNT(*) as total FROM gtoken WHERE id_user = :uid";
        $sqlHoje = "SELECT COUNT(*) as total FROM gtoken WHERE id_user = :uid AND DATE({$this->colunaData}) = CURDATE()";
        $sqlPending = "SELECT COUNT(*) as total FROM gtoken WHERE id_user = :uid AND statuspag = 'AGUARDANDO'";
        $stmtTotal = $this->conn->prepare($sqlTotal);
        $stmtTotal->execute([':uid' => $userId]);
        $stmtHoje = $this->conn->prepare($sqlHoje);
        $stmtHoje->execute([':uid' => $userId]);
        $stmtPending = $this->conn->prepare($sqlPending);
        $stmtPending->execute([':uid' => $userId]);
        return [
            'total' => $stmtTotal->fetch()['total'],
            'hoje' => $stmtHoje->fetch()['total'],
            'pendentes' => $stmtPending->fetch()['total']
        ];
    }

    public function getById($id)
    {
        // Podemos adicionar nomeresp e responsavel_f no select se quiser exibir no cupom
        $sql = "SELECT t.id_token as id, t.token, t.paciente as nome_paciente, t.{$this->colunaData} as created_at,
                       t.nomeresp, t.responsavel_f,
                       p.profissional as nome_profissional 
                FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                WHERE t.id_token = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllWithDetails($limit = 50)
    {
        $sql = "SELECT t.id_token as id, t.token, t.paciente, t.cpf, t.statuspag, 
                       t.{$this->colunaData} as data_registro,
                       p.profissional as nome_profissional,
                       u.nome as nome_usuario
                       -- , t.valor
                       -- , t.forma_pgto
                FROM gtoken t 
                LEFT JOIN profissionais p ON t.id_prof = p.id_prof
                LEFT JOIN usuarios_a u ON t.id_user = u.id_user
                ORDER BY t.{$this->colunaData} DESC 
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function confirmPayment($id, $valor, $formaPgto)
    {
        $sql = "UPDATE gtoken SET statuspag = 'PAGO' WHERE id_token = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function generateUniqueCode()
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    }
}