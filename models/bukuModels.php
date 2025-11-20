<!-- Data buku  -->
 <?php
// bukuModels.php
require_once  __DIR__ . '/../config/database.php';

class BukuModel {
    private $db;
    private $table = 'buku';

    public function __construct() {
        $this->db = new Database();
    }

    // GET ALL BOOKS
    public function getAllBooks() {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.status = 'active'
                      ORDER BY b.created_at DESC";
            
            $this->db->query($query);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getAllBooks: " . $e->getMessage());
            return [];
        }
    }

    // GET BOOK BY ID
    public function getBookById($id) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.id = :id AND b.status = 'active'";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            return $this->db->single();
        } catch (PDOException $e) {
            error_log("Error getBookById: " . $e->getMessage());
            return false;
        }
    }

    // GET BOOKS BY CATEGORY
    public function getBooksByCategory($kategori_id) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.kategori_id = :kategori_id AND b.status = 'active'
                      ORDER BY b.judul ASC";
            
            $this->db->query($query);
            $this->db->bind('kategori_id', $kategori_id);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getBooksByCategory: " . $e->getMessage());
            return [];
        }
    }

    // GET BOOKS BY AUTHOR
    public function getBooksByAuthor($penulis_id) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.penulis_id = :penulis_id AND b.status = 'active'
                      ORDER BY b.judul ASC";
            
            $this->db->query($query);
            $this->db->bind('penulis_id', $penulis_id);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getBooksByAuthor: " . $e->getMessage());
            return [];
        }
    }

    // SEARCH BOOKS
    public function searchBooks($keyword) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE (b.judul LIKE :keyword 
                         OR b.deskripsi LIKE :keyword 
                         OR p.nama_penulis LIKE :keyword)
                         AND b.status = 'active'
                      ORDER BY b.judul ASC";
            
            $this->db->query($query);
            $this->db->bind('keyword', "%$keyword%");
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error searchBooks: " . $e->getMessage());
            return [];
        }
    }

    // GET POPULAR BOOKS
    public function getPopularBooks($limit = 8) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis, 
                             COUNT(f.buku_id) as total_favorit
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      LEFT JOIN buku_favorit f ON b.id = f.buku_id
                      WHERE b.status = 'active'
                      GROUP BY b.id
                      ORDER BY total_favorit DESC, b.download_count DESC
                      LIMIT :limit";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getPopularBooks: " . $e->getMessage());
            return [];
        }
    }

    // GET RECENT BOOKS
    public function getRecentBooks($limit = 8) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.status = 'active'
                      ORDER BY b.created_at DESC
                      LIMIT :limit";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getRecentBooks: " . $e->getMessage());
            return [];
        }
    }

    // GET RECOMMENDED BOOKS (for homepage)
    public function getRecommendedBooks($limit = 6) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.status = 'active' AND b.is_recommended = 1
                      ORDER BY b.created_at DESC
                      LIMIT :limit";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getRecommendedBooks: " . $e->getMessage());
            return [];
        }
    }

    // ADD NEW BOOK
    public function addBook($data) {
        try {
            $query = "INSERT INTO {$this->table} 
                      (judul, deskripsi, penulis_id, kategori_id, file_path, cover_path, 
                       tahun_terbit, isbn, halaman, bahasa, publisher, status) 
                      VALUES 
                      (:judul, :deskripsi, :penulis_id, :kategori_id, :file_path, :cover_path, 
                       :tahun_terbit, :isbn, :halaman, :bahasa, :publisher, 'active')";
            
            $this->db->query($query);
            
            // Bind values
            $this->db->bind('judul', $data['judul']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('penulis_id', $data['penulis_id']);
            $this->db->bind('kategori_id', $data['kategori_id']);
            $this->db->bind('file_path', $data['file_path']);
            $this->db->bind('cover_path', $data['cover_path']);
            $this->db->bind('tahun_terbit', $data['tahun_terbit']);
            $this->db->bind('isbn', $data['isbn']);
            $this->db->bind('halaman', $data['halaman']);
            $this->db->bind('bahasa', $data['bahasa']);
            $this->db->bind('publisher', $data['publisher']);

            // Execute
            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error addBook: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE BOOK
    public function updateBook($data) {
        try {
            $query = "UPDATE {$this->table} 
                      SET judul = :judul, 
                          deskripsi = :deskripsi, 
                          penulis_id = :penulis_id, 
                          kategori_id = :kategori_id, 
                          file_path = :file_path, 
                          cover_path = :cover_path, 
                          tahun_terbit = :tahun_terbit, 
                          isbn = :isbn, 
                          halaman = :halaman, 
                          bahasa = :bahasa, 
                          publisher = :publisher,
                          updated_at = CURRENT_TIMESTAMP
                      WHERE id = :id";
            
            $this->db->query($query);
            
            // Bind values
            $this->db->bind('id', $data['id']);
            $this->db->bind('judul', $data['judul']);
            $this->db->bind('deskripsi', $data['deskripsi']);
            $this->db->bind('penulis_id', $data['penulis_id']);
            $this->db->bind('kategori_id', $data['kategori_id']);
            $this->db->bind('file_path', $data['file_path']);
            $this->db->bind('cover_path', $data['cover_path']);
            $this->db->bind('tahun_terbit', $data['tahun_terbit']);
            $this->db->bind('isbn', $data['isbn']);
            $this->db->bind('halaman', $data['halaman']);
            $this->db->bind('bahasa', $data['bahasa']);
            $this->db->bind('publisher', $data['publisher']);

            // Execute
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error updateBook: " . $e->getMessage());
            return false;
        }
    }

    // DELETE BOOK (soft delete)
    public function deleteBook($id) {
        try {
            $query = "UPDATE {$this->table} SET status = 'inactive' WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error deleteBook: " . $e->getMessage());
            return false;
        }
    }

    // INCREMENT DOWNLOAD COUNT
    public function incrementDownloadCount($id) {
        try {
            $query = "UPDATE {$this->table} SET download_count = download_count + 1 WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error incrementDownloadCount: " . $e->getMessage());
            return false;
        }
    }

    // INCREMENT VIEW COUNT
    public function incrementViewCount($id) {
        try {
            $query = "UPDATE {$this->table} SET view_count = view_count + 1 WHERE id = :id";
            
            $this->db->query($query);
            $this->db->bind('id', $id);
            
            return $this->db->execute();
        } catch (PDOException $e) {
            error_log("Error incrementViewCount: " . $e->getMessage());
            return false;
        }
    }

    // GET BOOK COUNT
    public function getBookCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'active'";
            
            $this->db->query($query);
            $result = $this->db->single();
            return $result->total;
        } catch (PDOException $e) {
            error_log("Error getBookCount: " . $e->getMessage());
            return 0;
        }
    }

    // GET BOOKS WITH PAGINATION
    public function getBooksWithPagination($limit, $offset) {
        try {
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.status = 'active'
                      ORDER BY b.created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $this->db->query($query);
            $this->db->bind('limit', $limit);
            $this->db->bind('offset', $offset);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getBooksWithPagination: " . $e->getMessage());
            return [];
        }
    }

    // GET BOOKS BY MULTIPLE CATEGORIES
    public function getBooksByMultipleCategories($kategori_ids) {
        try {
            $placeholders = implode(',', array_fill(0, count($kategori_ids), '?'));
            $query = "SELECT b.*, k.nama_kategori, p.nama_penulis 
                      FROM {$this->table} b
                      LEFT JOIN kategori k ON b.kategori_id = k.id
                      LEFT JOIN penulis p ON b.penulis_id = p.id
                      WHERE b.kategori_id IN ($placeholders) AND b.status = 'active'
                      ORDER BY b.judul ASC";
            
            $this->db->query($query);
            for ($i = 0; $i < count($kategori_ids); $i++) {
                $this->db->bind(($i + 1), $kategori_ids[$i]);
            }
            return $this->db->resultSet();
        } catch (PDOException $e) {
            error_log("Error getBooksByMultipleCategories: " . $e->getMessage());
            return [];
        }
    }
}
?>