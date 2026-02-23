<?php
session_start();

/* ล้าง session ทั้งหมด */
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ออกจากระบบแล้ว</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            font-family: 'Kanit', Arial, sans-serif;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logout-box {
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            text-align: center;
            width: 360px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .logout-box h1 {
            margin: 0 0 10px;
            color: #1f2933;
            font-size: 26px;
        }

        .logout-box p {
            color: #6b7280;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .logout-box a {
            display: inline-block;
            padding: 12px 24px;
            background: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            transition: background 0.3s, transform 0.2s;
        }

        .logout-box a:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="logout-box">
    <h1>ออกจากระบบเรียบร้อย</h1>
    <p>คุณได้ออกจากระบบแล้ว<br>ขอบคุณที่ใช้งานระบบ</p>
    <a href="index.php">เข้าสู่ระบบอีกครั้ง</a>
</div>

</body>
</html>
