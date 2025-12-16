<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
header('Content-Type: application/json; charset=utf-8');

$db_host = 'localhost';
$db_user = 'aaaaaa';
$db_pass = 'aaaaaa';
$db_name = 'aaaaaa';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'code' => 500,
        'msg' => '数据库连接失败：' . $e->getMessage(),
        'data' => null
    ]);
    exit;
}

function encryptPassword($password) {
    $reversed_pwd = strrev($password);
    $custom_key = 'api_token_key_2025_secure';
    $mix_pwd = $reversed_pwd . '_' . $custom_key . '_' . md5($password . $custom_key);
    $hash_pwd = hash('sha256', $mix_pwd);
    $final_pwd = substr($hash_pwd, 8, 24) . '-' . substr($hash_pwd, 0, 8) . '-' . substr($hash_pwd, 32);
    return $final_pwd;
}

function generateUniqueToken($pdo) {
    do {
        $random_str = bin2hex(random_bytes(16)) . rand(10000000, 99999999);
        $token = hash('sha1', $random_str);
        $stmt = $pdo->prepare("SELECT TOKEN FROM users WHERE TOKEN = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    } while ($exists);
    return $token;
}

$params = $_POST ?: $_GET;
$action = isset($params['action']) ? trim($params['action']) : '';

switch ($action) {
    case 'register':
        $required_fields = ['username', 'password', 'repassword'];
        foreach ($required_fields as $field) {
            if (!isset($params[$field]) || empty(trim($params[$field]))) {
                echo json_encode([
                    'code' => 400,
                    'msg' => $field . '为空参数！',
                    'data' => null
                ]);
                exit;
            }
        }
        $username = trim($params['username']);
        $password = trim($params['password']);
        $repassword = trim($params['repassword']);

        if ($password !== $repassword) {
            echo json_encode([
                'code' => 400,
                'msg' => '两次密码不一致！',
                'data' => null
            ]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT Username FROM users WHERE Username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode([
                'code' => 400,
                'msg' => '用户名已存在',
                'data' => null
            ]);
            exit;
        }

        $encrypted_pwd = encryptPassword($password);
        $token = generateUniqueToken($pdo);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (Username, Password, TOKEN) VALUES (:username, :password, :token)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $encrypted_pwd);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $uid = $pdo->lastInsertId();

            echo json_encode([
                'code' => 200,
                'msg' => '注册成功',
                'data' => [
                    'UID' => $uid,
                    'Username' => $username,
                    'TOKEN' => $token
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'code' => 500,
                'msg' => '注册失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
        break;

    case 'login':
        $has_token = isset($params['token']) && !empty(trim($params['token']));
        $has_user_pwd = isset($params['username']) && !empty(trim($params['username'])) && isset($params['password']) && !empty(trim($params['password']));

        if (!$has_token && !$has_user_pwd) {
            echo json_encode([
                'code' => 400,
                'msg' => '请输入用户名与密码。或使用TOKEN登录',
                'data' => null
            ]);
            exit;
        }

        if ($has_token) {
            $token = trim($params['token']);
            $stmt = $pdo->prepare("SELECT UID, Username, TOKEN FROM users WHERE TOKEN = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo json_encode([
                    'code' => 401,
                    'msg' => 'TOKEN 无效！',
                    'data' => null
                ]);
            } else {
                echo json_encode([
                    'code' => 200,
                    'msg' => '登录成功',
                    'data' => [
                        'UID' => $user['UID'],
                        'Username' => $user['Username'],
                        'TOKEN' => $user['TOKEN']
                    ]
                ]);
            }
        } else {
            $username = trim($params['username']);
            $password = trim($params['password']);

            $stmt = $pdo->prepare("SELECT UID, Username, Password, TOKEN FROM users WHERE Username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo json_encode([
                    'code' => 401,
                    'msg' => '用户名不存在',
                    'data' => null
                ]);
            } elseif (encryptPassword($password) !== $user['Password']) {
                echo json_encode([
                    'code' => 401,
                    'msg' => '密码错误',
                    'data' => null
                ]);
            } else {
                echo json_encode([
                    'code' => 200,
                    'msg' => '登录成功',
                    'data' => [
                        'UID' => $user['UID'],
                        'Username' => $user['Username'],
                        'TOKEN' => $user['TOKEN']
                    ]
                ]);
            }
        }
        break;

    default:
        echo json_encode([
            'code' => 400,
            'msg' => '无效的操作类型 [action:login/register]',
            'data' => null
        ]);
        break;
}
?>