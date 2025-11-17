<!-- Data kontak  -->
 <?php
// models/kontakModels.php
require_once __DIR__ . '/../config/database.php';

class KontakModel {
    private $db;
    private $table = 'kontak';

    public function __construct() {
        $this->db = new Database();
    }

    // GET ALL MESSAGES
    public function getAllMessages() {
        try {
            $query = "SELECT * FROM {$this->table} 
                      ORDER BY created_at DESC";
            
            $this->db->query($query);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getAllMessages: " . $e->getMessage());
            return [];
        }
    }

    // GET MESSAGE BY ID
    public function getMessageById($id) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("Error getMessageById: " . $e->getMessage());
            return false;
        }
    }

    // GET UNREAD MESSAGES COUNT
    public function getUnreadCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'unread'";
            
            $this->db->query($query);
            $result = $this->db->single();
            return $result->total;
        } catch (PDOException $e) {
            error_log("Error getUnreadCount: " . $e->getMessage());
            return 0;
        }
    }

    // GET MESSAGES BY STATUS
    public function getMessagesByStatus($status) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE status = :status 
                      ORDER BY created_at DESC";
            
            $this->db->query($query);
            $this->db->bind('status', $status);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getMessagesByStatus: " . $e->getMessage());
            return [];
        }
    }

    // ADD NEW MESSAGE
    public function addMessage($data) {
        try {
            $query = "INSERT INTO {$this->table} 
                      (nama, email, subject, message, status) 
                      VALUES 
                      (:nama, :email, :subject, :message, 'unread')";
            
            $this->db->query($query);
            
            // Bind values
            $this->db->bind('nama', $data['nama']);
            $this->db->bind('email', $data['email']);
            $this->db->bind('subject', $data['subject']);
            $this->db->bind('message', $data['message']);

            // Execute
            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error addMessage: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE MESSAGE STATUS
    public function updateMessageStatus($id, $status) {
        try {
            $query = "UPDATE {$this->table} 
                      SET status = :status,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            $this->db->bind('status', $status);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error updateMessageStatus: " . $e->getMessage());
            return false;
        }
    }

    // MARK AS READ
    public function markAsRead($id) {
        return $this->updateMessageStatus($id, 'read');
    }

    // MARK AS REPLIED
    public function markAsReplied($id) {
        return $this->updateMessageStatus($id, 'replied');
    }

    // DELETE MESSAGE
    public function deleteMessage($id) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error deleteMessage: " . $e->getMessage());
            return false;
        }
    }

    // GET MESSAGE COUNT
    public function getMessageCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table}";
            
            $this->db->query($query);
            $result = $this->db->single();
            return $result->total;
        } catch (PDOException $e) {
            error_log("Error getMessageCount: " . $e->getMessage());
            return 0;
        }
    }

    // GET MESSAGES WITH PAGINATION
    public function getMessagesWithPagination($limit, $offset) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      ORDER BY created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            $this->db->bind('offset', $offset);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getMessagesWithPagination: " . $e->getMessage());
            return [];
        }
    }

    // SEARCH MESSAGES
    public function searchMessages($keyword) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE nama LIKE :keyword 
                         OR email LIKE :keyword 
                         OR subject LIKE :keyword 
                         OR message LIKE :keyword
                      ORDER BY created_at DESC";
            
            $this->db->query($query);
            $this->db->bind('keyword', "%$keyword%");
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error searchMessages: " . $e->getMessage());
            return [];
        }
    }

    // GET RECENT MESSAGES
    public function getRecentMessages($limit = 5) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getRecentMessages: " . $e->getMessage());
            return [];
        }
    }
}
?>