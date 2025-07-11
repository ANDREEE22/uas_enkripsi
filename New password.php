<?php
session_start();
$device = isset($_GET['device']) ? $_GET['device'] : '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) ? strtoupper(trim($_POST['password'])) : '';
    
    if ($password === 'INDONESIA') {
        $_SESSION['logged_in'] = true;
        header('Location: perangkat'.$device.'.php');
        exit();
    } else {
        $error = "Sandi salah! Coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan Sandi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .password-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        p {
            color: #666;
            margin-bottom: 20px;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        button {
            background-color: #4a6fa5;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }
        
        button.cancel {
            background-color: #e74c3c;
        }
        
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="password-box">
        <h3>Masukkan Sandi</h3>
        <p>Sandi: "INDONESIA" (huruf kapital)</p>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="password" id="passwordInput" name="password" placeholder="Masukkan sandi" required>
            <button type="submit">Submit</button>
            <button type="button" class="cancel" onclick="window.location.href='index.php'">Batal</button>
        </form>
    </div>
</body>
</html>