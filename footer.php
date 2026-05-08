<?php
// footer.php - تذييل النظام مع هوية الكلية
?>
<footer class="university-footer">
    <div class="footer-content">
        <div class="footer-logo">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="footer-text">
            <strong>كلية التقنية الهندسية - جنزور</strong><br>
            <small>&copy; <?php echo date('Y'); ?> جميع الحقوق محفوظة</small>
        </div>
    </div>
</footer>

<style>
.university-footer {
    background: linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%);
    color: white;
    padding: 15px 20px;
    text-align: center;
    margin-top: 20px;
    border-radius: 8px 8px 0 0;
    position: relative;
    overflow: hidden;
}

.university-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, #f59e0b, #10b981, #f59e0b);
    animation: shimmer 3s infinite;
}

.footer-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.footer-logo {
    font-size: 20px;
    color: #fbbf24;
}

.footer-text {
    font-size: 13px;
    line-height: 1.4;
}

.footer-text strong {
    font-size: 14px;
    color: #fbbf24;
    display: block;
    margin-bottom: 2px;
}

.footer-text small {
    opacity: 0.8;
    font-size: 11px;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

@media (max-width: 768px) {
    .university-footer {
        padding: 12px 15px;
        margin-top: 15px;
    }
    
    .footer-content {
        gap: 10px;
    }
    
    .footer-logo {
        font-size: 18px;
    }
    
    .footer-text {
        font-size: 12px;
    }
    
    .footer-text strong {
        font-size: 13px;
    }
}
</style>
