<?php 
include('db.php'); 

$status = ""; // ตัวแปรสำหรับเช็คสถานะเพื่อแสดง Popup

// 1. เพิ่มข้อมูลใหม่
if(isset($_POST['save_new'])){
    $id = $_POST['Cus_id'];
    $name = $_POST['Cus_name'];
    
    $check = $conn->query("SELECT * FROM CUS_NAME WHERE Cus_id = '$id'");
    if($check->num_rows > 0) {
        $status = "duplicate";
    } else {
        if($conn->query("INSERT INTO CUS_NAME (Cus_id, Cus_name) VALUES ('$id', '$name')")){
            $status = "save_success";
        }
    }
}

// 2. แก้ไขข้อมูล
if(isset($_POST['update_data'])){
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    if($conn->query("UPDATE CUS_NAME SET Cus_name = '$name' WHERE Cus_id = '$id'")){
        $status = "edit_success";
    }
}

// 3. ลบข้อมูล
if(isset($_GET['confirm_del'])){
    $id = $_GET['confirm_del'];
    $conn->query("DELETE FROM CUS_NAME WHERE Cus_id='$id'");
    $status = "del_success";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการข้อมูลลูกค้า</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- 1. เพิ่ม SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 30px; border-radius: 15px; width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .modal-header { font-size: 18px; font-weight: 500; color: var(--primary-bg); margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header-banner">บันทึก / แก้ไข ข้อมูลลูกค้า</div>

    <div class="container">
        <!-- ฟอร์มเพิ่มข้อมูล -->
        <div class="card">
            <h3 style="margin-top:0; font-size:16px;">เพิ่มลูกค้าใหม่</h3>
            <form method="POST">
                <div style="display: flex; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label>รหัสลูกค้า (5 หลัก):</label>
                        <input type="text" name="Cus_id" maxlength="5" required placeholder="C001">
                    </div>
                    <div style="flex: 2;">
                        <label>ชื่อลูกค้า:</label>
                        <input type="text" name="Cus_name" maxlength="30" required placeholder="ชื่อ-นามสกุล">
                    </div>
                    <button type="submit" name="save_new" class="btn btn-add">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>

        <!-- ตารางข้อมูล -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th width="20%">รหัสลูกค้า</th>
                        <th width="50%">รายละเอียดลูกค้า</th>
                        <th width="30%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM CUS_NAME ORDER BY Cus_id ASC");
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                            <td>{$row['Cus_id']}</td>
                            <td style='text-align: left;'>{$row['Cus_name']}</td>
                            <td>
                                <button class='btn btn-edit' onclick='openEditModal(\"{$row['Cus_id']}\", \"{$row['Cus_name']}\")'>แก้ไข</button>
                                <button class='btn btn-del' onclick='confirmDelete(\"{$row['Cus_id']}\")' style='margin-left:5px;'>ลบ</button>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn btn-edit" style="background-color:#6366f1; color:white;">กลับหน้าหลัก</a>
    </div>

    <!-- Popup แก้ไข -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">แก้ไขข้อมูลลูกค้า</div>
            <form method="POST">
                <div class="form-group">
                    <label>รหัสลูกค้า:</label>
                    <input type="text" name="edit_id" id="edit_id" readonly style="background:#f3f4f6;">
                </div>
                <div class="form-group">
                    <label>ชื่อลูกค้า:</label>
                    <input type="text" name="edit_name" id="edit_name" required>
                </div>
                <div style="text-align: right; margin-top: 20px; display:flex; gap:10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-del" onclick="closeModal()" style="background:#9ca3af;">ยกเลิก</button>
                    <button type="submit" name="update_data" class="btn btn-add">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 2. ฟังก์ชันแสดง Popup แจ้งเตือนเมื่อทำงานสำเร็จ
        <?php if($status == "save_success"): ?>
            Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', text: 'ข้อมูลลูกค้าถูกเพิ่มเข้าระบบแล้ว', timer: 2000, showConfirmButton: false });
        <?php elseif($status == "edit_success"): ?>
            Swal.fire({ icon: 'success', title: 'แก้ไขสำเร็จ', text: 'ปรับปรุงข้อมูลลูกค้าเรียบร้อยแล้ว', timer: 2000, showConfirmButton: false });
        <?php elseif($status == "del_success"): ?>
            Swal.fire({ icon: 'success', title: 'ลบข้อมูลสำเร็จ', timer: 1500, showConfirmButton: false });
        <?php elseif($status == "duplicate"): ?>
            Swal.fire({ icon: 'error', title: 'รหัสซ้ำ!', text: 'รหัสลูกค้านี้มีอยู่ในระบบแล้ว' });
        <?php endif; ?>

        // ฟังก์ชันยืนยันการลบแบบสวยๆ
        function confirmDelete(id) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบลูกค้า " + id + " ใช่หรือไม่?",
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

        function openEditModal(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() { document.getElementById('editModal').style.display = 'none'; }
        window.onclick = function(event) { if (event.target == document.getElementById('editModal')) closeModal(); }
    </script>
</body>
</html>