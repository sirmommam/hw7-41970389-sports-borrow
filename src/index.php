<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | ระบบยืม-คืนอุปกรณ์กีฬา</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-white">

    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-lg rounded-2xl shadow-[0_0_40px_rgba(8,112,184,0.15)] p-8 border border-slate-700">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-cyan-500/10 mb-4">
                <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">เข้าสู่ระบบ</h1>
            <p class="text-slate-400 mt-2 text-sm">ระบบยืม-คืนอุปกรณ์กีฬา (Sports Borrowing)</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label class="block text-slate-300 text-sm font-medium mb-2" for="username">รหัสนักศึกษา / Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input type="text" id="username" class="w-full pl-10 pr-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all duration-300" placeholder="กรอกรหัสนักศึกษา" required>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 text-sm font-medium mb-2" for="password">รหัสผ่าน / Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" id="password" class="w-full pl-10 pr-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all duration-300" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" id="btnSubmit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 flex justify-center items-center shadow-lg shadow-cyan-500/30">
                <span>เข้าสู่ระบบ</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-xs text-slate-500">หากพบปัญหาการใช้งาน กรุณาติดต่อศูนย์คอมพิวเตอร์</p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault(); // ป้องกันหน้าเว็บกระพริบ (ไม่ยอม submit แบบปกติ)
                
                let username = $('#username').val();
                let password = $('#password').val();
                let btn = $('#btnSubmit');
                let originalContent = btn.html();

                // 1. เปลี่ยนสถานะปุ่มเป็นกำลังโหลด (Loading State) เพื่อ UI ที่ดูเป็นมืออาชีพ
                btn.html('<svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> กำลังตรวจสอบ...');
                btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');

                // 2. จำลองการดีเลย์ส่งข้อมูลไปหา PHP (รอ 1 วินาที)
                setTimeout(() => {
                    // คืนค่าปุ่มกลับมาเหมือนเดิม
                    btn.html(originalContent);
                    btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');

                    // 3. จำลองเงื่อนไขการเช็ค Login (ให้ลองกรอก admin / 1234 เพื่อดูแจ้งเตือนสีเขียว)
                    if(username === 'admin' && password === '1234') {
                        // โชว์ความหล่อของ jQuery Confirm เมื่อสำเร็จ
                        $.confirm({
                            title: '🎉 สำเร็จ!',
                            content: 'ยินดีต้อนรับเข้าสู่ระบบ กำลังพาท่านไปหน้าจัดการข้อมูล...',
                            type: 'green',
                            theme: 'modern',
                            backgroundDismiss: false,
                            buttons: {
                                ok: {
                                    text: 'ไปที่ Dashboard',
                                    btnClass: 'btn-green',
                                    action: function(){
                                        // ของจริงจะใส่ window.location.href = 'dashboard.php'; ตรงนี้ครับ
                                        $.alert('จำลองการย้ายหน้าสำเร็จ!'); 
                                    }
                                }
                            }
                        });
                    } else {
                        // โชว์แจ้งเตือน Error 
                        $.confirm({
                            title: '🚨 ข้อผิดพลาด!',
                            content: 'รหัสนักศึกษาหรือรหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
                            type: 'red',
                            theme: 'modern',
                            typeAnimated: true,
                            buttons: {
                                tryAgain: {
                                    text: 'ลองอีกครั้ง',
                                    btnClass: 'btn-red'
                                }
                            }
                        });
                    }
                }, 1000); // ดีเลย์ 1 วินาที
            });
        });
    </script>
</body>
</html>