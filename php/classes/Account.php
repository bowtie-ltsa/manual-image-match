<?php 
    declare(strict_types=1);

    class Account {
        public const ADMIN = 'admin';

        private static $account = null;
        private static $isAdmin = false;

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