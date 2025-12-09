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

    /**
     * Cria um novo paciente no banco de dados.
     * Tenta salvar todos os campos, mas tem um plano B se a tabela estiver desatualizada.
     */
    public function create($data)
    {
        try {
            // TENTATIVA 1: Inserir com todos os campos novos (origem, responsáveis)
            $sql = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, nome_responsavel, responsavel_financeiro, origem, data_cadastro) 
                    VALUES (:nome, :cpf, :id_prof, :nome_resp, :resp_fin, :origem, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                ':nome' => $data['nome'],
                ':cpf' => $data['cpf'],
                ':id_prof' => !empty($data['id_prof']) ? $data['id_prof'] : null,
                ':nome_resp' => $data['nome_responsavel'] ?? null,
                ':resp_fin' => $data['responsavel_financeiro'] ?? null,
                ':origem' => $data['origem'] ?? null
            ]);

        } catch (Exception $e) {
            // FALLBACK (PLANO B): Se der erro (ex: coluna 'origem' não existe), 
            // tenta salvar só o básico para o usuário não perder o cadastro.
            try {
                $sqlBackup = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, data_cadastro) 
                              VALUES (:nome, :cpf, :id_prof, NOW())";
                $stmtBackup = $this->conn->prepare($sqlBackup);
                return $stmtBackup->execute([
                    ':nome' => $data['nome'],
                    ':cpf' => $data['cpf'],
                    ':id_prof' => !empty($data['id_prof']) ? $data['id_prof'] : null
                ]);
            } catch (Exception $ex) {
                // Se falhar até o backup, retorna falso
                return false;
            }
        }
    }

    /**
     * Busca Híbrida Blindada:
     * Procura tanto na tabela de 'pacientes' quanto no histórico antigo de 'gtoken'.
     */
    public function search($term)
    {
        $term = "%$term%";
        
        try {
            // TENTATIVA 1: Busca Completa (Colunas novas devem existir)
            // Traz dados detalhados para preencher o formulário automaticamente
            $sql = "
            SELECT nome, cpf, nome_responsavel as resp, responsavel_financeiro as fin, origem, 'cadastro' as tipo_origem 
            FROM pacientes 
            WHERE nome LIKE :term1 OR cpf LIKE :term2
            
            UNION
            
            SELECT DISTINCT paciente as nome, cpf, nomeresp as resp, responsavel_f as fin, 'historico' as origem, 'historico' as tipo_origem
            FROM gtoken 
            WHERE paciente LIKE :term3 OR cpf LIKE :term4
            
            LIMIT 20";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':term1' => $term, ':term2' => $term,
                ':term3' => $term, ':term4' => $term
            ]);
            
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            // TENTATIVA 2 (FALLBACK): Se der erro de coluna não encontrada, busca apenas Nome e CPF.
            // Isso garante que o autocomplete continue funcionando mesmo sem os detalhes extras.
            $sqlSimple = "
            SELECT nome, cpf, NULL as resp, NULL as fin, 'cadastro' as origem, 'cadastro' as tipo_origem 
            FROM pacientes 
            WHERE nome LIKE :term1 OR cpf LIKE :term2
            
            UNION
            
            SELECT DISTINCT paciente as nome, cpf, nomeresp as resp, responsavel_f as fin, 'historico' as origem, 'historico' as tipo_origem 
            FROM gtoken 
            WHERE paciente LIKE :term3 OR cpf LIKE :term4
            
            LIMIT 20";

            $stmt = $this->conn->prepare($sqlSimple);
            $stmt->execute([
                ':term1' => $term, ':term2' => $term,
                ':term3' => $term, ':term4' => $term
            ]);

            return $stmt->fetchAll();
        }
    }
}