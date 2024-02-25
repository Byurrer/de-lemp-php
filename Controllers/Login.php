<?php

namespace App\Controllers;

use Exception;
use GuzzleHttp\Psr7\Response;
use PDO;
use PDOException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Login
{
    public function __construct()
    {}

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function action(RequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            if ($this->auth($data['username'], $data['password'])) {
                $body = [
                    'success' => true
                ];
                return new Response(200, [], json_encode($body, JSON_UNESCAPED_UNICODE));
            }
        } catch(Exception $e) {
            $body = [
                'message' => $e->getMessage()
            ];
            return new Response(500, [], json_encode($body, JSON_UNESCAPED_UNICODE));
        }

        $body = [
            'message' => 'Неверный логин или пароль'
        ];
        return new Response(400, [], json_encode($body, JSON_UNESCAPED_UNICODE));
    }

    private function auth(string $username, string $password): array|false {
        $host = 'db'; // Имя хоста
        $db = 'lemp'; // Имя базы данных
        $user = 'root'; // Имя пользователя базы данных
        $pass = 'root'; // Пароль пользователя базы данных

        // Подключение к базе данных с использованием PDO
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

        // Устанавливаем режим обработки ошибок PDO на исключения
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Подготовка SQL-запроса
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");

        // Выполнение подготовленного запроса с передачей параметров
        $stmt->execute(['login' => $username]);

        // Получение результата запроса в виде ассоциативного массива
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверка наличия пользователя и соответствия пароля
        if ($user && $user['password'] == md5($password)) {
            // Пользователь найден и пароль совпадает
            return $user;
        } else {
            // Неправильное имя пользователя или пароль
            return false;
        }
    }
}
