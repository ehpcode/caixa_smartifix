<?php
namespace App\Core;

class Model {
    public $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
}
