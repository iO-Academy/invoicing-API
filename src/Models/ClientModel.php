<?php

namespace Invoices\Models;

class ClientModel
{
    private \PDO $db;

    /**
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getClients(): array
    {
        $query = $this->db->query('SELECT * FROM `clients`');
        return $query->fetchAll();
    }

    public function getClientById(int $id): array
    {
        $query = $this->db->prepare('SELECT * FROM `clients` WHERE `id` = ?');
        $query->execute([$id]);
        return $query->fetch();
    }
}