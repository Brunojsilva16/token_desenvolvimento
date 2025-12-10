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
     * Padronizado para usar placeholders (?) no INSERT.
     */
    public function create($data)
    {
        try {
            // TENTATIVA 1: Inserir com todos os campos (nome, cpf, prof, resp1, resp2, origem)
            // Note que data_cadastro é NOW(), então não precisa de ? para ele.
            $sql = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, nome_responsavel, responsavel_financeiro, origem, data_cadastro) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            // A ordem do array deve seguir EXATAMENTE a ordem das colunas no INSERT acima
            return $stmt->execute([
                $data['nome'],                                          // 1. nome
                $data['cpf'],                                           // 2. cpf
                !empty($data['id_prof']) ? $data['id_prof'] : null,     // 3. id_prof_referencia
                $data['nome_responsavel'] ?? null,                      // 4. nome_responsavel
                $data['responsavel_financeiro'] ?? null,                // 5. responsavel_financeiro
                $data['origem'] ?? null                                 // 6. origem
            ]);

        } catch (Exception $e) {
            // FALLBACK (PLANO B): Se a tabela estiver desatualizada (sem colunas novas),
            // tenta salvar apenas o básico usando a mesma padronização (?).
            try {
                $sqlBackup = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, data_cadastro) 
                              VALUES (?, ?, ?, NOW())";
                
                $stmtBackup = $this->conn->prepare($sqlBackup);
                
                return $stmtBackup->execute([
                    $data['nome'],                                      // 1. nome
                    $data['cpf'],                                       // 2. cpf
                    !empty($data['id_prof']) ? $data['id_prof'] : null  // 3. id_prof_referencia
                ]);

            } catch (Exception $ex) {
                // Se falhar até o backup, retorna falso
                return false;
            }
        }
    }

    /**
     * Busca Híbrida Blindada:
     * Mantida com parâmetros nomeados (:term) pois facilita a reutilização do mesmo valor
     * em múltiplos lugares da query (OR/UNION) sem precisar repetir no array.
     */
    public function search($term)
    {
        $term = "%$term%";
        
        try {
            // TENTATIVA 1: Busca Completa
            $sql = "
            SELECT nome, cpf, nome_responsavel as resp, responsavel_financeiro as fin, origem, 'cadastro' as tipo_origem 
            FROM pacientes 
            WHERE nome LIKE :term1 OR cpf LIKE :term2
            
            UNION
            
            SELECT DISTINCT paciente as nome, cpf, nome_resp as resp, responsavel_f as fin, 'historico' as origem, 'historico' as tipo_origem
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
            // TENTATIVA 2 (FALLBACK): Busca Simples
            $sqlSimple = "
            SELECT nome, cpf, NULL as resp, NULL as fin, 'cadastro' as origem, 'cadastro' as tipo_origem 
            FROM pacientes 
            WHERE nome LIKE :term1 OR cpf LIKE :term2
            
            UNION
            
            SELECT DISTINCT paciente as nome, cpf, nome_resp as resp, responsavel_f as fin, 'historico' as origem, 'historico' as tipo_origem 
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