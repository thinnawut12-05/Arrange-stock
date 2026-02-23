<?php 
include('db.php'); 

// เตรียมตัวแปรสำหรับแจ้งเตือนและคงค่าในฟอร์ม
$status = "";
$count_processed = 0;
// ใช้ชื่อตัวแปรตามภาพที่ 6
$gdoc_date1 = isset($_POST['gdoc_date1']) ? $_POST['gdoc_date1'] : '';
$gdoc_date2 = isset($_POST['gdoc_date2']) ? $_POST['gdoc_date2'] : '';

if (isset($_POST['process_action'])) {
    if (!empty($gdoc_date1) && !empty($gdoc_date2)) {
        
        // 1. ค้นหาข้อมูลตามเงื่อนไขในภาพ (Fin_date ระหว่าง gdoc_date1 ถึง gdoc_date2)
        $sql_select = "SELECT h.Cus_id, d.Goods_id, h.Order_Date as Doc_date, 
                              d.Ord_date, d.Fin_date, d.Amount, d.TOT_PRC as cost_tot, d.Order_no
                       FROM H_ORDER h 
                       JOIN D_ORDER d ON h.Order_no = d.Order_no
                       WHERE d.Fin_date >= '$gdoc_date1' AND d.Fin_date <= '$gdoc_date2'";
        
        $result = $conn->query($sql_select);

        if ($result && $result->num_rows > 0) {
            $conn->begin_transaction();

            try {
                while ($row = $result->fetch_assoc()) {
                    // 2. ย้ายลงตาราง m_order
                    $stmt = $conn->prepare("INSERT INTO m_order (Cus_id, Goods_id, Doc_date, Ord_date, Fin_date, Sys_date, Amount, cost_tot) 
                                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
                    
                    $stmt->bind_param("sssssdi", 
                        $row['Cus_id'], 
                        $row['Goods_id'], 
                        $row['Doc_date'], 
                        $row['Ord_date'], 
                        $row['Fin_date'], 
                        $row['Amount'], 
                        $row['cost_tot']
                    );
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error inserting into m_order");
                    }
                    $count_processed++;
                }

                // 3. ลบข้อมูลออกจากตารางงานประจำวันตามโจทย์
                $conn->query("DELETE FROM D_ORDER WHERE Fin_date >= '$gdoc_date1' AND Fin_date <= '$gdoc_date2'");
                $conn->query("DELETE FROM H_ORDER WHERE Order_no NOT IN (SELECT DISTINCT Order_no FROM D_ORDER)");

                $conn->commit();
                $status = "success";

            } catch (Exception $e) {
                $conn->rollback();
                $status = "error";
            }
        } else {
            $status = "no_data";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>การประมวลผลข้อมูลสั่งซื้อสินค้า</title>
    <!-- เรียกใช้ CSS Modern เดิมของคุณ -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- SweetAlert2 สำหรับแจ้งเตือน -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .process-container { max-width: 550px; margin: 80px auto; }
        .inner-form-box { 
            background: #fffbeb; /* สีส้มอ่อนเลียนแบบภาพต้นฉบับแต่ให้ดูทันสมัย */
            border: 1px solid #fef3c7; 
            padding: 30px; 
            border-radius: 12px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- หัวเว็บ Indigo เดิม -->
    <div class="header-banner">การประมวลผลข้อมูลสั่งซื้อสินค้า</div>

    <div class="container">
        <div class="card process-container">
            <div class="card-title" style="justify-content: center;">ย้ายข้อมูลสั่งซื้อเข้า Master File</div>
            
            <form method="POST">
                <!-- ส่วนกล่องกรอกข้อมูลที่ทำตามภาพที่ 6 -->
                <div class="inner-form-box">
                    <div class="form-group">
                        <label>ระหว่างวันที่ส่งสินค้า :</label>
                        <input type="date" name="gdoc_date1" value="<?php echo $gdoc_date1; ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>ถึงวันที่ :</label>
                        <input type="date" name="gdoc_date2" value="<?php echo $gdoc_date2; ?>" required>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 25px;">
                    <button type="submit" name="process_action" class="btn btn-add" style="background:#6366f1; width: 120px;">ตกลง</button>
                    <a href="index.php" class="btn btn-del" style="background:#9ca3af; width: 100px; text-align:center;">ยกเลิก</a>
                </div>
            </form>

            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 20px;">
                * ข้อมูลที่ลง <strong>วันที่ส่งจริง</strong> ในช่วงวันที่เลือกจะถูกย้ายไปตารางประวัติ (m_order)
            </p>
        </div>
    </div>

    <script>
        // แจ้งเตือน SweetAlert2 เมื่อทำงานเสร็จ
        <?php if($status == "success"): ?>
            Swal.fire({ icon: 'success', title: 'ประมวลผลสำเร็จ', text: 'ย้ายเข้า Master File <?php echo $count_processed; ?> รายการ เรียบร้อยแล้ว' });
        <?php elseif($status == "no_data"): ?>
            Swal.fire({ icon: 'warning', title: 'ไม่พบข้อมูล', text: 'ไม่พบรายการที่มีวันที่ส่งจริงในช่วงวันที่คุณเลือก' });
        <?php elseif($status == "error"): ?>
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ระบบไม่สามารถประมวลผลข้อมูลได้' });
        <?php endif; ?>
    </script>
</body>
</html>