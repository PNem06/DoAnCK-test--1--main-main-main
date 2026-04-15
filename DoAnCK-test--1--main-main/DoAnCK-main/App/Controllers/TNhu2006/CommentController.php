<?php
require_once __DIR__ . '/../Models/Comment.php';
require_once __DIR__ . '/../Config/database.php';

class CommentController {
    private $commentModel;
    
    public function __construct() {
        $mysqli = $this->getMysqliConnection();
        $this->commentModel = new Comment($mysqli);
    }
    
    /**
     * Nhận bình luận từ Form -> Gọi Model lưu vào DB
     */
    public function addComment() {
        // Kiểm tra phương thức request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Phương thức không hợp lệ');
        }
        
        // Lấy dữ liệu từ form
        $news_id = isset($_POST['news_id']) ? (int)$_POST['news_id'] : 0;
        $account_id = isset($_POST['account_id']) ? (int)$_POST['account_id'] : 0;
        $comment_data = isset($_POST['comment_data']) ? trim($_POST['comment_data']) : '';
        
        // Validate
        if (!$news_id || !$account_id || empty($comment_data)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            header("Location: index.php?controller=news&action=showDetail&id=$news_id");
            exit();
        }
        
        // Gọi Model lưu vào DB
        $this->commentModel->setData($comment_data);
        $this->commentModel->setDate(date('Y-m-d H:i:s'));
        $this->commentModel->setAccount($account_id);
        $this->commentModel->setNews($news_id);
        
        $result = $this->commentModel->writeComment();
        
        if ($result) {
            header("Location: index.php?controller=news&action=showDetail&id=$news_id");
            exit();
        } else {
            die('Lưu bình luận thất bại');
        }
    }
    
    /**
     * Xóa bình luận (Admin)
     */
    public function deleteComment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Phương thức không hợp lệ');
        }
        
        $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        $news_id = isset($_POST['news_id']) ? (int)$_POST['news_id'] : 0;
        
        if (!$comment_id) {
            die('ID bình luận không hợp lệ');
        }
        
        $this->commentModel->setId($comment_id);
        $result = $this->commentModel->delete();
        
        if ($result) {
            header("Location: index.php?controller=news&action=showDetail&id=$news_id");
            exit();
        } else {
            die('Xóa bình luận thất bại');
        }
    }
    
    /**
     * Lấy kết nối MySQLi
     */
    private function getMysqliConnection() {
        $config = require __DIR__ . '/../../Config/config.php';
        $mysqli = new mysqli(
            $config['db']['host'],
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['name']
        );
        
        if ($mysqli->connect_error) {
            die("Kết nối MySQLi thất bại: " . $mysqli->connect_error);
        }
        
        return $mysqli;
    }
}
