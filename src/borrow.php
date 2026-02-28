<?php
session_start();
if (!isset($_SESSION['is_logged_in'])) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืมอุปกรณ์กีฬา | Sports System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
    <style>body { font-family: 'Prompt', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex overflow-hidden">

    <?php include 'includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col h-screen">
        <header class="h-16 bg-white border-b flex items-center justify-between px-8 shrink-0 shadow-sm">
            <h2 class="text-slate-800 font-bold text-lg">ทำรายการยืมอุปกรณ์</h2>
            <div class="text-sm text-blue-600 font-semibold">ผู้ยืม: <?php echo $_SESSION['fullname']; ?></div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            <div class="mb-6 relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchItem" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-all" placeholder="ค้นหาอุปกรณ์ที่คุณต้องการ...">
            </div>

            <div id="equipmentGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                </div>
        </main>
    </div>

    <script>
    $(document).ready(function() {
        loadAvailableItems();

        // ค้นหาอุปกรณ์
        $("#searchItem").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#equipmentGrid .item-card").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function loadAvailableItems() {
        $.get('api/equipment_api.php?action=fetch', function(res) {
            let html = '';
            res.forEach(item => {
                if (item.remain_qty > 0) {
                    let img = item.eq_image ? `uploads/${item.eq_image}` : 'https://ui-avatars.com/api/?name='+item.eq_name;
                    html += `
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl transition-all group item-card">
                        <div class="h-48 overflow-hidden relative">
                            <img src="${img}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[10px] font-bold text-blue-600 shadow-sm">
                                คงเหลือ ${item.remain_qty} ชิ้น
                            </div>
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-slate-400 font-medium mb-1">${item.eq_type}</p>
                            <h4 class="font-bold text-slate-800 mb-4 h-12 line-clamp-2">${item.eq_name}</h4>
                            <button onclick="confirmBorrow(${item.eq_id}, '${item.eq_name}')" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-100 flex items-center justify-center">
                                <i class="fas fa-hand-holding-heart mr-2"></i> ยืมอุปกรณ์นี้
                            </button>
                        </div>
                    </div>`;
                }
            });
            $('#equipmentGrid').html(html || '<div class="col-span-full text-center py-20 text-slate-400">ไม่มีอุปกรณ์พร้อมให้ยืมในขณะนี้</div>');
        }, 'json');
    }

    function confirmBorrow(id, name) {
        $.confirm({
            title: 'ยืนยันการยืม?',
            content: `คุณต้องการยืม <b>${name}</b> ใช่หรือไม่? <br><small class="text-slate-500">*กรุณารับอุปกรณ์ที่ห้องพักครูหลังทำรายการ</small>`,
            type: 'blue',
            theme: 'modern',
            buttons: {
                confirm: {
                    text: 'ยืนยันยืม',
                    btnClass: 'btn-blue',
                    action: function() {
                        $.post('api/borrow_api.php', { action: 'borrow', eq_id: id }, function(res) {
                            if(res.status === 'success') {
                                $.alert({ title: 'สำเร็จ!', content: res.message, type: 'green' });
                                loadAvailableItems();
                            } else {
                                $.alert({ title: 'ผิดพลาด', content: res.message, type: 'red' });
                            }
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