<?php
/*
DOSYA: ortak/yetkinlik-secimi.php
KİM: Kişi 3
NE YAPAR: Tamircinin tamir edebildiği kategoriler için onay kutularını çizer.
Tamirci kayıt ve tamirci profil sayfası kullanır.
Çağırmadan önce sayfa $secili_yetkinlikler dizisini doldurur. Örnek: array(1, 3)
Kutuların adı yetkinlikler[] olduğu için PHP seçilenleri dizi olarak alır.
*/

// Sayfa vermediyse boş dizi.
if (!isset($secili_yetkinlikler)) {
    // Hiçbiri seçili değil.
    $secili_yetkinlikler = array();
}

// Kategorileri okuruz.
$yetkinlik_kategorileri = satirlar("SELECT * FROM kategoriler ORDER BY id");
?>
<!-- Yetkinlik alanı. -->
<div class="form-group">
    <!-- Alan başlığı. -->
    <label class="section-label">Tamir Edebildiğiniz Cihaz Türleri (Yetkinlikleriniz)</label>
    <!-- Kısa açıklama. -->
    <p class="form-help">Sadece seçtiğiniz kategorilerdeki ve kendi ilinizdeki talepler panelinize düşer.</p>
    <!-- Kutuların ızgarası. -->
    <div class="competencies-grid">
        <!-- Her kategori bir onay kutusu. -->
        <?php foreach ($yetkinlik_kategorileri as $kategori) { ?>
            <!-- Tıklanabilir kart. -->
            <label class="checkbox-card">
                <!-- in_array: bu kategori seçililer arasında varsa kutu işaretli gelir. -->
                <input type="checkbox" name="yetkinlikler[]" value="<?php echo (int) $kategori["id"]; ?>" <?php if (in_array((int) $kategori["id"], $secili_yetkinlikler)) { echo "checked"; } ?>>
                <!-- Kategori adı. -->
                <span><?php echo temiz_yaz($kategori["ad"]); ?></span>
            </label>
        <?php } ?>
    </div>
</div>
