<?php
// Tamirci sayfalarinin ikinci menusu.
// Onay bilgisini oturumdan degil tablodan okuruz.
// Yonetici onaylayinca tamirci yeniden giris yapmadan sayfayi yenileyince gorur.

$onayli = 0;
if (isset($veritabani_hazir) && $veritabani_hazir && isset($_SESSION["kullanici_id"])) {
    $benim_id = (int) $_SESSION["kullanici_id"];
    $ben_sonuc = mysqli_query($baglanti, "SELECT onayli FROM kullanicilar WHERE id = $benim_id LIMIT 1");
    if ($ben_sonuc && mysqli_num_rows($ben_sonuc) == 1) {
        $ben = mysqli_fetch_assoc($ben_sonuc);
        $onayli = (int) $ben["onayli"];
    }
}
?>
<nav class="alt-menu">
    <div class="kapsayici">
        <a href="panel.php">Özet</a>
        <a href="gelen-talepler.php">Gelen talepler</a>
        <a href="randevularim.php">Randevularım</a>
        <a href="profil.php">Profil</a>
    </div>
</nav>
<?php if ($onayli != 1) { ?>
    <div class="uyari">Hesabınız henüz onaylı değil. Yönetici onaylayınca talep alabilirsiniz.</div>
<?php } ?>
