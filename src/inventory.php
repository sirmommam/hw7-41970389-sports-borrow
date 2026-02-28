<?php
session_start();
/**
 * 🛡️ Session Security Check
 * ตรวจสอบการเข้าถึงสิทธิ์ หากไม่ได้ Login ให้กลับไปหน้า index.php 
 */
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php'; // ใช้ไฟล์เชื่อมต่อ PDO ของคุณ Nhum
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคลังอุปกรณ์ | Sports Borrowing System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>

    <style>
        body { font-family: 'Prompt', sans-serif; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-collapsed { width: 80px; }
        .sidebar-expanded { width: 260px; }
    </style>
</head>
<body class="bg-slate-50 flex overflow-hidden">

    <?php include 'includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0 h-screen">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0 shadow-sm z-10">
            <div class="flex items-center space-x-2">
                <i class="fas fa-boxes-stacked text-blue-600"></i>
                <h2 class="text-slate-800 font-bold text-lg">จัดการคลังอุปกรณ์กีฬา</h2>
            </div>
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-blue-100 flex items-center">
                <i class="fas fa-plus mr-2"></i> เพิ่มอุปกรณ์ใหม่
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        รายการอุปกรณ์ทั้งหมด <span id="itemCount" class="ml-2 px-2 py-0.5 bg-slate-100 text-slate-500 text-xs rounded-full">0</span>
                    </h3>
                    <div class="relative w-full md:w-72">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-search text-sm"></i>
                        </span>
                        <input type="text" id="tableSearch" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="ค้นหาชื่ออุปกรณ์...">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-400 text-xs uppercase tracking-widest">
                                <th class="px-6 py-4 font-semibold">รูปภาพ</th>
                                <th class="px-6 py-4 font-semibold">ชื่ออุปกรณ์ / ประเภท</th>
                                <th class="px-6 py-4 font-semibold text-center">คงเหลือ / ทั้งหมด</th>
                                <th class="px-6 py-4 font-semibold text-center">สถานะ</th>
                                <th class="px-6 py-4 font-semibold text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="equipmentTable" class="divide-y divide-slate-50 text-sm">
                            </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="equipmentModal" class="fixed inset-0 bg-slate-900/60 hidden z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden transform transition-all">
            <div class="px-8 py-5 bg-white border-b border-slate-50 flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-800">เพิ่มอุปกรณ์กีฬา</h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="equipmentForm" enctype="multipart/form-data" class="p-8 space-y-5">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="eq_id" id="eq_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2 text-center pb-4">
                        <label for="eq_image" class="cursor-pointer group block">
                            <div id="imagePreview" class="w-32 h-32 mx-auto rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden group-hover:border-blue-400 transition-all">
                                <i class="fas fa-camera text-slate-300 text-2xl group-hover:text-blue-400"></i>
                            </div>
                            <span class="text-xs text-blue-600 mt-2 block font-medium group-hover:underline">คลิกเพื่ออัปโหลดรูปภาพ</span>
                        </label>
                        <input type="file" name="eq_image" id="eq_image" class="hidden" accept="image/*">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ชื่ออุปกรณ์</label>
                        <input type="text" name="eq_name" id="eq_name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ประเภท</label>
                        <select name="eq_type" id="eq_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="ลูกบอล">ลูกบอล</option>
                            <option value="ไม้แร็กเกต">ไม้แร็กเกต</option>
                            <option value="ชุดฝึกซ้อม">ชุดฝึกซ้อม</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">จำนวนทั้งหมด</label>
                        <input type="number" name="total_qty" id="total_qty" required min="1" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 text-slate-400 font-semibold hover:text-slate-600 transition-colors">ยกเลิก</button>
                    <button type="submit" id="btnSubmit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-95">
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadEquipment(); // ดึงข้อมูลครั้งแรก

            // ✅ พรีวิวรูปภาพก่อนอัปโหลด
            $('#eq_image').change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').html(`<img src="${e.target.result}" class="w-full h-full object-cover">`);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // ✅ ส่ง Form ด้วย AJAX (FormData) เพื่อรองรับไฟล์ภาพ [cite: 1]
            $('#equipmentForm').on('submit', function(e) {
                e.preventDefault(); // 🚨 สำคัญมาก: ป้องกันการ Refresh หน้าจอ
                
                let formData = new FormData(this);
                $('#btnSubmit').prop('disabled', true).text('กำลังประมวลผล...');

                $.ajax({
                    url: 'api/equipment_api.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        $('#btnSubmit').prop('disabled', false).text('บันทึกข้อมูล');
                        if(res.status === 'success') {
                            $.alert({ title: 'สำเร็จ!', content: res.message, type: 'green', theme: 'modern' });
                            closeModal();
                            loadEquipment();
                        } else {
                            $.alert({ title: 'ผิดพลาด!', content: res.message, type: 'red', theme: 'modern' });
                        }
                    },
                    error: function() {
                        $('#btnSubmit').prop('disabled', false).text('บันทึกข้อมูล');
                        $.alert({ title: 'Error', content: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', type: 'orange' });
                    }
                });
            });

            // ✅ ระบบค้นหาแบบ Real-time
            $("#tableSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#equipmentTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function loadEquipment() {
            $.get('api/equipment_api.php?action=fetch', function(res) {
                let html = '';
                $('#itemCount').text(res.length);
                if(res.length > 0) {
                    res.forEach(item => {
                        let img = item.eq_image ? `uploads/${item.eq_image}` : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(item.eq_name) + '&background=f1f5f9&color=64748b&size=128';
                        let statusColor = item.remain_qty > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600';
                        
                        html += `
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-6 py-4">
                                <img src="${img}" class="w-12 h-12 rounded-2xl object-cover shadow-sm border border-slate-100 group-hover:scale-110 transition-transform">
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">${item.eq_name}</p>
                                <p class="text-xs text-slate-400 mt-0.5">${item.eq_type}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-blue-600">${item.remain_qty}</span>
                                <span class="text-slate-300 mx-1">/</span>
                                <span class="text-slate-500">${item.total_qty}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 ${statusColor} rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    ${item.remain_qty > 0 ? 'พร้อมยืม' : 'หมดชั่วคราว'}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="editItem(${item.eq_id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button onclick="deleteItem(${item.eq_id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="5" class="px-6 py-20 text-center text-slate-400 italic">ไม่พบข้อมูลอุปกรณ์ในคลัง</td></tr>';
                }
                $('#equipmentTable').html(html);
            }, 'json');
        }

        function editItem(id) {
            $.get('api/equipment_api.php', { action: 'get_item', eq_id: id }, function(data) {
                $('#modalTitle').text('แก้ไขข้อมูลอุปกรณ์');
                $('#formAction').val('update'); // เปลี่ยนเป็น Update Mode
                $('#eq_id').val(data.eq_id);
                $('#eq_name').val(data.eq_name);
                $('#eq_type').val(data.eq_type);
                $('#total_qty').val(data.total_qty);
                if (data.eq_image) {
                    $('#imagePreview').html(`<img src="uploads/${data.eq_image}" class="w-full h-full object-cover">`);
                }
                $('#equipmentModal').removeClass('hidden');
            }, 'json');
        }

        function openAddModal() {
            $('#modalTitle').text('เพิ่มอุปกรณ์ใหม่');
            $('#formAction').val('add');
            $('#equipmentForm')[0].reset();
            $('#imagePreview').html('<i class="fas fa-camera text-slate-300 text-2xl group-hover:text-blue-400"></i>');
            $('#equipmentModal').removeClass('hidden');
        }

        function closeModal() {
            $('#equipmentModal').addClass('hidden');
        }

        function deleteItem(id) {
            $.confirm({
                title: 'ยืนยันการลบ?',
                content: 'คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'ลบข้อมูล',
                        btnClass: 'btn-red',
                        action: function() {
                            $.post('api/equipment_api.php', { action: 'delete', eq_id: id }, function(res) {
                                if(res.status === 'success') loadEquipment();
                            }, 'json');
                        }
                    },
                    cancel: { text: 'ยกเลิก' }
                }
            });
        }
    </script>
</body>
</html>