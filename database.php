<?php
class Database {
    private $pdo;
    
    /**
     * Constructor for the Database class.
     *
     * @param PDO $pdo The PDO object to use for database connections.
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Prepares and executes an SQL query.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params Optional parameters to bind to the query.
     * @return PDOStatement The executed statement.
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Executes an SQL query and fetches a single row.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params Optional parameters to bind to the query.
     * @return array|false The fetched row as an associative array, or false if no row is found.
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    /**
     * Executes an SQL query and fetches all rows.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params Optional parameters to bind to the query.
     * @return array An array of associative arrays representing the fetched rows.
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Executes an SQL query and fetches a single column from the first row.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params Optional parameters to bind to the query.
     * @param int $column The column number to fetch (default is 0).
     * @return mixed The value of the specified column, or false if no row is found.
     */
    public function fetchColumn($sql, $params = [], $column = 0) {
        return $this->query($sql, $params)->fetchColumn($column);
    }
    
    /**
     * Inserts a new row into the specified table.
     *
     * @param string $table The name of the table to insert into.
     * @param array $data An associative array of column names and values to insert.
     * @return string The ID of the newly inserted row.
     */
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
    
    /**
     * Updates rows in the specified table.
     *
     * @param string $table The name of the table to update.
     * @param array $data An associative array of column names and new values.
     * @param string $where The WHERE clause to specify which rows to update.
     * @param array $whereParams Optional parameters to bind to the WHERE clause.
     * @return int The number of rows affected by the update.
     */
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
    
    /**
     * Deletes rows from the specified table.
     *
     * @param string $table The name of the table to delete from.
     * @param string $where The WHERE clause to specify which rows to delete.
     * @param array $params Optional parameters to bind to the WHERE clause.
     * @return int The number of rows affected by the delete.
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }
    
    /**
     * Begins a database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commits the current database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Rolls back the current database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function rollBack() {
        return $this->pdo->rollBack();
    }
    
    /**
     * Fetches a thread by its ID, including the username and category name.
     *
     * @param int $id The ID of the thread to fetch.
     * @return array|false The thread data as an associative array, or false if not found.
     */
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
    
    /**
     * Fetches threads for a specific category, with pagination.
     *
     * @param int $categoryId The ID of the category.
     * @param int $limit The number of threads to fetch.
     * @param int $offset The offset for pagination.
     * @return array An array of threads as associative arrays.
     */
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
    
    /**
     * Fetches recent threads across all categories, with pagination.
     *
     * @param int $limit The number of threads to fetch.
     * @param int $offset The offset for pagination.
     * @return array An array of threads as associative arrays, including username and category name.
     */
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
    
    /**
     * Fetches all replies for a specific thread, including the username.
     *
     * @param int $threadId The ID of the thread.
     * @return array An array of replies as associative arrays.
     */
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
    
    /**
     * Fetches a user by their username.
     *
     * @param string $username The username to search for.
     * @return array|false The user data as an associative array, or false if not found.
     */
    public function getUserByUsername($username) {
        return $this->fetch(
            "SELECT * FROM users WHERE username = ?",
            [$username]
        );
    }
    
    /**
     * Creates a new thread.
     *
     * @param int $userId The ID of the user creating the thread.
     * @param int $categoryId The ID of the category for the thread.
     * @param string $title The title of the thread.
     * @param string $body The body content of the thread.
     * @return string The ID of the newly created thread.
     */
    public function createThread($userId, $categoryId, $title, $body) {
        return $this->insert('threads', [
            'user_id' => $userId,
            'category_id' => $categoryId,
            'title' => $title,
            'body' => $body
        ]);
    }
    
    /**
     * Creates a new reply to a thread.
     *
     * @param int $threadId The ID of the thread to reply to.
     * @param int $userId The ID of the user creating the reply.
     * @param string $body The body content of the reply.
     * @return string The ID of the newly created reply.
     */
    public function createReply($threadId, $userId, $body) {
        return $this->insert('replies', [
            'thread_id' => $threadId,
            'user_id' => $userId,
            'body' => $body
        ]);
    }
    
    /**
     * Creates a new user.
     *
     * @param string $username The username of the new user.
     * @param string $passwordHash The hashed password of the new user.
     * @return string The ID of the newly created user.
     */
    public function createUser($username, $passwordHash) {
        return $this->insert('users', [
            'username' => $username,
            'password' => $passwordHash
        ]);
    }
    
    /**
     * Fetches all categories, ordered by name.
     *
     * @return array An array of categories as associative arrays.
     */
    public function getCategories() {
        return $this->fetchAll("SELECT * FROM categories ORDER BY name");
    }
    
    /**
     * Fetches a category by its ID.
     *
     * @param int $id The ID of the category to fetch.
     * @return array|false The category data as an associative array, or false if not found.
     */
    public function getCategoryById($id) {
        return $this->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }
    
    /**
     * Counts the total number of threads.
     *
     * @return int The total number of threads.
     */
    public function countThreads() {
        return $this->fetchColumn("SELECT COUNT(*) FROM threads");
    }
    
    /**
     * Counts the number of threads in a specific category.
     *
     * @param int $categoryId The ID of the category.
     * @return int The number of threads in the category.
     */
    public function countThreadsByCategory($categoryId) {
        return $this->fetchColumn(
            "SELECT COUNT(*) FROM threads WHERE category_id = ?",
            [$categoryId]
        );
    }
    /**
     * Fetches replies for a specific thread with pagination.
     *
     * @param int $threadId The ID of the thread.
     * @param int $limit The number of replies to fetch.
     * @param int $offset The offset for pagination.
     * @return array An array of replies as associative arrays.
     */
    public function getRepliesByThreadPaginated($threadId, $limit, $offset) {
        return $this->fetchAll(
            "SELECT r.*, u.username
             FROM replies r
             JOIN users u ON r.user_id = u.id
             WHERE r.thread_id = ?
             ORDER BY r.created_at
             LIMIT ? OFFSET ?",
            [$threadId, $limit, $offset]
        );
    }
    
    /**
     * Counts the number of replies for a specific thread.
     *
     * @param int $threadId The ID of the thread.
     * @return int The number of replies.
     */
    public function countRepliesByThread($threadId) {
        return $this->fetchColumn(
            "SELECT COUNT(*) FROM replies WHERE thread_id = ?",
            [$threadId]
        );
    }
}
