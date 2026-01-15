  <?php

    require_once 'interfaces/ILoginUserRepository.php';
    require_once 'models/User.php';
    require_once 'config/Database.php';

    /**
     * MariaDBUserRepository - Implementación de IUserRepository para MariaDB
     * Maneja todas las operaciones de base de datos relacionadas con usuarios
     * No contiene lógica de controlador, solo acceso a datos
     */
    class MariaDBILoginUserRepository implements ILoginUserRepository
    {
        private $conn; //Conexion con la base de datos
        private $table = 'users';

        /**
         * Constructor - Inicializa la conexión a BD
         */
        public function __construct(PDO $pdo)
        {
            $this->conn = $pdo;
        }

        /**
         * Obtener usuario por email
         * @param string $email Email del usuario a buscar
         * @return User|null Usuario encontrado o null
         */
        public function getByEmail($email)
        {
            $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";

            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    return $this->mapRowToUser($row);
                }
                return null;
            } catch (PDOException $e) {
                throw new Exception("Error al buscar usuario: " . $e->getMessage());
            }
        }

        /**
         * Obtener usuario por ID
         * @param int $id ID del usuario a buscar
         * @return User|null Usuario encontrado o null
         */
        public function getById($id)
        {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";

            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_STR);
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    return $this->mapRowToUser($row);
                }

                return null;
            } catch (PDOException $e) {
                throw new Exception("Error al buscar usuario por ID: " . $e->getMessage());
            }
        }

        /**
         * Guardar un nuevo usuario en la base de datos
         * @param User $user Usuario a guardar
         * @return bool true si fue exitoso
         */
        public function save(User $user)
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
         * Actualizar intentos fallidos de login
         * @param string $id ID del usuario
         * @param int $attempts Número de intentos fallidos
         * @return bool true si fue exitoso
         */
        public function updateAttempts($id, $attempts)
        {
            $query = "UPDATE " . $this->table . " SET tryAttempts = :attempts WHERE id = :id";

            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':attempts', $attempts, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_STR);

                return $stmt->execute();
            } catch (PDOException $e) {
                throw new Exception("Error al actualizar intentos: " . $e->getMessage());
            }
        }
        /**
         * Desactiva al usuario por seguridad al haber tanto intentos fallidos
         * @param string $id ID del usuario
         */
        public function updateState($id)
        {
            $query = "UPDATE " . $this->table . " SET state = 0 WHERE id = :id";

            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_STR);

                return $stmt->execute();
            } catch (PDOException $e) {
                throw new Exception("Error a desactivar el usuario: " . $e->getMessage());
            }
        }

        /**
         * Resetear intentos fallidos a 0 (después de login exitoso)
         * @param string $id ID del usuario
         * @return bool true si fue exitoso
         */
        public function resetAttempts($id)
        {
            $query = "UPDATE " . $this->table . " 
                  SET tryAttempts = 0
                  WHERE id = :id";

            try {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_STR);

                return $stmt->execute();
            } catch (PDOException $e) {
                throw new Exception("Error al resetear intentos: " . $e->getMessage());
            }
        }

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
