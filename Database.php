<?php
namespace Classes;

use PDO;

class Database {

    private $conn;
    public function __construct() {
        require_once 'Config.php';
        $this->conn = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
    }

    public function getData(array $numbers):array {

        if(count($numbers) > MAX) {
            return ['Error' => 1];
        }
        $sql = "
        CREATE TEMPORARY TABLE TempNumbers (
            number varchar(18) PRIMARY KEY
        )";
        $this->conn->query($sql);
        $numbersChunk = array_chunk($numbers, 20);
        foreach ($numbersChunk as $numbers) {
            $nums = [];
            $bind = [];
            foreach ($numbers as $key => $value) {
                $nums[":Key$key"] = $value;
                $bind[] = '(' . $value . ')';
            }
            $insert = $this->conn->prepare("INSERT IGNORE INTO TempNumbers(number) VALUES" . implode(', ', $bind));
            foreach ($nums as $key => $value) {
                $insert->bindValue($key, $value, PDO::PARAM_STR);
            }
            $insert->execute();
        }
        
        $query = "
         SELECT 
            Test.Number,
            Test.Description
         FROM
            Test
            
            INNER JOIN TempNumbers
            ON TempNumbers.Number = Test.Number
        ";
        $products = $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
        $this->conn->query("DROP TEMPORARY TABLE TempNumbers")->execute();
        if(!$products) {
            return ['Error' => 2];
        }

        return $products;

    }

    public function firstStart() {
        $sql = "
        CREATE TABLE IF NOT EXISTS `Test` (
            `Number` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
            `Description` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`Number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ";
        $this->conn->query($sql)->execute();
        if (!$this->conn->query("SELECT Number FROM Test LIMIT 1")->fetchColumn()) {
            $numbers = [];
            for ($try = 1; $try <= 3000; $try++) {
                $numbers[] = random_int('1000000000', '9999999999');
            }
            $forInsertNumber = [];
            $forInsertDescription = [];
            $bind = [];
            foreach ($numbers as $key => $value) {
                $forInsertNumber[":Number$key"] = $value;
                $forInsertDescription[":Description$key"] = "Description to $value";
                $bind[] = "(:Number$key, :Description$key)";
            }
            $insert = $this->conn->prepare("INSERT INTO Test(Number, Description) VALUES" . implode(', ', $bind));
            foreach ($forInsertNumber as $key => $value) {
                $insert->bindValue($key, $value, PDO::PARAM_STR);
            }
            foreach ($forInsertDescription as $key => $value) {
                $insert->bindValue($key, $value, PDO::PARAM_STR);
            }
            $insert->execute();

        }
    }
}
