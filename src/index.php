<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | Sports Borrowing System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Prompt', sans-serif; }
        .jconfirm .jconfirm-box { border-radius: 15px; }
    </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-200">
        <div class="text-center mb-8">
            <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-volleyball-ball text-3xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-slate-800">SPORTS SYSTEM</h1>
            <p class="text-slate-500 mt-2 text-sm">ระบบยืม-คืนอุปกรณ์กีฬา SPU Chonburi</p>
        </div>

        <form id="loginForm" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">ชื่อผู้ใช้งาน (Username)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" id="username" required
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="admin">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">รหัสผ่าน (Password)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" id="btnLogin"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i> เข้าสู่ระบบ
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-400 uppercase tracking-widest">
            &copy; 2026 Sports Borrowing System
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#btnLogin');
            const originalContent = btn.html();

            // Loading State
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> กำลังตรวจสอบ...');

            $.ajax({
                url: 'api/auth_api.php', // ตรวจสอบว่าไฟล์อยู่ใน src/api/auth_api.php 
                type: 'POST',
                data: $(this).serialize() + '&action=login',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        $.confirm({
                            title: '<span class="text-green-600">สำเร็จ!</span>',
                            content: response.message,
                            type: 'green',
                            icon: 'fas fa-check-circle',
                            theme: 'modern',
                            buttons: {
                                ok: {
                                    text: 'ตกลง',
                                    btnClass: 'btn-green',
                                    action: function(){
                                        window.location.href = 'dashboard.php';
                                    }
                                }
                            }
                        });
                    } else {
                        // 🚨 กรณีรหัสผ่านไม่ถูกต้อง หรือ error อื่นๆ
                        $.confirm({
                            title: '<span class="text-red-600">เข้าสู่ระบบไม่สำเร็จ</span>',
                            content: response.message,
                            type: 'red',
                            icon: 'fas fa-exclamation-triangle',
                            theme: 'modern',
                            buttons: {
                                tryAgain: {
                                    text: 'ลองอีกครั้ง',
                                    btnClass: 'btn-red',
                                    action: function(){
                                        $('#password').val('').focus();
                                    }
                                }
                            }
                        });
                        btn.prop('disabled', false).html(originalContent);
                    }
                },
                error: function(xhr) {
                    $.alert({
                        title: 'เกิดข้อผิดพลาด!',
                        content: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ (Status: ' + xhr.status + ')',
                        type: 'orange',
                        theme: 'modern'
                    });
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        });
    });
    </script>
</body>
</html>