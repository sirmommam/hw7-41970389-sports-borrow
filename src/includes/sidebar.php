<?php
/**
 * 🚩 Sidebar Component
 * รวมเมนูหลักและระบบ Toggle Sidebar ไว้ที่เดียวเพื่อให้ง่ายต่อการบำรุงรักษา 
 */
// ตรวจสอบสถานะการ Login เบื้องต้นเพื่อความปลอดภัย
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

if (!isset($_SESSION['is_logged_in'])) {
    exit; 
}

$current_page = basename($_SERVER['PHP_SELF']); 
?>

<aside id="sidebar" class="sidebar-transition sidebar-expanded bg-slate-900 min-h-screen flex flex-col text-slate-300 relative z-50 shadow-2xl">
    
    <div class="h-16 flex items-center px-6 bg-slate-950/50 border-b border-slate-800/50">
        <i class="fas fa-volleyball-ball text-blue-500 text-2xl min-w-[32px]"></i>
        <span class="ml-4 font-bold text-white text-lg overflow-hidden whitespace-nowrap sidebar-text transition-opacity duration-300">
            SPORTS <span class="text-blue-500">SYSTEM</span>
        </span>
    </div>

    <button id="toggleSidebar" class="absolute -right-3 top-20 bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-slate-100 hover:bg-blue-700 transition-all shadow-lg z-[60]">
        <i class="fas fa-chevron-left text-[10px]" id="toggleIcon"></i>
    </button>

    <nav class="flex-1 mt-6 px-4 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo $current_page == 'dashboard.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-th-large min-w-[20px] <?php echo $current_page == 'dashboard.php' ? '' : 'group-hover:text-blue-400'; ?>"></i>
            <span class="ml-4 sidebar-text overflow-hidden whitespace-nowrap text-sm font-medium">หน้าหลัก (Dashboard)</span>
        </a>

        <a href="inventory.php" class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo $current_page == 'inventory.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-boxes-stacked min-w-[20px] <?php echo $current_page == 'inventory.php' ? '' : 'group-hover:text-blue-400'; ?>"></i>
            <span class="ml-4 sidebar-text overflow-hidden whitespace-nowrap text-sm font-medium">จัดการคลังอุปกรณ์</span>
        </a>

        <a href="borrow.php" class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo $current_page == 'borrow.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-hand-holding-heart min-w-[20px] group-hover:text-blue-400"></i>
            <span class="ml-4 sidebar-text overflow-hidden whitespace-nowrap text-sm font-medium">ยืม-คืนอุปกรณ์</span>
        </a>

        <a href="reports.php" class="flex items-center px-4 py-3 rounded-xl transition-all group <?php echo $current_page == 'reports.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fas fa-chart-line min-w-[20px] group-hover:text-blue-400"></i>
            <span class="ml-4 sidebar-text overflow-hidden whitespace-nowrap text-sm font-medium">รายงานสรุป</span>
        </a>
    </nav>

    <div class="p-4 border-t border-slate-800 bg-slate-950/20">
        <div class="flex items-center px-2 mb-4">
            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow-inner">
                <i class="fas fa-user-circle text-lg"></i>
            </div>
            <div class="ml-3 sidebar-text overflow-hidden">
                <p class="text-xs font-bold text-white truncate"><?php echo $_SESSION['fullname']; ?></p> 
                <p class="text-[10px] text-slate-500 uppercase tracking-tighter font-semibold"><?php echo $_SESSION['role']; ?></p> 
            </div>
        </div>
        
        <a href="api/logout.php" class="flex items-center px-4 py-2.5 text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all whitespace-nowrap overflow-hidden group">
            <i class="fas fa-power-off min-w-[20px] group-hover:scale-110 transition-transform"></i>
            <span class="ml-4 sidebar-text text-sm font-semibold">ออกจากระบบ</span>
        </a>
    </div>
</aside>

<script>
$(document).ready(function() {
    // ฟังก์ชันสำหรับจัดการสถานะ Sidebar 
    const toggleSidebar = () => {
        const sidebar = $('#sidebar');
        const icon = $('#toggleIcon');
        const sidebarText = $('.sidebar-text');
        
        if (sidebar.hasClass('sidebar-expanded')) {
            // ย่อ Sidebar 
            sidebar.removeClass('sidebar-expanded').addClass('sidebar-collapsed');
            sidebarText.fadeOut(100);
            icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
        } else {
            // ขยาย Sidebar 
            sidebar.removeClass('sidebar-collapsed').addClass('sidebar-expanded');
            sidebarText.fadeIn(400);
            icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
        }
    };

    $('#toggleSidebar').on('click', toggleSidebar); 
});
</script>