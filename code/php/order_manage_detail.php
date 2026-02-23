<?php 
include('db.php'); 

// รับเลขที่สั่งซื้อจาก URL
$order_no = isset($_GET['order_no']) ? $_GET['order_no'] : '';

if(empty($order_no)) {
    header("Location: order_list.php");
    exit();
}

$status_action = "";

// 1. ระบบบันทึก/แก้ไข รายการสินค้า (Detail)
if(isset($_POST['add_item'])){
    $g_id = $_POST['Goods_id'];
    $amount = $_POST['Amount'];
    $ord_date = $_POST['Ord_date'];
    // รับค่าวันที่ส่งจริง (Fin_date)
    $fin_date = !empty($_POST['Fin_date']) ? "'" . $_POST['Fin_date'] . "'" : "NULL";
    
    // ดึงราคาจากฐานข้อมูลเพื่อความปลอดภัย
    $g_query = $conn->query("SELECT cost_unit FROM GOODS_NAME WHERE Goods_id = '$g_id'");
    $g_info = $g_query->fetch_assoc();
    
    $price = $g_info['cost_unit'];
    $total = $price * $amount;

    $sql = "REPLACE INTO D_ORDER (Order_no, Goods_id, Ord_date, Fin_date, Amount, COST_UNIT, TOT_PRC) 
            VALUES ($order_no, '$g_id', '$ord_date', $fin_date, $amount, $price, $total)";
    
    if($conn->query($sql)){
        $status_action = "added";
    }
}

// 2. ระบบลบรายการสินค้า
if(isset($_GET['del_item'])){
    $gid = $_GET['del_item'];
    $conn->query("DELETE FROM D_ORDER WHERE Order_no = $order_no AND Goods_id = '$gid'");
    header("Location: order_manage_detail.php?order_no=$order_no&status=deleted");
    exit();
}

$h_sql = "SELECT H.*, C.Cus_name FROM H_ORDER H JOIN CUS_NAME C ON H.Cus_id = C.Cus_id WHERE Order_no = $order_no";
$header = $conn->query($h_sql)->fetch_assoc();

