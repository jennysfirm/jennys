<?php

class JennysPdo extends \PDO
{
    /**
     * @var bool
     */
    private $killOnError = true;
    /**
     * @var bool
     */
    private $echoOnError = false;

    public function __construct(string $host, string $user, string $pass, string $db, string $charset = "utf8")
    {
        parent::__construct("mysql:host={$host};dbname={$db};charset={$charset}", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function run(string $query, array $values = []): PDOStatement
    {
        try {
            if (empty($values)) {
                $stmt = $this->prepare($query);
                $stmt->execute($values);
            } else {
                $stmt = $this->query($query);
            }
        } catch (PDOException $e) {
            $this->handleError($e->getMessage()); // if you die, you will lose the stack trace?
            throw  $e;
        }
        return $stmt;
    }

    private function handleError(string $error): void
    {
        error_log($error);
        if ($this->echoOnError) echo $error;
        if ($this->killOnError) die($error);
    }
}
