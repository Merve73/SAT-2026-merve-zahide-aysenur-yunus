<?php
/*
DOSYA: ortak/mesajlar.php
KİM: Kişi 3
NE YAPAR: Sayfanın $hata ve $tamam yazılarını kırmızı ve yeşil kutuda gösterir.
Her formlu sayfa bu dosyayı include eder. Böylece aynı 6 satırı her sayfada yazmayız.
*/
?>
<!-- Hata yazısı varsa kırmızı kutu. -->
<?php if (isset($hata) && $hata != "") { ?>
    <!-- Hatanın kendisi. -->
    <p class="hata"><?php echo temiz_yaz($hata); ?></p>
<?php } ?>
<!-- Başarı yazısı varsa yeşil kutu. -->
<?php if (isset($tamam) && $tamam != "") { ?>
    <!-- Başarı yazısı. -->
    <p class="tamam"><?php echo temiz_yaz($tamam); ?></p>
<?php } ?>
