<?php
/*
DOSYA: tamirci/onay-durumu.php
KİM: Kişi 2
NE YAPAR: Tamircinin onay durumunu okur ve onaylı değilse sarı uyarı gösterir.
$onayli değişkeni sayfada da kullanılır: 0 bekliyor, 1 onaylı, 2 reddedildi.
Onay her seferinde tablodan okunur. Yönetici onaylayınca sayfayı yenilemek yeter.
Tamirci menüsü artık üstteki açılır menüdedir (ortak/baslik.php).
*/

// HARİTA: Tamirci onay durumu kontrolü
// Tamircinin onay durumu.
$onayli = sayi_getir("SELECT onayli FROM kullanicilar WHERE id = " . (int) $_SESSION["kullanici_id"]);
?>
<!-- Onay bekliyorsa sarı uyarı. -->
<?php if ($onayli == 0) { ?>
    <!-- Uyarı. Kum saati ikonu döner. -->
    <div class="uyari"><?php echo ikon("saat"); ?> Hesabınız henüz onaylı değil. Yönetici onaylayınca talepleri görüp teklif verebilirsiniz.</div>
<?php } ?>
<!-- Reddedildiyse sarı uyarı. -->
<?php if ($onayli == 2) { ?>
    <!-- Uyarı. -->
    <div class="uyari"><?php echo ikon("uyari"); ?> Başvurunuz yönetici tarafından reddedildi. Ayrıntı için iletişim sayfasından yazın.</div>
<?php } ?>
