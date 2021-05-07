<?php 
    declare(strict_types=1);

    class Account {
        public const ADMIN = 'admin';

        public static $filepath = null;
        public static $accounts = null;

        private static $account = null;
        private static $isAdmin = false;

        public static function ReadAccounts(): array {
            self::$filepath = sprintf("%saccounts-%s.csv", CONFIG_DIR, explode(":", $_SERVER['HTTP_HOST'])[0]);
            $accounts = array();
            $list = self::readCsv(self::$filepath);
            foreach($list as $item) {
                $name = $item['name'];
                $role = @$item['role'];
                $acct = new Account($name, $role);
                $accounts[strtolower($name)] = $acct;
            }
            self::$accounts = $accounts;
            return $accounts;
        }
    
        private static function readCsv(string $filepath): array {
            $rows   = array_map('str_getcsv', file($filepath)); // consider fgetcsv() loop instead to process newlines in values, and to save memory
            $header = array_shift($rows);
            $countColumns = count($header);
            $csv    = array();
            $i = 0;
            foreach($rows as $row) {
                $i++;
                if (count($row) != $countColumns) { throw new Exception("panic: invalid row $i reading accounts"); }
                $csv[] = array_combine($header, $row);
            }
            return $csv;
        }
    
        public static function Set(Account $account): void { 
            self::$account = $account;
            self::$isAdmin = ($account->role == self::ADMIN);
        }

        public static function GetVid(): string {
            return self::$vid;
        }

        public static function IsAdmin(): bool {
            return self::$isAdmin;
        }

        // instance variables and methods

        public $name = null;
        public $role = null;
        
        public function __construct(string $name, ?string $role) { 
            $this->name = $name;
            $this->role = $role;
        }
    }
?>