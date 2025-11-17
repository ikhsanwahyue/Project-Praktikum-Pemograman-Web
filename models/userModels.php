<!-- Data user (login, daftar) -->
 <?php
// models/userModels.php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = new Database();
    }

    // GET ALL USERS
    public function getAllUsers() {
        try {
            $query = "SELECT * FROM {$this->table} 
                      ORDER BY created_at DESC";
            
            $this->db->query($query);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    // GET USER BY ID
    public function getUserById($id) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("Error getUserById: " . $e->getMessage());
            return false;
        }
    }

    // GET USER BY EMAIL
    public function getUserByEmail($email) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE email = :email";
            
            $this->db->query($query);
            $this->db->bind('email', $email);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("Error getUserByEmail: " . $e->getMessage());
            return false;
        }
    }

    // CREATE NEW USER
    public function createUser($data) {
        try {
            $query = "INSERT INTO {$this->table} 
                      (nama_lengkap, email, password, role, foto) 
                      VALUES 
                      (:nama_lengkap, :email, :password, :role, :foto)";
            
            $this->db->query($query);
            
            // Bind values
            $this->db->bind('nama_lengkap', $data['nama_lengkap']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('password', $data['password']);
            $this->db->bind('role', $data['role']);
            $this->db->bind('foto', $data['foto']);

            // Execute
            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error createUser: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE USER
    public function updateUser($data) {
        try {
            $query = "UPDATE {$this->table} 
                      SET nama_lengkap = :nama_lengkap,
                          email = :email,
                          foto = :foto,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $this->db->query($query);
            
            // Bind values
            $this->db->bind('id', $data['id']);
            $this->db->bind('nama_lengkap', $data['nama_lengkap']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('foto', $data['foto']);

            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error updateUser: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE PASSWORD
    public function updatePassword($id, $newPassword) {
        try {
            $query = "UPDATE {$this->table} 
                      SET password = :password,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            $this->db->bind('password', $newPassword);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error updatePassword: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE USER ROLE
    public function updateUserRole($id, $role) {
        try {
            $query = "UPDATE {$this->table} 
                      SET role = :role,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            $this->db->bind('role', $role);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error updateUserRole: " . $e->getMessage());
            return false;
        }
    }

    // DELETE USER
    public function deleteUser($id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error deleteUser: " . $e->getMessage());
            return false;
        }
    }

    // VERIFY USER LOGIN
    public function verifyLogin($email, $password) {
        try {
            $user = $this->getUserByEmail($email);
            
            if ($user && password_verify($password, $user->password)) {
                return $user;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error verifyLogin: " . $e->getMessage());
            return false;
        }
    }

    // CHECK IF EMAIL EXISTS
    public function emailExists($email) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE email = :email";
            
            $this->db->query($query);
            $this->db->bind('email', $email);
            $result = $this->db->single();
            
            return $result->total > 0;
        } catch (PDOException $e) {
            error_log("Error emailExists: " . $e->getMessage());
            return false;
        }
    }

    // GET USERS BY ROLE
    public function getUsersByRole($role) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE role = :role 
                      ORDER BY nama_lengkap ASC";
            
            $this->db->query($query);
            $this->db->bind('role', $role);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getUsersByRole: " . $e->getMessage());
            return [];
        }
    }

    // GET USER COUNT
    public function getUserCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            
            $this->db->query($query);
            $result = $this->db->single();
            return $result->total;
        } catch (PDOException $e) {
            error_log("Error getUserCount: " . $e->getMessage());
            return 0;
        }
    }

    // GET USER COUNT BY ROLE
    public function getUserCountByRole($role) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role";
            
            $this->db->query($query);
            $this->db->bind('role', $role);
            $result = $this->db->single();
            return $result->total;
        } catch (PDOException $e) {
            error_log("Error getUserCountByRole: " . $e->getMessage());
            return 0;
        }
    }

    // GET USERS WITH PAGINATION
    public function getUsersWithPagination($limit, $offset) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      ORDER BY created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            $this->db->bind('offset', $offset);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getUsersWithPagination: " . $e->getMessage());
            return [];
        }
    }

    // SEARCH USERS
    public function searchUsers($keyword) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE nama_lengkap LIKE :keyword 
                         OR email LIKE :keyword
                      ORDER BY nama_lengkap ASC";
            
            $this->db->query($query);
            $this->db->bind('keyword', "%$keyword%");
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error searchUsers: " . $e->getMessage());
            return [];
        }
    }

    // UPDATE USER PROFILE
    public function updateProfile($data) {
        try {
            $query = "UPDATE {$this->table} 
                      SET nama_lengkap = :nama_lengkap,
                          email = :email,
                          foto = :foto,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $this->db->query($query);
            
            $this->db->bind('id', $data['id']);
            $this->db->bind('nama_lengkap', $data['nama_lengkap']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('foto', $data['foto']);

            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error updateProfile: " . $e->getMessage());
            return false;
        }
    }

    // GET RECENT USERS
    public function getRecentUsers($limit = 5) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getRecentUsers: " . $e->getMessage());
            return [];
        }
    }
}
?>