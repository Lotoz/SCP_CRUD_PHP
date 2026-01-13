<?php
require_once 'models/User.php';
require_once 'interfaces/IUserRepository.php';
require_once 'config/Database.php';

class MariaDBICrudRepositoryUsers implements IUserRepository
{
    private $conn; //Conexion con la base de datos
    private $table = 'users';

    /**
     * Constructor - Inicializa la conexión a BD
     */
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * 
     */
    public function allData()
    {
        $query = "SELECT * FROM " . $this->table;

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            //$rows = $stmt->fetch(PDO::FETCH_ASSOC);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error al buscar usuarios: " . $e->getMessage());
        }
    }
    /**
     * Crear nuevo item
     */
    public function createObject(User $user)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (id, name, lastname, email, password, rol, theme, tryAttempts, state, creationDate) 
                  VALUES 
                  (:id, :name, :lastname, :email, :password, :rol, :theme, :tryAttempts, :state, :creationDate)";

        try {
            $stmt = $this->conn->prepare($query);

            // Hash de la contraseña
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_BCRYPT);
            $stmt->bindParam(':id', $user->getId(), PDO::PARAM_STR);
            $stmt->bindParam(':name', $user->getname(), PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $user->getlastname(), PDO::PARAM_STR);
            $stmt->bindParam(':email', $user->getEmail(), PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $user->getRol(), PDO::PARAM_STR);
            $stmt->bindParam(':theme', $user->getTheme(), PDO::PARAM_STR);
            $stmt->bindParam(':tryAttempts', $user->gettryAttempts(), PDO::PARAM_INT);
            $stmt->bindParam(':state', $user->isstate(), PDO::PARAM_BOOL);
            $stmt->bindParam(':creationDate', $user->getcreationDate(), PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al guardar usuario: " . $e->getMessage());
        }
    }
    /**
     * Editar object
     */
    public function editObject() {}
    /**
     * Eliminar un objeto
     */
    public function delete() {}

     // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Convertir una fila de BD a objeto User
     * @param array $row Fila de la base de datos
     * @return User Objeto User construido
     */
    private function mapRowToUser($row)
    {
        $user = new User(
            $row['id'],
            $row['name'],
            $row['lastname'],
            $row['email'],
            $row['password'],
            $row['level'],
            $row['rol'],
            $row['theme']
        );

        $user->settryAttempts($row['tryAttempts']);
        $user->setstate((bool)$row['state']);
        $user->setcreationDate($row['creationDate']);

        return $user;
    }
}
