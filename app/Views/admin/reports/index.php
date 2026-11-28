<div class="container-fluid">
    <!-- Header Báo Cáo -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Báo Cáo Thống Kê & Doanh Thu</h4>
            <p class="text-secondary small mb-0">Phân tích kết quả kinh doanh, xu hướng doanh thu và sản phẩm sneaker bán chạy nhất</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_url('admin-report-export') ?>" class="btn btn-success rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-file-excel me-1"></i> Xuất Báo Cáo (CSV / Excel)
            </a>
            <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-3">
                <i class="fa-solid fa-print me-1"></i> In Báo Cáo
            </button>
        </div>
    </div>

    <!-- 4 Thẻ Thống Kê Nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Doanh Thu Thực Tế</span>
                        <h3 class="fw-black text-danger mt-1 mb-0"><?= format_price($stats['revenue']) ?></h3>
                    </div>
                    <div class="bg-danger-subtle p-3 rounded-circle text-danger fs-3">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Tổng Số Đơn Hàng</span>
                        <h3 class="fw-black text-dark mt-1 mb-0"><?= $stats['total_orders'] ?> đơn</h3>
                    </div>
                    <div class="bg-primary-subtle p-3 rounded-circle text-primary fs-3">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Đơn Chờ Xử Lý</span>
                        <h3 class="fw-black text-warning mt-1 mb-0"><?= $stats['pending_orders'] ?> đơn</h3>
                    </div>
                    <div class="bg-warning-subtle p-3 rounded-circle text-warning fs-3">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card card-stat bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase">Khách Hàng Đăng Ký</span>
                        <h3 class="fw-black text-dark mt-1 mb-0"><?= $stats['total_users'] ?> thành viên</h3>
                    </div>
                    <div class="bg-success-subtle p-3 rounded-circle text-success fs-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng Biểu Đồ 1: Doanh Thu Theo Ngày & Phân Bố Trạng Thái Đơn -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-danger me-2"></i> Xu Hướng Doanh Thu (15 Ngày Gần Nhất)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueDaysChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie text-primary me-2"></i> Trạng Thái Đơn Hàng</h5>
                <div style="height: 300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng Biểu Đồ 2: Top Sản Phẩm Bán Chạy & Doanh Số Theo Danh Mục -->
    <div class="row g-4 mb-4">
        <!-- Top Sản Phẩm Bán Chạy Nhất -->
        <div class="col-lg-7">
            <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-fire text-danger me-2"></i> Top Giày Sneaker Bán Chạy Nhất</h5>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Top</th>
                                <th>Mẫu Giày</th>
                                <th>SKU</th>
                                <th class="text-center">Số Lượng Đã Bán</th>
                                <th class="text-end">Doanh Số Thu Về</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topProducts)): ?>
                                <?php foreach ($topProducts as $i => $tp): ?>
                                    <tr>
                                        <td class="fw-bold text-center">
                                            <?php if ($i === 0): ?>
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-crown"></i> 1</span>
                                            <?php elseif ($i === 1): ?>
                                                <span class="badge bg-secondary">2</span>
                                            <?php elseif ($i === 2): ?>
                                                <span class="badge bg-danger-subtle text-danger">3</span>
                                            <?php else: ?>
                                                <span class="text-secondary"><?= $i + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= htmlspecialchars(image_src($tp['image_url'] ?: 'uploads/products/no-product-image.svg')) ?>" alt="Shoe" class="rounded-2 border" style="width: 42px; height: 42px; object-fit: cover;">
                                                <div class="fw-bold small text-dark"><?= htmlspecialchars($tp['product_name']) ?></div>
                                            </div>
                                        </td>
                                        <td><code class="small"><?= htmlspecialchars($tp['sku']) ?></code></td>
                                        <td class="text-center fw-bold text-danger"><?= $tp['total_sold'] ?> đôi</td>
                                        <td class="text-end fw-bold"><?= format_price($tp['total_revenue']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4 text-secondary">Chưa có dữ liệu bán hàng.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Doanh Số Theo Danh Mục -->
        <div class="col-lg-5">
            <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-tags text-warning me-2"></i> Doanh Số Theo Danh Mục Giày</h5>
                <div style="height: 240px;" class="mb-3">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="table-responsive small">
                    <table class="table table-sm table-borderless mb-0">
                        <?php foreach ($categorySales as $cs): ?>
                            <tr>
                                <td><i class="fa-solid fa-shoe-prints text-danger me-1"></i> <strong><?= htmlspecialchars($cs['category_name']) ?></strong></td>
                                <td class="text-center"><?= $cs['total_sold'] ?> đôi</td>
                                <td class="text-end fw-bold text-danger"><?= format_price($cs['total_revenue']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 1. Biểu đồ doanh thu 15 ngày
const revenueDaysData = <?= json_encode($revenueDays) ?>;
const dayLabels = revenueDaysData.map(d => d.order_date.split('-').slice(1).reverse().join('/'));
const dayValues = revenueDaysData.map(d => parseFloat(d.revenue));

const ctxDays = document.getElementById('revenueDaysChart').getContext('2d');
new Chart(ctxDays, {
    type: 'line',
    data: {
        labels: dayLabels.length ? dayLabels : ['Hôm nay'],
        datasets: [{
            label: 'Doanh thu (₫)',
            data: dayValues.length ? dayValues : [0],
            borderColor: '#ff4757',
            backgroundColor: 'rgba(255, 71, 87, 0.1)',
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#ff4757',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return (value / 1000000) + ' Tr';
                    }
                }
            }
        }
    }
});

// 2. Biểu đồ trạng thái đơn hàng
const statusData = <?= json_encode($statusDistribution) ?>;
const statusMap = {
    'pending': 'Chờ xác nhận',
    'confirmed': 'Đã duyệt',
    'processing': 'Đang đóng gói',
    'shipping': 'Đang giao',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy'
};
const statusColors = {
    'pending': '#ffc107',
    'confirmed': '#0dcaf0',
    'processing': '#6c757d',
    'shipping': '#0d6efd',
    'completed': '#198754',
    'cancelled': '#dc3545'
};

const statusLabels = statusData.map(s => statusMap[s.order_status] || s.order_status);
const statusCounts = statusData.map(s => parseInt(s.count));
const bgColors = statusData.map(s => statusColors[s.order_status] || '#adb5bd');

const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: statusLabels.length ? statusLabels : ['Chưa có đơn'],
        datasets: [{
            data: statusCounts.length ? statusCounts : [1],
            backgroundColor: bgColors.length ? bgColors : ['#e9ecef']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// 3. Biểu đồ doanh số theo danh mục
const catData = <?= json_encode($categorySales) ?>;
const catLabels = catData.map(c => c.category_name);
const catValues = catData.map(c => parseFloat(c.total_revenue));

const ctxCat = document.getElementById('categoryChart').getContext('2d');
new Chart(ctxCat, {
    type: 'bar',
    data: {
        labels: catLabels.length ? catLabels : ['Chưa có'],
        datasets: [{
            label: 'Doanh số (₫)',
            data: catValues.length ? catValues : [0],
            backgroundColor: ['#ff4757', '#3742fa', '#2ed573', '#ffa502', '#70a1ff'],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(v) { return (v / 1000000) + ' Tr'; }
                }
            }
        }
    }
});
</script>
