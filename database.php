<?php
class Database {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchColumn($sql, $params = [], $column = 0) {
        return $this->query($sql, $params)->fetchColumn($column);
    }
    
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $set),
            $where
        );
        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }
    
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollBack() {
        return $this->pdo->rollBack();
    }
    
    public function getThread($id) {
        return $this->fetch(
            "SELECT t.*, u.username, c.name as category_name
             FROM threads t
             JOIN users u ON t.user_id = u.id
             JOIN categories c ON t.category_id = c.id
             WHERE t.id = ?",
            [$id]
        );
    }
    
    public function getThreadsByCategory($categoryId, $limit, $offset) {
        return $this->fetchAll(
            "SELECT t.*, u.username
             FROM threads t
             JOIN users u ON t.user_id = u.id
             WHERE t.category_id = ?
             ORDER BY t.created_at DESC
             LIMIT ? OFFSET ?",
            [$categoryId, $limit, $offset]
        );
    }
    
    public function getRecentThreads($limit, $offset) {
        return $this->fetchAll(
            "SELECT t.*, u.username, c.name as category_name
             FROM threads t
             JOIN users u ON t.user_id = u.id
             JOIN categories c ON t.category_id = c.id
             ORDER BY t.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }
    
    public function getRepliesByThread($threadId) {
        return $this->fetchAll(
            "SELECT r.*, u.username
             FROM replies r
             JOIN users u ON r.user_id = u.id
             WHERE r.thread_id = ?
             ORDER BY r.created_at",
            [$threadId]
        );
    }
    
    public function getUserByUsername($username) {
        return $this->fetch(
            "SELECT * FROM users WHERE username = ?",
            [$username]
        );
    }
    
    public function createThread($userId, $categoryId, $title, $body) {
        return $this->insert('threads', [
            'user_id' => $userId,
            'category_id' => $categoryId,
            'title' => $title,
            'body' => $body
        ]);
    }
    
    public function createReply($threadId, $userId, $body) {
        return $this->insert('replies', [
            'thread_id' => $threadId,
            'user_id' => $userId,
            'body' => $body
        ]);
    }
    
    public function createUser($username, $passwordHash) {
        return $this->insert('users', [
            'username' => $username,
            'password' => $passwordHash
        ]);
    }
    
    public function getCategories() {
        return $this->fetchAll("SELECT * FROM categories ORDER BY name");
    }
    
    public function getCategoryById($id) {
        return $this->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }
    
    public function countThreads() {
        return $this->fetchColumn("SELECT COUNT(*) FROM threads");
    }
    
    public function countThreadsByCategory($categoryId) {
        return $this->fetchColumn(
            "SELECT COUNT(*) FROM threads WHERE category_id = ?",
            [$categoryId]
        );
    }
}
