<?php
// app/Models/UserModel.php

namespace App\Models;

use App\Database\Connection;
use PDO;

class UserModel
{
    protected $conn;

    public function __construct()
    {
        $this->conn = Connection::getInstance();
    }

    public function authenticate($email, $password)
    {
        // MUDANÇA: Tabela 'usuarios_a'
        $sql = "SELECT * FROM usuarios_a WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Mapeando para o sistema entender 'id' independente do nome no banco
            $user['id'] = $user['id_user']; 

            // Coluna de senha (assumindo 'senha')
            $hashBanco = $user['senha']; 

            // Verifica BCRYPT (Moderno)
            if (password_verify($password, $hashBanco)) {
                return $user;
            }
            
            // Verifica MD5 (Legado)
            if (md5($password) === $hashBanco) {
                return $user;
            }

            // Verifica Texto Puro (Caso de testes)
            if ($password === $hashBanco) {
                return $user;
            }
        }

        return false;
    }
}