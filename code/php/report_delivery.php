<?php
include('db.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานกำหนดส่งสินค้า</title>

    <!-- ✅ ใช้ CSS เดิมของพี่ -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- ใช้แค่ตอนพิมพ์ -->
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="header-banner">
    รายงานกำหนดส่งสินค้า
</div>

<div class="container">

<!-- ฟอร์มค้นหา -->
<div class="card no-print">
<form method="GET">
    <div style="display:flex; gap:20px; align-items:flex-end; padding:20px;">
        <div style="flex:1;">
            <label>วันที่เริ่ม</label>
            <input type="date" name="d1"
                value="<?= $_GET['d1'] ?? '' ?>" required>
        </div>
        <div style="flex:1;">
            <label>ถึงวันที่</label>
            <input type="date" name="d2"
                value="<?= $_GET['d2'] ?? '' ?>" required>
        </div>
        <button type="submit" class="btn-add">
            แสดงข้อมูล
        </button>
    </div>
</form>
</div>

<!-- ตาราง -->
<div class="card">
<table>
<thead>
<tr>
    <th>ลำดับ</th>
    <th>รายละเอียดลูกค้า</th>
    <th>รายละเอียดสินค้า</th>
    <th>วันที่สั่ง</th>
    <th>กำหนดส่ง</th>
    <th>วันส่งจริง</th>
    <th>จำนวน</th>
    <th>ราคา/หน่วย</th>
    <th>รวม</th>
</tr>
</thead>
<tbody>

<?php
if (isset($_GET['d1'], $_GET['d2'])) {

    $d1 = $_GET['d1'];
    $d2 = $_GET['d2'];

    $sql = "
        SELECT
            m.Doc_date,
            m.Ord_date,
            m.Fin_date,
            m.Amount,
            m.cost_tot,
            (m.cost_tot / m.Amount) AS price_unit,
            c.Cus_id, c.Cus_name,
            g.Goods_id, g.Goods_name
        FROM m_order m
        JOIN cus_name c ON m.Cus_id = c.Cus_id
        JOIN goods_name g ON m.Goods_id = g.Goods_id
        WHERE m.Ord_date BETWEEN '$d1' AND '$d2'
        ORDER BY m.Ord_date
    ";

    $res = $conn->query($sql);
    $i = 1;
    $sumQty = 0;
    $sumPrice = 0;

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {

            echo "<tr>
                <td>{$i}</td>
                <td>{$row['Cus_id']} : {$row['Cus_name']}</td>
                <td>{$row['Goods_id']} : {$row['Goods_name']}</td>
                <td>".date('d/m/Y', strtotime($row['Doc_date']))."</td>
                <td>".date('d/m/Y', strtotime($row['Ord_date']))."</td>
                <td>".date('d/m/Y', strtotime($row['Fin_date']))."</td>
                <td>".number_format($row['Amount'])."</td>
                <td>".number_format($row['price_unit'],2)."</td>
                <td>".number_format($row['cost_tot'],2)."</td>
            </tr>";

            $sumQty += $row['Amount'];
            $sumPrice += $row['cost_tot'];
            $i++;
        }

        echo "<tr class='summary-row'>
            <td colspan='6' style='text-align:right;'>รวมทั้งสิ้น</td>
            <td>".number_format($sumQty)."</td>
            <td></td>
            <td>".number_format($sumPrice,2)."</td>
        </tr>";
    } else {
        echo "<tr><td colspan='9'>ไม่พบข้อมูล</td></tr>";
    }
}
?>

</tbody>
</table>
</div>

<!-- ปุ่ม -->
<div class="no-print" style="display:flex; justify-content:space-between; margin-top:20px;">
    <button onclick="window.print()" class="btn-add">
        พิมพ์รายงาน
    </button>
    <a href="index.php" class="btn-back">
        กลับหน้าหลัก
    </a>
</div>

</div>
</body>
</html>
