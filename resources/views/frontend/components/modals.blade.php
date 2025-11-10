<!-- Luxury Modal Component -->
<div class="luxury-modal-overlay" id="luxuryModal">
    <div class="luxury-modal">
        <button class="modal-close" onclick="closeLuxuryModal()">
            <i class="ti-close" style="font-size:18px;"></i>
        </button>
        <div class="modal-icon-container">
            <div class="modal-icon" id="modalIcon">
                <i id="modalIconClass"></i>
                <div class="modal-icon-ring"></div>
            </div>
        </div>
        <h2 class="modal-title" id="modalTitle">Thành công!</h2>
        <p class="modal-text" id="modalText">Thao tác của bạn đã được thực hiện thành công</p>
        <div class="modal-product-info" id="modalProductInfo" style="display:none;">
            <img id="modalProductImg" src="" alt="Product" class="modal-product-img">
            <div class="modal-product-details">
                <div class="modal-product-name" id="modalProductName"></div>
                <div class="modal-product-price" id="modalProductPrice"></div>
            </div>
        </div>
        <div class="modal-actions" id="modalActions">
            <button class="modal-btn modal-btn-primary" onclick="closeLuxuryModal()" id="modalPrimaryBtn">
                Hoàn tất
            </button>
        </div>
    </div>
</div>

<style>
/* Modal Overlay */
.luxury-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.luxury-modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Modal Container */
.luxury-modal {
    background: white;
    border-radius: 20px;
    max-width: 450px;
    width: 90%;
    padding: 40px 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    position: relative;
    transform: scale(0.7);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.luxury-modal-overlay.active .luxury-modal {
    transform: scale(1);
    opacity: 1;
}

/* Close Button */
.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 35px;
    height: 35px;
    background: #f5f5f5;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: #666;
    font-size: 18px;
}

.modal-close:hover {
    background: #D4AF37;
    color: white;
    transform: rotate(90deg);
}

/* Icon Container */
.modal-icon-container {
    width: 80px;
    height: 80px;
    margin: 0 auto 25px;
    position: relative;
    animation: iconPop 0.6s ease forwards;
}

@keyframes iconPop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.modal-icon {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    position: relative;
    color: white;
}

