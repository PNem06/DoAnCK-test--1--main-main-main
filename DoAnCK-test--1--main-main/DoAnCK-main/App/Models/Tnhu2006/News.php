<?php
class News {
    private $conn;

    public function __construct(mysqli $db){
        $this->conn = $db;
    }

    // =========================
    // LATEST NEWS
    // =========================
    public function getLatest($limit){
        $stmt = $this->conn->prepare("CALL sp_GetLatestNews(?)");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    // =========================
    // GET BY ID
    // =========================
    public function getById($id){
        $stmt = $this->conn->prepare("CALL sp_GetNewsById(?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // =========================
    // INCREASE VIEW
    // =========================
    public function increaseView($id){
        $stmt = $this->conn->prepare("CALL sp_IncreaseNewsView(?)");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // =========================
    // COMMENTS
    // =========================
    public function getComments($news_id){
        $stmt = $this->conn->prepare("CALL sp_GetCommentsByNews(?)");
        $stmt->bind_param("i", $news_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // =========================
    // RELATED NEWS (3 PARAMS - FIXED)
    // =========================
    public function getRelated($newsId, $category, $limitNum){
        $stmt = $this->conn->prepare("CALL sp_GetRelatedNews(?, ?, ?)");
        $stmt->bind_param("isi", $newsId, $category, $limitNum);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>