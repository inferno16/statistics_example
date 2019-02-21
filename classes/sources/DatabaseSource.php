<?php
include_once 'classes/Database.php';

class DatabaseSource extends Source
{
    private $db;
    public function __construct() {
        $config = parse_ini_file('configs/db.ini');
        $db = new Database($config);
    }

    public function GetVisitorsCount() : int {
        return $this->GetVisitorsFromDB();
    }

    private function GetVisitorsFromDB() : int {
        $visitors = -1;
        if(!$this->db || !$this->db->ok())
            return $visitors;
        $stmt = $this->db->execute("SELECT COUNT(DISTINCT(`userID`)) FROM `vistors`");
        if($stmt) {
            $data = $stmt->fetch(PDO::FETCH_NUM);
            if($data[0])
                $visitors = $data[0];
        }
        return $visitors;
    }
}
?>