if(isset($_GET['status']) && $_GET['status'] == 'deleted') {
    $status_action = "deleted";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการใบสั่งซื้อ #<?php echo $order_no; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="header-banner">บันทึก/แก้ไข รายการสินค้าในใบสั่งซื้อ</div>

    <div class="container">
        <!-- ข้อมูล Header -->
        <div class="card" style="background: #f8fafc; border-left: 5px solid #6366f1; padding: 15px 25px;">
            <div style="font-size: 14px;">
                <strong>เลขที่สั่งซื้อ:</strong> <?php echo $order_no; ?> | 
                <strong>ลูกค้า:</strong> <?php echo $header['Cus_name']; ?> | 
                <strong>วันที่สั่ง:</strong> <?php echo $header['Order_Date']; ?> |
            </div>
        </div>

        <!-- ฟอร์มเพิ่มสินค้า (ช่องสีนวล) - มีคำนวณ JS และ ปฏิทินวันส่งจริง -->
        <div class="card" style="background: #fffbeb; border: 1px solid #fef3c7;">
            <form method="POST" id="orderForm">
                <div class="form-grid" style="display: grid; grid-template-columns: 2fr 0.8fr 0.8fr 1fr 1fr 1fr auto; gap: 10px; align-items: flex-end;">
                    <div>
                        <label>เลือกสินค้า:</label>
                        <select name="Goods_id" id="Goods_id" required onchange="calculateTotal()">
                            <option value="" data-price="0">-- เลือกสินค้า --</option>
                            <?php 
                            $goods = $conn->query("SELECT * FROM GOODS_NAME ORDER BY Goods_name ASC");
                            while($g = $goods->fetch_assoc()) {
                                echo "<option value='{$g['Goods_id']}' data-price='{$g['cost_unit']}'>{$g['Goods_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label>ราคา/หน่วย:</label>
                        <input type="text" id="view_price" readonly style="background:#f1f5f9; text-align:right;" placeholder="0.00">
                    </div>
                    <div>
                        <label>จำนวน:</label>
                        <input type="number" name="Amount" id="Amount" required min="1" value="1" oninput="calculateTotal()">
                    </div>
                    <div>
                        <label>ราคารวม:</label>
                        <input type="text" id="view_total" readonly style="background:#f1f5f9; font-weight:bold; text-align:right; color:#6366f1;" placeholder="0.00">
                    </div>
                    <div>
                        <label>กำหนดส่ง:</label>
                        <input type="date" name="Ord_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label>วันที่ส่งจริง:</label>
                        <input type="date" name="Fin_date">
                    </div>
                    <div>
                        <button type="submit" name="add_item" class="btn btn-add" style="background:#6366f1; height:42px; width:100%;">เพิ่มรายการ</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ตารางรายการสินค้า -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th style="text-align: left;">สินค้า</th>
                        <th>กำหนดส่ง</th>
                        <th>วันที่ส่งจริง</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                        <th>รวม</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $details = $conn->query("SELECT D.*, G.Goods_name FROM D_ORDER D JOIN GOODS_NAME G ON D.Goods_id = G.Goods_id WHERE Order_no = $order_no");
                    $grand_total = 0;
                    if($details->num_rows > 0) {
                        while($d = $details->fetch_assoc()){
                            $grand_total += $d['TOT_PRC'];
                            echo "<tr>
                                <td style='text-align: left;'>{$d['Goods_name']}</td>
                                <td>" . date('d/m/Y', strtotime($d['Ord_date'])) . "</td>
                                <td>" . ($d['Fin_date'] ? date('d/m/Y', strtotime($d['Fin_date'])) : '<span style="color:#9ca3af">-</span>') . "</td>
                                <td>" . number_format($d['Amount']) . "</td>
                                <td style='text-align:right;'>" . number_format($d['COST_UNIT'], 2) . "</td>
                                <td style='text-align:right; font-weight:500;'>" . number_format($d['TOT_PRC'], 2) . "</td>
                                <td>
                                    <button class='btn btn-del' onclick='confirmDeleteItem(\"{$d['Goods_id']}\", \"{$d['Goods_name']}\")'>ลบ</button>
                                </td>
                            </tr>";
                        }
                        echo "<tr style='background:#f9fafb; font-weight:600;'><td colspan='5' style='text-align:right; padding-right:20px;'>ยอดรวมทั้งสิ้น:</td><td style='text-align:right; color:#6366f1; font-size:16px;'>".number_format($grand_total, 2)."</td><td>บาท</td></tr>";
                    } else {
                        echo "<tr><td colspan='7' style='padding:40px; color:#9ca3af;'>ยังไม่มีรายการสินค้าในใบสั่งซื้อนี้</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            <a href="order_list.php" class="btn btn-edit" style="background:#6366f1; color:white; padding: 10px 20px;">กลับหน้ารายการสั่งซื้อ</a>
        </div>
    </div>

    <script>
        // ฟังก์ชันคำนวณราคาสด (Live Calculation)
        function calculateTotal() {
            const select = document.getElementById('Goods_id');
            const amountInput = document.getElementById('Amount');
            const priceDisplay = document.getElementById('view_price');
            const totalDisplay = document.getElementById('view_total');

            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const amount = parseInt(amountInput.value) || 0;

            const total = price * amount;

            priceDisplay.value = price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            totalDisplay.value = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // แจ้งเตือน SweetAlert2
        <?php if($status_action == "added"): ?>
            Swal.fire({ icon: 'success', title: 'เพิ่มรายการสำเร็จ', showConfirmButton: false, timer: 1500 });
        <?php elseif($status_action == "deleted"): ?>
            Swal.fire({ icon: 'success', title: 'ลบรายการสำเร็จ', showConfirmButton: false, timer: 1500 });
            window.history.replaceState({}, document.title, window.location.pathname + "?order_no=<?php echo $order_no; ?>");
        <?php endif; ?>

        // ยืนยันการลบ
        function confirmDeleteItem(goodsId, goodsName) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ต้องการลบ '" + goodsName + "' ออกจากใบสั่งซื้อนี้หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?order_no=<?php echo $order_no; ?>&del_item=' + goodsId;
                }
            })
        }
    </script>
</body>
</html>