        <footer class="site-footer">
            <div class="footer-content">
                &copy; <?= date('Y') ?> 提示板
            </div>
        </footer>
    </div>
</body>

<script>
    // ページ読み込み時に3秒後にメッセージを非表示にする
    window.addEventListener('DOMContentLoaded', () => {
        const alertMsg = document.getElementById('alert-message');
        if (alertMsg) {
            setTimeout( () => {
                alertMsg.style.transition = 'opacity 0.5s ease';
                alertMsg.style.opacity = '0';
                setTimeout( () => alertMsg.style.display = 'none', 500);
            }, 3000); // 3秒後にフェードアウト開始
        }
    });
</script>

</html>