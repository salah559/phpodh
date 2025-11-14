    <footer class="footer bg-dark text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="text-golden">أضحيتي</h5>
                    <p>منصة موثوقة لشراء الأغنام والأضاحي بجودة عالية وأسعار منافسة</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-golden">روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo url('index.php'); ?>" class="text-white-50">الرئيسية</a></li>
                        <li><a href="<?php echo url('products.php'); ?>" class="text-white-50">المنتجات</a></li>
                        <li><a href="<?php echo url('orders.php'); ?>" class="text-white-50">الطلبات</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-golden">اتصل بنا</h5>
                    <p class="text-white-50">📞 الهاتف: +213 XXX XXX XXX</p>
                    <p class="text-white-50">📧 البريد: info@adhiyati.dz</p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 أضحيتي - جميع الحقوق محفوظة</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo asset('js/cart.js'); ?>"></script>
</body>
</html>
