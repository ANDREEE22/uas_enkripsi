<?php
session_start();
// Clear any existing session data when returning to index
session_unset();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Perangkat - Enkripsi/Dekripsi Realtime</title>
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
            padding: 20px;
        }
        
        .container {
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            background-color: #4a6fa5;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: #3a5a8a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .btn-perangkat1 {
            background-color: #6a8fc7;
        }
        
        .btn-perangkat2 {
            background-color: #e67e22;
        }
        
        .description {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <img src="img/hytamm.jpeg" alt="Logo Aplikasi" class="logo">
        <h1>KUCING CHAT</h1>
        <p class="description">
            Pilih perangkat yang ingin Anda gunakan untuk memulai percakapan terenkripsi.
            Perangkat 1 untuk enkripsi, Perangkat 2 untuk dekripsi.
            <br><br>
            <strong>Sandi: INDONESIA</strong>
        </p>
        
        <div class="btn-container">
            <a href="perangkat1.php" class="btn btn-perangkat1">
                <i class="fas fa-lock"></i> Perangkat 1 - Enkripsi
            </a>
            <a href="perangkat2.php" class="btn btn-perangkat2">
                <i class="fas fa-lock-open"></i> Perangkat 2 - Dekripsi
            </a>
        </div>
    </div>
</body>
</html>