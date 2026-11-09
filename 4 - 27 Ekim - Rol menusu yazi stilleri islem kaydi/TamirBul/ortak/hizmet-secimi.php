<?php
/*
DOSYA: ortak/hizmet-secimi.php
KİM: Kişi 3
NE YAPAR: Talep formunda hizmet seçim kartlarını çizer. Talep oluştur ve talep düzenle kullanır.
Çağırmadan önce sayfa $secili_hizmet değişkenini doldurur (0: hizmet seçilmedi).
Her kartın data-kategori değeri hizmetin kategorisi, data-cihazlar değeri hizmetin cihaz türleridir (örnek: "2,4").
data-cihazlar boşsa hizmet kategorideki bütün cihazlar içindir.
js/site.js (7. bölüm) kategori veya cihaz türü değişince sadece uygun kartları gösterir.
Örnek: Akıllı Telefon seçilince "Bilgisayar formatı" gizlenir.
Sunucu da kontrol eder: hizmet_kategoride_mi ve hizmet_cihaza_uygun_mu.
*/

// HARİTA: Kategoriye özel hizmet seçimi (form parçası)
// Sayfa vermediyse hizmet seçilmemiş sayılır.
if (!isset($secili_hizmet)) {
    // Seçili hizmet yok.
    $secili_hizmet = 0;
}

// Hizmet-cihaz tablosu eski veritabanında yoksa oluştururuz. Yoksa aşağıdaki sorgu çalışmaz.
hizmet_cihaz_tablosunu_hazirla();
// Sitede açık olan hizmetler. Kategori adı ikon seçmek için gerekir.
// Alt sorgu: hizmetin cihaz türü numaraları virgülle. GROUP_CONCAT satırları tek yazıda birleştirir. Örnek: "2,4"
$secim_hizmetler = satirlar("SELECT hizmetler.id, hizmetler.ad, hizmetler.aciklama, hizmetler.baslangic_fiyat, hizmetler.kategori_id, kategoriler.ad AS kategori_adi, (SELECT GROUP_CONCAT(cihaz_turu_id) FROM hizmet_cihazlari WHERE hizmet_cihazlari.hizmet_id = hizmetler.id) AS cihazlar FROM hizmetler INNER JOIN kategoriler ON kategoriler.id = hizmetler.kategori_id WHERE hizmetler.aktif = 1 ORDER BY hizmetler.kategori_id, hizmetler.ad");
?>
<!-- Hizmet seçimi. id: js/site.js bu kutuyu bulur. -->
<div id="hizmet-secimi" class="form-group">
    <!-- Başlık. -->
    <p class="hizmet-secimi-baslik">Hizmet seçin <span class="kucuk-etiket">(zorunlu değil, seçtiğiniz cihaza göre değişir)</span></p>
    <!-- Kategori seçilmeden görünen bilgi. JS kategori seçilince gizler. -->
    <p id="hizmet-kategori-bekle" class="kucuk-etiket">Önce yukarıdan ana kategoriyi ve cihaz türünü seçin, o cihazın hizmetleri burada çıkar.</p>
    <!-- Kartların ızgarası. -->
    <div class="hizmet-kartlari">
        <!-- İlk kart her zaman görünür: hizmet seçmeden devam. value 0: hizmet yok. -->
        <label class="hizmet-karti hizmet-yok">
            <!-- Seçim yuvarlağı. Seçili hizmet yoksa bu seçili gelir. -->
            <input type="radio" name="hizmet_id" value="0" <?php if ($secili_hizmet == 0) { echo "checked"; } ?>>
            <!-- Kartın içi. -->
            <span class="hizmet-karti-ici">
                <!-- Hareketli ikon. -->
                <span class="ikon ikon-salla">🤔</span>
                <!-- Ad. -->
                <strong>Emin değilim</strong>
                <!-- Açıklama. -->
                <small>Tamirci arızaya bakıp karar versin.</small>
            </span>
        </label>
        <!-- Her hizmet bir kart. -->
        <?php foreach ($secim_hizmetler as $secim_hizmeti) { ?>
            <!-- Kart. data-kategori ve data-cihazlar: JS bu değerlere bakıp gösterir veya gizler. -->
            <label class="hizmet-karti" data-kategori="<?php echo (int) $secim_hizmeti["kategori_id"]; ?>" data-cihazlar="<?php echo temiz_yaz((string) $secim_hizmeti["cihazlar"]); ?>">
                <!-- Seçim yuvarlağı. Seçili hizmet buysa checked gelir. -->
                <input type="radio" name="hizmet_id" value="<?php echo (int) $secim_hizmeti["id"]; ?>" <?php if ($secili_hizmet == $secim_hizmeti["id"]) { echo "checked"; } ?>>
                <!-- Kartın içi. -->
                <span class="hizmet-karti-ici">
                    <!-- Kategorinin hareketli ikonu. -->
                    <span class="ikon ikon-zipla"><?php echo kategori_ikonu($secim_hizmeti["kategori_adi"]); ?></span>
                    <!-- Hizmet adı. -->
                    <strong><?php echo temiz_yaz($secim_hizmeti["ad"]); ?></strong>
                    <!-- Kısa açıklama. -->
                    <small><?php echo temiz_yaz($secim_hizmeti["aciklama"]); ?></small>
                    <!-- Başlangıç fiyatı. -->
                    <span class="fiyat"><?php echo fiyat_yaz($secim_hizmeti["baslangic_fiyat"]); ?>'den başlayan</span>
                </span>
            </label>
        <?php } ?>
    </div>
    <!-- Seçilen kategoride hizmet yoksa görünen bilgi. JS gösterir. -->
    <p id="hizmet-yok-bilgisi" class="kucuk-etiket gizli">Bu cihaz için kayıtlı hizmet yok. "Emin değilim" ile devam edebilirsiniz.</p>
</div>
