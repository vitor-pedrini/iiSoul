<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe Database
 * Responsavel apenas por criar e entregar a conexao PDO com o banco MySQL.
 */
class Database
{
    private static ?PDO $conexao = null;

    // conectar ao banco usando as variaveis do .env
    public static function conectar(): PDO
    {
        if (self::$conexao === null) {
            $host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $porta   = $_ENV['DB_PORT'] ?? '3306';
            $banco   = $_ENV['DB_NAME'] ?? 'suamarca_db';
            $usuario = $_ENV['DB_USER'] ?? 'root';
            $senha   = $_ENV['DB_PASS'] ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$porta};dbname={$banco};charset={$charset}";

            try {
                self::$conexao = new PDO($dsn, $usuario, $senha, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                ]);
            } catch (PDOException $erro) {
                die('Erro ao conectar no banco de dados. Verifique o arquivo .env ou rode o comando: php migrate.php');
            }
        }

        return self::$conexao;
    }
}
