<?php
/**
 * @var array $items
 * @var float $subtotal
 * @var float $shippingFee
 * @var ?array $appliedVoucher
 * @var float $discountAmount
 * @var float $finalTotal
 * @var ?array $user
 * @var array $savedAddresses
 * @var ?array $defaultAddress
 * @var array $availableVouchers
 */
if (!function_exists('route_url')) {
    require_once dirname(__DIR__, 2) . '/Core/Helper.php';
}

$items = !empty($items) && is_array($items) ? $items : [];
$subtotal = $subtotal ?? 0.0;
$shippingFee = $shippingFee ?? 0.0;
$appliedVoucher = $appliedVoucher ?? null;
$discountAmount = $discountAmount ?? 0.0;
$finalTotal = $finalTotal ?? 0.0;
$user = $user ?? null;
$savedAddresses = !empty($savedAddresses) && is_array($savedAddresses) ? $savedAddresses : [];
$defaultAddress = $defaultAddress ?? null;
$availableVouchers = !empty($availableVouchers) && is_array($availableVouchers) ? $availableVouchers : [];
?>
<div class="container my-5">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-credit-card me-2 text-danger"></i> Thanh Toán Đơn Hàng</h3>

    <form action="<?= route_url('checkout-process') ?>" method="POST" id="checkoutForm">
        <div class="row g-4">
            <!-- Cột Trái: Thông Tin Nhận Hàng & Thanh Toán -->
            <div class="col-lg-7">
                <!-- 1. Địa chỉ giao hàng -->
                <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">1. Địa Chỉ Nhận Hàng</h5>

                    <?php if (!empty($user) && !empty($savedAddresses)): ?>
                        <!-- Lựa chọn địa chỉ đã lưu -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Chọn từ sổ địa chỉ đã lưu:</label>
                            <?php foreach ($savedAddresses as $idx => $addr): ?>
                                <div class="form-check p-3 border rounded-3 mb-2 <?= !empty($addr['is_default']) ? 'border-danger bg-danger-subtle bg-opacity-10' : 'bg-light' ?>">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="selected_address_id" id="addr_<?= $addr['id'] ?>" value="<?= $addr['id'] ?>" <?= (!empty($addr['is_default']) || $idx === 0) ? 'checked' : '' ?> onchange="toggleAddressMode('saved')">
                                    <label class="form-check-label d-block ms-4" for="addr_<?= $addr['id'] ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong><?= htmlspecialchars($addr['recipient_name'] ?? '') ?></strong> - <?= htmlspecialchars($addr['phone'] ?? '') ?>
                                            <?php if (!empty($addr['is_default'])): ?>
                                                <span class="badge bg-danger small">Mặc định</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-secondary mt-1">
                                            <?= htmlspecialchars(($addr['address_line'] ?? '') . ', ' . ($addr['ward'] ?? '') . ', ' . ($addr['district'] ?? '') . ', ' . ($addr['province'] ?? '')) ?>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="radio" name="address_choice" id="addr_choice_saved" value="saved" checked onchange="toggleAddressMode('saved')">
                                <label class="form-check-label fw-semibold" for="addr_choice_saved">Sử dụng địa chỉ đã chọn ở trên</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_choice" id="addr_choice_new" value="new" onchange="toggleAddressMode('new')">
                                <label class="form-check-label fw-semibold text-danger" for="addr_choice_new">+ Giao đến địa chỉ mới</label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Form nhập địa chỉ mới -->
                    <div id="newAddressContainer" class="<?= (!empty($savedAddresses)) ? 'd-none' : '' ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Họ và Tên Người Nhận <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" id="inputRecipientName" class="form-control" placeholder="Ví dụ: Nguyễn Văn Phong" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" <?= empty($savedAddresses) ? 'required' : '' ?>>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số Điện Thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="recipient_phone" id="inputRecipientPhone" class="form-control" placeholder="Ví dụ: 0912345678" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" <?= empty($savedAddresses) ? 'required' : '' ?>>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tỉnh / Thành Phố <span class="text-danger">*</span></label>
                                <input type="text" name="province" id="inputProvince" class="form-control" placeholder="Ví dụ: Hà Nội">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quận / Huyện <span class="text-danger">*</span></label>
                                <input type="text" name="district" id="inputDistrict" class="form-control" placeholder="Ví dụ: Cầu Giấy">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phường / Xã</label>
                                <input type="text" name="ward" id="inputWard" class="form-control" placeholder="Ví dụ: Dịch Vọng Hậu">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa Chỉ Chi Tiết (Số nhà, tên đường, ngõ ngách) <span class="text-danger">*</span></label>
                            <input type="text" name="address_line" id="inputAddressLine" class="form-control" placeholder="Ví dụ: Số 12 ngõ 45 phố Duy Tân">
                        </div>

                        <?php if ($user): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="save_address" id="save_address" value="1" checked>
                                <label class="form-check-label small text-secondary" for="save_address">
                                    Lưu địa chỉ này vào sổ địa chỉ của tôi
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2 mt-3">
                        <label class="form-label fw-semibold">Ghi Chú Đơn Hàng (Nếu có)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút..."></textarea>
                    </div>
                </div>

                <!-- 2. Phương Thức Thanh Toán -->
                <div class="bg-white p-4 rounded-4 border shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">2. Phương Thức Thanh Toán</h5>

                    <div class="form-check p-3 border rounded-3 mb-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
                        <label class="form-check-label fw-bold d-block" for="pay_cod">
                            <i class="fa-solid fa-money-bill-wave text-success me-2 fs-5"></i> Thanh toán tiền mặt khi nhận hàng (COD)
                            <small class="d-block text-secondary fw-normal mt-1">Khách nhận giày tận tay, kiểm tra đúng size và mẫu mã trước khi trả tiền cho shipper.</small>
                        </label>
                    </div>

                    <div class="form-check p-3 border rounded-3 mb-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_banking" value="banking">
                        <label class="form-check-label fw-bold d-block" for="pay_banking">
                            <i class="fa-solid fa-qrcode text-primary me-2 fs-5"></i> Chuyển khoản VietQR Ngân hàng (Tự động)
                            <small class="d-block text-secondary fw-normal mt-1">Hệ thống tạo mã QR động đúng số tiền và nội dung chuyển khoản qua ứng dụng mọi ngân hàng.</small>
                        </label>
                    </div>

                    <div class="form-check p-3 border rounded-3 mb-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_vnpay" value="vnpay">
                        <label class="form-check-label fw-bold d-block" for="pay_vnpay">
                            <i class="fa-solid fa-credit-card text-danger me-2 fs-5"></i> Cổng thanh toán VNPay
                            <small class="d-block text-secondary fw-normal mt-1">Thanh toán an toàn qua cổng VNPay (Thẻ ATM nội địa, Visa/Mastercard hoặc VNPay-QR).</small>
                        </label>
                    </div>

                    <div class="form-check p-3 border rounded-3 bg-light">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_momo" value="momo">
                        <label class="form-check-label fw-bold d-block" for="pay_momo">
                            <i class="fa-solid fa-wallet text-danger-emphasis me-2 fs-5" style="color: #a50064 !important;"></i> Ví điện tử MoMo
                            <small class="d-block text-secondary fw-normal mt-1">Quét mã QR qua ứng dụng Ví MoMo để hoàn tất thanh toán tức thì.</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Tóm Tắt Sản Phẩm & Voucher & Nút Đặt Hàng -->
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Đơn Hàng (<?= count($items) ?> sản phẩm)</h5>

                    <div class="order-items-list mb-3" style="max-height: 280px; overflow-y: auto;">
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600') ?>" alt="<?= htmlspecialchars($item['product_name'] ?? 'Giày') ?>" class="rounded-3 border" style="width: 55px; height: 55px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['product_name'] ?? '') ?></h6>
                                    <span class="small text-secondary">Size <?= htmlspecialchars($item['size'] ?? '') ?> | SL: <?= (int)($item['quantity'] ?? 1) ?></span>
                                </div>
                                <span class="fw-bold small text-danger"><?= format_price(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Voucher tại bước thanh toán -->
                    <div class="mb-3">
                        <?php if (!empty($appliedVoucher)): ?>
                            <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-0 small">
                                <div>
                                    <i class="fa-solid fa-tag me-1"></i> Mã: <strong><?= htmlspecialchars($appliedVoucher['code'] ?? '') ?></strong>
                                    <span class="d-block text-success-emphasis">Đã giảm <?= format_price($discountAmount) ?></span>
                                </div>
                                <a href="<?= route_url('cart-remove-voucher', ['redirect_to' => route_url('checkout')]) ?>" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill small" title="Gỡ">Bỏ mã</a>
                            </div>
                        <?php else: ?>
                            <div class="input-group">
                                <input type="text" id="checkoutVoucherInput" class="form-control form-control-sm text-uppercase" placeholder="Nhập mã voucher...">
                                <button type="button" class="btn btn-sm btn-dark" onclick="applyCheckoutVoucher()">Áp Dụng</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tạm tính:</span>
                        <span class="fw-semibold"><?= format_price($subtotal) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Giảm giá voucher:</span>
                        <span class="fw-semibold text-danger">
                            <?= ($discountAmount > 0) ? '- ' . format_price($discountAmount) : '0 ₫' ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí giao hàng:</span>
                        <span class="fw-semibold <?= ($shippingFee == 0) ? 'text-success' : '' ?>">
                            <?= ($shippingFee == 0) ? 'Miễn phí' : format_price($shippingFee) ?>
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Tổng tiền phải trả:</span>
                        <span class="fw-black fs-4 text-danger"><?= format_price($finalTotal) ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                        <i class="fa-solid fa-lock me-2"></i> Xác Nhận Đặt Hàng
                    </button>

                    <p class="text-center small text-secondary mt-3 mb-0">
                        <i class="fa-solid fa-shield-halved me-1 text-success"></i> Cam kết bảo mật thông tin & giao dịch 100%
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAddressMode(mode) {
    const newContainer = document.getElementById('newAddressContainer');
    const inputName = document.getElementById('inputRecipientName');
    const inputPhone = document.getElementById('inputRecipientPhone');
    
    if (mode === 'saved') {
        newContainer.classList.add('d-none');
        if (inputName) inputName.removeAttribute('required');
        if (inputPhone) inputPhone.removeAttribute('required');
    } else {
        newContainer.classList.remove('d-none');
        if (inputName) inputName.setAttribute('required', 'required');
        if (inputPhone) inputPhone.setAttribute('required', 'required');
    }
}

function applyCheckoutVoucher() {
    const input = document.getElementById('checkoutVoucherInput');
    if (!input) return;
    const val = input.value.trim();
    if (!val) {
        alert('Vui lòng nhập mã giảm giá!');
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= route_url('cart-apply-voucher') ?>';
    
    const inputCode = document.createElement('input');
    inputCode.type = 'hidden';
    inputCode.name = 'voucher_code';
    inputCode.value = val;
    
    const inputRedirect = document.createElement('input');
    inputRedirect.type = 'hidden';
    inputRedirect.name = 'redirect_to';
    inputRedirect.value = '<?= route_url('checkout') ?>';
    
    form.appendChild(inputCode);
    form.appendChild(inputRedirect);
    document.body.appendChild(form);
    form.submit();
}
</script>
