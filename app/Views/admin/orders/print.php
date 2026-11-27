<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Bán Hàng - <?= htmlspecialchars($order['order_code']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        .invoice-box {
            max-width: 800px;
            margin: 30px auto;
            padding: 40px;
            background: #fff;
            border: 1px solid #dee2e6;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        @media print {
            body {
                background: none;
                margin: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container text-center my-3 no-print">
    <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold rounded-pill">
        <i class="fa-solid fa-print me-2"></i> In Hóa Đơn (Ctrl + P)
    </button>
    <button onclick="window.close()" class="btn btn-secondary px-4 py-2 rounded-pill ms-2">
        Đóng Cửa Sổ
    </button>
</div>

<div class="invoice-box">
    <!-- Header Hóa Đơn -->
    <div class="row align-items-center mb-4">
        <div class="col-7">
            <h3 class="fw-bold mb-1" style="font-family: sans-serif; color: #ff4757;">APPLE MEO MEO SNEAKER</h3>
            <p class="small mb-1">Cửa hàng giày Sneaker chính hãng & Phong cách</p>
            <p class="small mb-1">Địa chỉ: Số 123 Đường Cầu Giấy, Q. Cầu Giấy, Hà Nội</p>
            <p class="small mb-0">Hotline: 090.123.4567 | Website: applemeomeo.vn</p>
        </div>
        <div class="col-5 text-end">
            <div class="invoice-title">HÓA ĐƠN BÁN HÀNG</div>
            <div class="small mt-1">Mã đơn: <strong><?= htmlspecialchars($order['order_code']) ?></strong></div>
            <div class="small">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
        </div>
    </div>

    <hr>

    <!-- Khách Hàng -->
    <div class="row mb-4">
        <div class="col-7">
            <h6 class="fw-bold text-uppercase mb-2">Thông Tin Khách Hàng:</h6>
            <div>Họ và tên: <strong><?= htmlspecialchars($order['recipient_name']) ?></strong></div>
            <div>Số điện thoại: <strong><?= htmlspecialchars($order['recipient_phone']) ?></strong></div>
            <div>Địa chỉ giao: <?= htmlspecialchars($order['shipping_address']) ?></div>
            <?php if (!empty($order['note'])): ?>
                <div>Ghi chú: <em><?= htmlspecialchars($order['note']) ?></em></div>
            <?php endif; ?>
        </div>
        <div class="col-5 text-end">
            <h6 class="fw-bold text-uppercase mb-2">Hình Thức Thanh Toán:</h6>
            <div>Phương thức: <strong><?= payment_method_label($order['payment_method']) ?></strong></div>
            <div>Trạng thái TT: <strong><?= ($order['payment_status'] === 'paid') ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN' ?></strong></div>
        </div>
    </div>

    <!-- Bảng Chi Tiết Sản Phẩm -->
    <table class="table table-bordered align-middle mb-3">
        <thead>
            <tr class="table-light text-center">
                <th style="width: 50px;">STT</th>
                <th>Tên Sản Phẩm / Mẫu Giày</th>
                <th style="width: 90px;">Size</th>
                <th style="width: 100px;">Màu</th>
                <th style="width: 130px;">Đơn Giá</th>
                <th style="width: 70px;">SL</th>
                <th style="width: 140px;" class="text-end">Thành Tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $idx => $it): ?>
                <tr>
                    <td class="text-center"><?= $idx + 1 ?></td>
                    <td>
                        <strong><?= htmlspecialchars($it['product_name']) ?></strong>
                        <div class="small text-muted">SKU: <?= htmlspecialchars($it['sku']) ?></div>
                    </td>
                    <td class="text-center fw-bold"><?= htmlspecialchars($it['size']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($it['color']) ?></td>
                    <td class="text-end"><?= format_price($it['unit_price']) ?></td>
                    <td class="text-center fw-bold"><?= $it['quantity'] ?></td>
                    <td class="text-end fw-bold"><?= format_price($it['line_total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tổng Tiền -->
    <div class="row justify-content-end mb-4">
        <div class="col-6">
            <table class="table table-sm table-borderless">
                <tr>
                    <td>Tạm tính:</td>
                    <td class="text-end fw-semibold"><?= format_price($order['subtotal']) ?></td>
                </tr>
                <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                    <tr class="text-danger">
                        <td>Giảm giá voucher:</td>
                        <td class="text-end fw-bold">- <?= format_price($order['discount_amount']) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Phí giao hàng:</td>
                    <td class="text-end fw-semibold"><?= ($order['shipping_fee'] == 0) ? 'Miễn phí' : format_price($order['shipping_fee']) ?></td>
                </tr>
                <tr class="border-top fs-5">
                    <td class="fw-bold">TỔNG THANH TOÁN:</td>
                    <td class="text-end fw-bold text-danger"><?= format_price($order['total_amount']) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Chữ Ký -->
    <div class="row text-center mt-5">
        <div class="col-6">
            <div class="fw-bold">NGƯỜI NHẬN HÀNG</div>
            <div class="small text-muted">(Ký và ghi rõ họ tên)</div>
            <div style="height: 70px;"></div>
        </div>
        <div class="col-6">
            <div class="fw-bold">ĐẠI DIỆN APPLE MEO MEO STORE</div>
            <div class="small text-muted">(Ký, đóng dấu)</div>
            <div style="height: 70px;"></div>
        </div>
    </div>

    <div class="text-center text-muted small mt-4 border-top pt-3">
        Cảm ơn quý khách đã tin tưởng và đồng hành cùng Apple Meo Meo Shoes Store! Đổi trả size miễn phí trong 30 ngày.
    </div>
</div>

</body>
</html>
