<?php 
include('db.php'); 

$status = ""; // ตัวแปรเช็คสถานะสำหรับแสดงผล Popup

// 1. เพิ่มสินค้าใหม่ (ไม่มีดัก 10 หลักแล้ว)
if(isset($_POST['add_new'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $cost = $_POST['cost'];
    
    // ตรวจสอบรหัสซ้ำ
    $check = $conn->query("SELECT * FROM GOODS_NAME WHERE Goods_id = '$id'");
    if($check->num_rows > 0) {
        $status = "duplicate";
    } else {
        if(!empty($id)) {
            $conn->query("INSERT INTO GOODS_NAME (Goods_id, Goods_name, cost_unit) VALUES ('$id', '$name', '$cost')");
            $status = "save_success";
        }
    }
}

// 2. แก้ไขสินค้า (Update)
if(isset($_POST['update_data'])){
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $cost = $_POST['edit_cost'];
    $conn->query("UPDATE GOODS_NAME SET Goods_name = '$name', cost_unit = '$cost' WHERE Goods_id = '$id'");
    $status = "edit_success";
}

// 3. ลบสินค้า
if(isset($_GET['confirm_del'])){
    $id = $_GET['confirm_del'];
    $conn->query("DELETE FROM GOODS_NAME WHERE Goods_id='$id'");
    $status = "del_success";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>บันทึก / แก้ไข ข้อมูลสินค้า</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- เรียกใช้ SweetAlert2 สำหรับแจ้งเตือนสวยๆ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* สไตล์สำหรับ Modal Popup แก้ไขข้อมูล */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 30px; border-radius: 15px; width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .modal-header { font-size: 18px; font-weight: 500; color: #6366f1; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        
        /* ปรับแต่งปุ่มในตารางให้เหมือนในภาพ */
        .btn-edit-table { background-color: #3b82f6; color: white; padding: 6px 15px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; transition: 0.2s; }
        .btn-edit-table:hover { background-color: #2563eb; }
        .btn-del-table { background-color: #ef4444; color: white; padding: 6px 15px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; transition: 0.2s; margin-left: 5px; }
        .btn-del-table:hover { background-color: #dc2626; }
    </style>
</head>
<body>
    <div class="header-banner">บันทึก / แก้ไข ข้อมูลสินค้า</div>
    
    <div class="container">
        <!-- บล็อกเพิ่มสินค้าใหม่ -->
        <div class="card">
            <h3 style="margin-top:0; font-size:16px; color:#111827;">เพิ่มสินค้าใหม่</h3>
            <form method="POST">
                <div style="display: flex; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label>รหัสสินค้า (10 หลัก):</label>
                        <input type="text" name="id" maxlength="10" required placeholder="G000000001">
                    </div>
                    <div style="flex: 2;">
                        <label>รายละเอียดสินค้า:</label>
                        <input type="text" name="name" required placeholder="ระบุชื่อสินค้า">
                    </div>
                    <div style="flex: 1;">
                        <label>ราคา/หน่วย:</label>
                        <input type="number" name="cost" step="0.01" required placeholder="0.00">
                    </div>
                    <button type="submit" name="add_new" class="btn btn-add" style="background-color: #6366f1; color: white; padding: 10px 20px;">เพิ่มสินค้า</button>
                </div>
            </form>
        </div>

        <!-- ตารางแสดงรายการสินค้า -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th width="15%">รหัสสินค้า</th>
                        <th width="45%">รายละเอียด</th>
                        <th width="15%">ราคา/หน่วย</th>
                        <th width="25%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM GOODS_NAME ORDER BY Goods_id ASC");
                    while($row = $res->fetch_assoc()){
                        echo "<tr>
                            <td><b>{$row['Goods_id']}</b></td>
                            <td style='text-align:left'>{$row['Goods_name']}</td>
                            <td>".number_format($row['cost_unit'],2)."</td>
                            <td>
                                <button class='btn-edit-table' onclick='openEditModal(\"{$row['Goods_id']}\", \"{$row['Goods_name']}\", \"{$row['cost_unit']}\")'>แก้ไข</button>
                                <button class='btn-del-table' onclick='confirmDelete(\"{$row['Goods_id']}\")'>ลบ</button>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn" style="background-color:#6366f1; color:white; padding:10px 20px; text-decoration:none; border-radius:6px; font-size:14px;">กลับหน้าหลัก</a>
    </div>

    <!-- Popup Modal สำหรับแก้ไขข้อมูล -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">แก้ไขข้อมูลสินค้า</div>
            <form method="POST">
                <div class="form-group">
                    <label>รหัสสินค้า (แก้ไขไม่ได้):</label>
                    <input type="text" name="edit_id" id="edit_id" readonly style="background:#f3f4f6;">
                </div>
                <div class="form-group">
                    <label>รายละเอียดสินค้า:</label>
                    <input type="text" name="edit_name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>ราคาต่อหน่วย:</label>
                    <input type="number" name="edit_cost" id="edit_cost" step="0.01" required>
                </div>
                <div style="text-align: right; margin-top: 20px; display:flex; gap:10px; justify-content: flex-end;">
                    <button type="button" class="btn" onclick="closeModal()" style="background:#9ca3af; color:white; padding:8px 15px; border-radius:6px; border:none;">ยกเลิก</button>
                    <button type="submit" name="update_data" class="btn btn-add" style="background:#6366f1; color:white; padding:8px 15px; border-radius:6px; border:none;">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // แจ้งเตือน Popup เมื่อทำงานสำเร็จ
        <?php if($status == "save_success"): ?>
            Swal.fire({ icon: 'success', title: 'เพิ่มสินค้าสำเร็จ', timer: 1500, showConfirmButton: false });
        <?php elseif($status == "edit_success"): ?>
            Swal.fire({ icon: 'success', title: 'แก้ไขข้อมูลสำเร็จ', timer: 1500, showConfirmButton: false });
        <?php elseif($status == "del_success"): ?>
            Swal.fire({ icon: 'success', title: 'ลบข้อมูลเรียบร้อย', timer: 1500, showConfirmButton: false });
        <?php elseif($status == "duplicate"): ?>
            Swal.fire({ icon: 'error', title: 'รหัสสินค้าซ้ำ!', text: 'รหัสนี้มีอยู่ในระบบแล้ว' });
        <?php endif; ?>

        // ฟังก์ชันยืนยันการลบ
        function confirmDelete(id) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบสินค้า " + id + " ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?confirm_del=' + id;
                }
            })
        }

        // ฟังก์ชันจัดการ Modal แก้ไข
        function openEditModal(id, name, cost) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_cost').value = cost;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() { document.getElementById('editModal').style.display = 'none'; }
        window.onclick = function(event) { if (event.target == document.getElementById('editModal')) closeModal(); }
    </script>
</body>
</html>