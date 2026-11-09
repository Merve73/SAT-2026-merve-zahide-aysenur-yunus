<?php
/*
DOSYA: ortak/mesajlar.php
KİM: Kişi 3
NE YAPAR: Sayfanın $hata ve $tamam yazılarını gösterir. İki yerde görünür:
1) Formun üstünde kırmızı (hata) veya yeşil (başarı) kutu. Sayfada kalır.
2) Sağ üstte kayarak gelen bildirim (pop-up). 6 saniye sonra kendiliğinden kaybolur (css: .bildirim).
Her formlu sayfa bu dosyayı include eder. Böylece aynı satırları her sayfada yazmayız.
*/
?>
<!-- HARİTA: Hata ve başarı bildirimleri (pop-up) -->
<!-- Hata yazısı varsa kırmızı kutu ve bildirim. -->
<?php if (isset($hata) && $hata != "") { ?>
    <!-- Kutu. İkon CSS'ten gelir. -->
    <p class="hata"><?php echo temiz_yaz($hata); ?></p>
    <!-- Bildirim. role="alert": ekran okuyucu hemen okur. -->
    <div class="bildirim bildirim-hata" role="alert">
        <!-- Sallanan uyarı ikonu. -->
        <span class="ikon ikon-salla">⚠️</span>
        <!-- Yazı. -->
        <span><?php echo temiz_yaz($hata); ?></span>
        <!-- Kapat düğmesi. js/site.js (9d) kapatır. aria-label: ekran okuyucu "Kapat" der. -->
        <button type="button" class="bildirim-kapat" aria-label="Kapat">✕</button>
    </div>
<?php } ?>
<!-- Başarı yazısı varsa yeşil kutu ve bildirim. -->
<?php if (isset($tamam) && $tamam != "") { ?>
    <!-- Kutu. -->
    <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
    <!-- Bildirim. role="status": ekran okuyucu sırası gelince okur. -->
    <div class="bildirim bildirim-tamam" role="status">
        <!-- Zıplayan onay ikonu. -->
        <span class="ikon ikon-zipla">✅</span>
        <!-- Yazı. -->
        <span><?php echo temiz_yaz($tamam); ?></span>
        <!-- Kapat düğmesi. -->
        <button type="button" class="bildirim-kapat" aria-label="Kapat">✕</button>
    </div>
<?php } ?>
