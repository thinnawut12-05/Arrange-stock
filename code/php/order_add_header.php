<?php 
include('db.php'); 

if(isset($_POST['save_header'])){
    $cus_id = $_POST['Cus_id'];
    $order_date = $_POST['Order_Date'];
    
    $sql = "INSERT INTO H_ORDER (Cus_id, Order_Date) VALUES ('$cus_id', '$order_date')";
    if($conn->query($sql)){
        $new_id = $conn->insert_id;
        header("Location: order_manage_detail.php?order_no=$new_id");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>เพิ่มรายการสั่งซื้อ</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="header-banner">บันทึก/แก้ไข การสั่งซื้อสินค้า (ส่วน Header)</div>
    <div class="container" style="max-width: 600px;">
        <form method="POST">
            <div class="form-group">
                <label>รหัสลูกค้า:</label>
                <select name="Cus_id" required>
                    <option value="">-- เลือกลูกค้า --</option>
                    <?php
                    $cus = $conn->query("SELECT * FROM CUS_NAME");
                    while($c = $cus->fetch_assoc()) echo "<option value='{$c['Cus_id']}'>{$c['Cus_id']} - {$c['Cus_name']}</option>";
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>วันที่สั่งสินค้า:</label>
                <input type="date" name="Order_Date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div style="text-align: center;">
                <button type="submit" name="save_header" class="btn btn-add">บันทึกและเพิ่มรายการสินค้าต่อ</button>
                <a href="order_list.php" class="btn btn-del">ยกเลิก</a>
            </div>
        </form>
    </div>
</body>
</html>