.modal-icon.success {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modal-icon.error {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.modal-icon.cart {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.modal-icon.wishlist {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.modal-icon-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid;
    border-radius: 50%;
    opacity: 0.3;
    animation: ringPulse 2s ease-in-out infinite;
}

.modal-icon.success .modal-icon-ring {
    border-color: #667eea;
}

.modal-icon.error .modal-icon-ring {
    border-color: #f5576c;
}

.modal-icon.cart .modal-icon-ring {
    border-color: #f5576c;
}

.modal-icon.wishlist .modal-icon-ring {
    border-color: #00f2fe;
}

@keyframes ringPulse {
    0% {
        transform: scale(1);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.3);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 0;
    }
}

/* Modal Content */
.modal-title {
    font-size: 26px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 12px;
    animation: slideDown 0.5s ease forwards;
    animation-delay: 0.2s;
    opacity: 0;
    text-align: center;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-text {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 30px;
    animation: slideDown 0.5s ease forwards;
    animation-delay: 0.3s;
    opacity: 0;
    text-align: center;
}

/* Product Info (for cart) */
.modal-product-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    animation: slideDown 0.5s ease forwards;
    animation-delay: 0.4s;
    opacity: 0;
}

.modal-product-img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
}

.modal-product-details {
    flex: 1;
    text-align: left;
}

.modal-product-name {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.modal-product-price {
    font-size: 16px;
    font-weight: 700;
    color: #D4AF37;
}

/* Modal Actions */
.modal-actions {
    display: flex;
    gap: 10px;
    animation: slideUp 0.5s ease forwards;
    animation-delay: 0.5s;
    opacity: 0;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-btn {
    flex: 1;
    padding: 14px 25px;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modal-btn-primary {
    background: linear-gradient(135deg, #D4AF37 0%, #C4A037 100%);
    color: #1a1a1a;
}

.modal-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
}

.modal-btn-secondary {
    background: white;
    color: #666;
    border: 2px solid #e0e0e0;
}

.modal-btn-secondary:hover {
    background: #f5f5f5;
    border-color: #D4AF37;
    color: #D4AF37;
}

/* Confetti Animation */
.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #D4AF37;
    animation: confettiFall 3s ease-out forwards;
}

@keyframes confettiFall {
    to {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

/* Responsive */
@media (max-width: 480px) {
    .luxury-modal {
        padding: 30px 20px;
    }
    .modal-title {
        font-size: 22px;
    }
    .modal-text {
        font-size: 14px;
    }
    .modal-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Luxury Modal Functions
function showLuxuryModal(type, title, text, options = {}) {
    const modal = document.getElementById('luxuryModal');
    const icon = document.getElementById('modalIcon');
    const iconClass = document.getElementById('modalIconClass');
    const modalTitle = document.getElementById('modalTitle');
    const modalText = document.getElementById('modalText');
    const modalProductInfo = document.getElementById('modalProductInfo');
    const modalActions = document.getElementById('modalActions');
    
    // Reset animations
    icon.className = 'modal-icon ' + type;
    modalTitle.style.opacity = '0';
    modalText.style.opacity = '0';
    if (modalProductInfo) modalProductInfo.style.opacity = '0';
    modalActions.style.opacity = '0';
    
    // Set icon
    const icons = {
        success: 'fa fa-check',
        error: 'fa fa-times',
        cart: 'ti-bag',
        wishlist: 'fa fa-heart'
    };
    iconClass.className = icons[type] || 'fa fa-check';
    
    // Set content
    modalTitle.textContent = title;
    modalText.textContent = text;
    
    // Product info (for cart)
    if (options.product) {
        modalProductInfo.style.display = 'flex';
        document.getElementById('modalProductImg').src = options.product.image || '';
        document.getElementById('modalProductName').textContent = options.product.name || '';
        document.getElementById('modalProductPrice').textContent = (options.product.price ? new Intl.NumberFormat('vi-VN').format(options.product.price) + '₫' : '');
    } else {
        modalProductInfo.style.display = 'none';
    }
    
    // Actions
    modalActions.innerHTML = '';
    if (options.secondaryBtn) {
        const secondaryBtn = document.createElement('button');
        secondaryBtn.className = 'modal-btn modal-btn-secondary';
        secondaryBtn.textContent = options.secondaryBtn.text || 'Đóng';
        secondaryBtn.onclick = function() {
            if (options.secondaryBtn.action) {
                options.secondaryBtn.action();
            }
            closeLuxuryModal();
        };
        modalActions.appendChild(secondaryBtn);
    }
    
    const primaryBtn = document.createElement('button');
    primaryBtn.className = 'modal-btn modal-btn-primary';
    primaryBtn.textContent = options.primaryBtn?.text || 'Hoàn tất';
    primaryBtn.onclick = function() {
        if (options.primaryBtn?.action) {
            options.primaryBtn.action();
        }
        closeLuxuryModal();
    };
    modalActions.appendChild(primaryBtn);
    
    // Show modal
    modal.classList.add('active');
    
    // Create confetti for success/cart/wishlist
    if (type !== 'error') {
        createConfetti(modal);
    }
    
    // Auto close after delay if specified
    if (options.autoClose !== false) {
        setTimeout(() => {
            closeLuxuryModal();
        }, options.autoClose || 3000);
    }
}

function closeLuxuryModal() {
    const modal = document.getElementById('luxuryModal');
    modal.classList.remove('active');
}

// Close modal when clicking outside
document.getElementById('luxuryModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLuxuryModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLuxuryModal();
    }
});

function createConfetti(modal) {
    const colors = ['#D4AF37', '#FFD700', '#FFA500', '#FF6B6B', '#4ECDC4'];
    for (let i = 0; i < 30; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
            modal.appendChild(confetti);
            setTimeout(() => confetti.remove(), 3000);
        }, i * 50);
    }
}

// Make functions globally available
window.showLuxuryModal = showLuxuryModal;
window.closeLuxuryModal = closeLuxuryModal;
</script>

