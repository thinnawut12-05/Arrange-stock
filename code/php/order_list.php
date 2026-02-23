<?php 
include('db.php'); 

// ระบบลบยกใบสั่งซื้อ
if(isset($_GET['del_order'])){
    $order_no = $_GET['del_order'];
    $conn->query("DELETE FROM D_ORDER WHERE Order_no = $order_no");
    $conn->query("DELETE FROM H_ORDER WHERE Order_no = $order_no");
    // ปรับการ Redirect ให้ส่ง parameter ไปเพื่อแจ้งเตือน
    header("Location: order_list.php?status=deleted");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>รายการสั่งซื้อสินค้า</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- เพิ่ม JS SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="header-banner">แสดงข้อมูล การสั่งซื้อสินค้า</div>
    <div class="container">
        <div class="card">
            <table>
                <thead>
                    <tr><th>รหัสลูกค้า</th><th>ชื่อลูกค้า</th><th>เลขที่สั่งซื้อ</th><th>จำนวนรายการ</th><th>จำนวนรวม</th><th colspan="2">จัดการ</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT H.Cus_id, C.Cus_name, H.Order_no, COUNT(D.Goods_id) AS CNT, SUM(D.Amount) AS TOTAL_AMOUNT 
                            FROM H_ORDER H JOIN CUS_NAME C ON H.Cus_id = C.Cus_id 
                            LEFT JOIN D_ORDER D ON H.Order_no = D.Order_no GROUP BY H.Order_no";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        echo "<tr><td>{$row['Cus_id']}</td><td>{$row['Cus_name']}</td><td>{$row['Order_no']}</td>
                        <td>".($row['CNT']?:0)."</td><td>".number_format($row['TOTAL_AMOUNT']?:0)."</td>
                        <td><a href='order_manage_detail.php?order_no={$row['Order_no']}' class='btn btn-edit'>แก้ไข</a></td>
                        <td><a href='#' class='btn btn-del' onclick='confirmDelete({$row['Order_no']})'>ลบ</a></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <a href="order_add_header.php" class="btn btn-add">เพิ่มข้อมูลการสั่งซื้อ</a>
            <a href="index.php" class="btn btn-edit">กลับหน้าหลัก</a>
        </div>
    </div>

    <script>
        // 1. ฟังก์ชันยืนยันการลบแบบสวยงาม
        function confirmDelete(orderNo) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบใบสั่งซื้อเลขที่ " + orderNo + " ใช่หรือไม่? ข้อมูลทั้งหมดจะหายไป",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?del_order=' + orderNo;
                }
            })
        }

        // 2. ตรวจสอบสถานะหลังการลบเพื่อแสดงข้อความสำเร็จ
        <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            Swal.fire({
                icon: 'success',
                title: 'ลบรายการสำเร็จ',
                text: 'ข้อมูลใบสั่งซื้อถูกลบออกจากระบบแล้ว',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // ล้าง parameter บน URL เพื่อไม่ให้แจ้งเตือนซ้ำเมื่อ refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        <?php endif; ?>
    </script>
</body>
</html>