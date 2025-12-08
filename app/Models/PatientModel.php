<?php
// app/Models/PatientModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;

class PatientModel
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO pacientes (nome, cpf, id_prof_referencia, nome_responsavel, responsavel_financeiro, data_cadastro) 
                VALUES (?,?,?,?,?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            $data['nome'],
            $data['cpf'],
            !empty($data['id_prof']) ? $data['id_prof'] : null,
            $data['nome_responsavel'] ?? null,
            $data['responsavel_financeiro'] ?? null
        ]);
    }

    /**
     * Busca pacientes incluindo responsáveis
     */
    public function search($term)
    {
        $term = "%$term%";
        
        // UNION: Padronizamos os nomes das colunas com 'AS'
        $sql = "
        SELECT nome, cpf, nome_responsavel as resp, responsavel_financeiro as fin, 'cadastro' as origem 
        FROM pacientes 
        WHERE nome LIKE :term1 OR cpf LIKE :term2
        
        UNION
        
        SELECT DISTINCT paciente as nome, cpf, nome_resp as resp, responsavel_f as fin, 'historico' as origem 
        FROM gtoken 
        WHERE paciente LIKE :term3 OR cpf LIKE :term4
        
        LIMIT 20";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':term1' => $term,
            ':term2' => $term,
            ':term3' => $term,
            ':term4' => $term
        ]);

        return $stmt->fetchAll();
    }
}