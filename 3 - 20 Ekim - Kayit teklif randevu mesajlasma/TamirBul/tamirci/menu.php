<?php
/*
DOSYA: tamirci/menu.php
KİM: Kişi 2
NE YAPAR: Tamirci sayfalarının ikinci menüsü ve onay uyarısı.
$onayli değişkeni sayfada da kullanılır: 0 bekliyor, 1 onaylı, 2 reddedildi.
Onay her seferinde tablodan okunur. Yönetici onaylayınca sayfayı yenilemek yeter.
*/

// HARİTA: Tamirci onay durumu kontrolü
// Tamircinin onay durumu.
$onayli = sayi_getir("SELECT onayli FROM kullanicilar WHERE id = " . (int) $_SESSION["kullanici_id"]);
?>
<!-- İkinci menü şeridi. -->
<nav class="alt-menu">
    <!-- Linkler. -->
    <div class="kapsayici">
        <!-- Panel. -->
        <a href="panel.php">Kontrol Paneli</a>
        <!-- Uygun talepler. -->
        <a href="gelen-talepler.php">Uygun Talepler</a>
        <!-- Randevular. -->
        <a href="randevularim.php">Randevularım</a>
        <!-- Profil. -->
        <a href="profil.php">Profil</a>
    </div>
</nav>
<!-- Onay bekliyorsa sarı uyarı. -->
<?php if ($onayli == 0) { ?>
    <!-- Uyarı. -->
    <div class="uyari">Hesabınız henüz onaylı değil. Yönetici onaylayınca talepleri görüp teklif verebilirsiniz.</div>
<?php } ?>
<!-- Reddedildiyse sarı uyarı. -->
<?php if ($onayli == 2) { ?>
    <!-- Uyarı. -->
    <div class="uyari">Başvurunuz yönetici tarafından reddedildi. Ayrıntı için iletişim sayfasından yazın.</div>
<?php } ?>
