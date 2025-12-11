<?php
// app/Models/PatientModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;
use Exception;
use PDOException;

class PatientModel
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, nome_responsavel, responsavel_financeiro, origem, data_cadastro) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                $data['nome'],
                $data['cpf'],
                !empty($data['id_prof']) ? $data['id_prof'] : null,
                $data['nome_responsavel'] ?? null,
                $data['responsavel_financeiro'] ?? null,
                $data['origem'] ?? null
            ]);

        } catch (Exception $e) {
            // Fallback simples
            try {
                $sqlBackup = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, data_cadastro) 
                              VALUES (?, ?, ?, NOW())";
                $stmtBackup = $this->conn->prepare($sqlBackup);
                return $stmtBackup->execute([
                    $data['nome'],
                    $data['cpf'],
                    !empty($data['id_prof']) ? $data['id_prof'] : null
                ]);
            } catch (Exception $ex) {
                return false;
            }
        }
    }

    /**
     * Busca Exclusiva em Pacientes Cadastrados
     * Removemos o UNION com a tabela antiga de tokens/histórico.
     */
    public function search($term)
    {
        $term = "%$term%";
        
        try {
            // Apenas tabela pacientes
            $sql = "
            SELECT 
                nome, 
                cpf, 
                nome_responsavel as resp, 
                responsavel_financeiro as fin, 
                origem, 
                'cadastro' as tipo_origem 
            FROM pacientes 
            WHERE nome LIKE :term1 OR cpf LIKE :term2
            LIMIT 20";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':term1' => $term, 
                ':term2' => $term
            ]);
            
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            // Fallback se colunas novas não existirem
            $sqlSimple = "
            SELECT nome, cpf, NULL as resp, NULL as fin, 'cadastro' as origem, 'cadastro' as tipo_origem 
            FROM pacientes 
            WHERE nome LIKE :term1 OR cpf LIKE :term2
            LIMIT 20";

            $stmt = $this->conn->prepare($sqlSimple);
            $stmt->execute([':term1' => $term, ':term2' => $term]);

            return $stmt->fetchAll();
        }
    }
}