<?php

class DatabaseConnection {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $conn;

    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexão bem-sucedida!";
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }

    public function disconnect() {
        $this->conn = null;
        echo "Conexão encerrada!";
    }

    public function select($table, $columns = "*", $where = "", $params = []) {
        try {
            $sql = "SELECT {$columns} FROM {$table} {$where}";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $e) {
            echo "Erro na consulta SELECT: " . $e->getMessage();
        }
    }

    public function insert($table, $data) {
        try {
            $columns = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            echo "Registro inserido com sucesso!";
        } catch (PDOException $e) {
            echo "Erro na inserção: " . $e->getMessage();
        }
    }

    public function update($table, $data, $where = "", $params = []) {
        try {
            $setValues = "";
            foreach ($data as $column => $value) {
                $setValues .= "{$column} = :{$column}, ";
            }
            $setValues = rtrim($setValues, ", ");
            $sql = "UPDATE {$table} SET {$setValues} {$where}";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array_merge($data, $params));
            echo "Registro atualizado com sucesso!";
        } catch (PDOException $e) {
            echo "Erro na atualização: " . $e->getMessage();
        }
    }

    public function delete($table, $where = "", $params = []) {
        try {
            $sql = "DELETE FROM {$table} {$where}";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            echo "Registro excluído com sucesso!";
        } catch (PDOException $e) {
            echo "Erro na exclusão: " . $e->getMessage();
        }
    }
}




// Exemplo de uso

// Configurações de conexão com o banco de dados
$host = "localhost";
$dbname = "mydatabase";
$username = "root";
$password = "password";

// Criação da instância da classe de conexão
$connection = new DatabaseConnection($host, $dbname, $username, $password);

// Conecta ao banco de dados
$connection->connect();


// Executa uma consulta SELECT
$array_where = [
    "id" => 1
];

$results = $connection->select("users", "*", "WHERE id = :id", $array_where);
print_r($results);

// resultado com mais de um registro
foreach ($results as $row) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Nome: " . $row['name'] . "<br>";
    echo "Email: " . $row['email'] . "<br>";
    echo "Idade: " . $row['age'] . "<br>";
    echo "<br>";
}



// Insere um novo registro
$data = [
    "name" => "John Doe",
    "email" => "johndoe@example.com",
    "age" => 30
];
$connection->insert("users", $data);

// Atualiza um registro existente
$data = [
    "name" => "Jane Doe",
    "age" => 28
];
$connection->update("users", $data, "WHERE id = :id", ["id" => 1]);

// Exclui um registro
$connection->delete("users", "WHERE id = :id", ["id" => 1]);

// Encerra a conexão com o banco de dados
$connection->disconnect();
?>