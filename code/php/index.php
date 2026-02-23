<?php 
include('db.php'); 


$count_cus = $conn->query("SELECT COUNT(*) as total FROM CUS_NAME")->fetch_assoc()['total'];
$count_goods = $conn->query("SELECT COUNT(*) as total FROM GOODS_NAME")->fetch_assoc()['total'];
$count_orders = $conn->query("SELECT COUNT(*) as total FROM H_ORDER")->fetch_assoc()['total'];


$count_pending = 0; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบบริหารจัดการสินค้าคงคลัง</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <div class="top-nav">
        <div class="title">
            🏢 ระบบบริหารจัดการสินค้าคงคลัง <span class="badge-neon">Neon</span>
        </div>
        <div class="user-info">
            ผู้ใช้งาน: ผู้ดูแลระบบ (admin)
        </div>
    </div>

    <div class="main-content">
        
        <!-- Welcome Banner -->
        <div class="welcome-card">
            <h1>ยินดีต้อนรับเข้าสู่ระบบ</h1>
            <p>กรุณาเลือกเมนูที่ต้องการใช้งาน</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="number"><?php echo number_format($count_cus); ?></div>
                <div class="label">ลูกค้าทั้งหมด</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo number_format($count_goods); ?></div>
                <div class="label">รายการสินค้า</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo number_format($count_orders); ?></div>
                <div class="label">คำสั่งซื้อทั้งหมด</div>
            </div>
        </div>

        <!-- Menu Cards Grid -->
        <div class="menu-grid">
            
            <div class="menu-card blue">
                <h3>ฐานข้อมูลอ้างอิง</h3>
                <div class="menu-list">
                    <a href="customers.php" class="menu-item"><span>→</span>บันทึก/แก้ไข ข้อมูลลูกค้า</a>
                    <a href="goods.php" class="menu-item"><span>→</span>บันทึก/แก้ไข ข้อมูลสินค้า</a>
                </div>
            </div>

            <div class="menu-card blue">
                <h3>การทำงานประจำวัน</h3>
                <div class="menu-list">
                    <a href="order_list.php" class="menu-item"><span>→</span>บันทึก/แก้ไข การสั่งซื้อสินค้า</a>
                    <a href="process_master.php" class="menu-item"><span>→</span>การประมวลผลข้อมูลการสั่งสินค้า</a>
                </div>
            </div>

            <div class="menu-card blue">
                <h3>รายงาน</h3>
                <div class="menu-list">
                    <a href="report_delivery.php" class="menu-item"><span>→</span>รายงานกำหนดส่งสินค้า</a>
                </div>
            </div>

        </div>

        <div class="menu-grid" style="margin-top: 20px;">
            <div class="menu-card orange" style="grid-column: span 1;">
                <h3>ออกจากระบบ</h3>
                <div class="menu-list">
                    <a href="logout.php" class="menu-item exit"><span>→</span>ออกจากโปรแกรม</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>