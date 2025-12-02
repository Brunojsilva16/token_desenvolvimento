<?php
// app/Models/ProfessionalModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;

class ProfessionalModel
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    public function getAll()
    {
        // MUDANÇA: Tabela 'profissionais' e PK 'id_prof'
        // Usamos 'as id' para manter compatibilidade com o <select> da View
        $sql = "SELECT id_prof as id, profissional FROM profissionais ORDER BY profissional ASC"; 
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}