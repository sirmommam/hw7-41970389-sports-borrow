⌨️ Coding Standards
Variable Naming: ใช้ camelCase สำหรับ JavaScript และ snake_case สำหรับ PHP/Database
PHP PDO: ห้ามเขียน Query ตรงๆ ให้ใช้ prepare() และ execute() เท่านั้นเพื่อป้องกัน SQL Injection
CSS: ใช้ Tailwind Utility Classes เป็นหลัก หลีกเลี่ยงการเขียน Inline Style

🌿 Git Workflow & Commit Messages
เราใช้รูปแบบ Conventional Commits:
feat: เพิ่ม Feature ใหม่ (เช่น feat: add login ajax)
fix: แก้ไข Bug (เช่น fix: database connection timeout)
docs: แก้ไขเอกสาร (เช่น docs: update task list)
style: ปรับปรุงความสวยงามของ Code/UI (ไม่มีผลกับ Logic)