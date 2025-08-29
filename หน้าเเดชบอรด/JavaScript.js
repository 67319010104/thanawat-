class Dashboard {
    constructor() {
        this.init();
    }

    init() {
        this.loadDashboardData();
        this.setupEventListeners();
    }

    async loadDashboardData() {
        try {
            const response = await fetch('api/dashboard.php');
            const data = await response.json();
            this.updateStats(data.stats);
            this.updateActivities(data.activities);
            this.updateTopProducts(data.topProducts);
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }

    updateStats(stats) {
        // อัพเดทสถิติต่างๆ
        document.querySelector('[data-stat="products"]').textContent = stats.totalProducts.toLocaleString();
        document.querySelector('[data-stat="users"]').textContent = stats.totalUsers.toLocaleString();
        document.querySelector('[data-stat="orders"]').textContent = stats.todayOrders.toLocaleString();
        document.querySelector('[data-stat="sales"]').textContent = '฿' + parseInt(stats.todaySales).toLocaleString();
    }

    updateActivities(activities) {
        const container = document.getElementById('activitiesContainer');
        container.innerHTML = '';
        
        activities.forEach(activity => {
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-3';
            
            const color = activity.type === 'user' ? 'green' : 'blue';
            const text = activity.type === 'user' 
                ? `ผู้ใช้ใหม่ลงทะเบียน: ${activity.email}`
                : `สินค้าใหม่ถูกเพิ่ม: ${activity.name}`;
                
            div.innerHTML = `
                <div class="w-2 h-2 bg-${color}-500 rounded-full"></div>
                <span class="text-sm text-gray-600">${text}</span>
            `;
            
            container.appendChild(div);
        });
    }

    updateTopProducts(products) {
        const container = document.getElementById('topProductsContainer');
        container.innerHTML = '';
        
        products.forEach(product => {
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center';
            div.innerHTML = `
                <span class="text-sm">${product.name}</span>
                <span class="text-sm font-semibold">${product.sold} ชิ้น</span>
            `;
            container.appendChild(div);
        });
    }

    setupEventListeners() {
        // Event listeners สำหรับการนำทาง
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const section = item.getAttribute('data-section');
                this.showSection(section);
            });
        });
    }

    showSection(sectionName) {
        // ซ่อนทุก section
        document.querySelectorAll('.section').forEach(section => {
            section.classList.add('hidden');
        });
        
        // แสดง section ที่เลือก
        document.getElementById(sectionName).classList.remove('hidden');
        
        // อัพเดทชื่อหน้า
        const titles = {
            'overview': 'ภาพรวม',
            'products': 'จัดการสินค้า',
            'users': 'จัดการผู้ใช้',
            'orders': 'คำสั่งซื้อ',
            'settings': 'ตั้งค่า'
        };
        document.getElementById('pageTitle').textContent = titles[sectionName];
        
        // อัพเดท active nav
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('bg-blue-700');
        });
        document.querySelector(`[data-section="${sectionName}"]`).classList.add('bg-blue-700');
    }
}

// เริ่มต้นเมื่อโหลดหน้าเสร็จ
document.addEventListener('DOMContentLoaded', () => {
    new Dashboard();
});