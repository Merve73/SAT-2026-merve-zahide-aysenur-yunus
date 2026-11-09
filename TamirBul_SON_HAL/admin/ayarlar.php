<?php
/*
SAYFA: Site yazıları
DOSYA: admin/ayarlar.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Yazılar sayfalara göre gruplu durur: Genel, Ana sayfa, Hakkımızda, Sık sorulan sorular, Hizmetler,
  Tamirciler, İletişim, Talep formu, Sayfanın en altı. Liste ortak/site-yazilari.php içindedir.
- Her yazının üstünde sitede NEREDE göründüğü yazar. Her grubun "Sayfada gör" düğmesi o sayfayı yeni sekmede açar.
- Solda grupların listesi ve arama kutusu: "telefon" yazınca sadece telefon kutusu kalır.
- Her yazının "İlk haline dön" düğmesi var. Değiştirilen kutu sarı çizgiyle işaretlenir.
- Yazı stili (renk, boyut, K, T, hizalama) her yazının altında açılır kutudadır. Stil seçiliyse "Stil var" yazar.
- Listede olmayan eski yazılar en altta "Diğer yazılar" grubunda görünür.
- Kaydet düğmesi sayfanın altında sabit durur.
*/

// Sekme başlığı.
$sayfa_basligi = "Site yazıları";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");
// Stil tablosu eski veritabanında yoksa oluştururuz.
yazi_stili_tablosunu_hazirla();
// Listede olup veritabanında olmayan yazıları ilk değerleriyle ekleriz.
site_yazilarini_hazirla();

// Kaydettikten sonra adres ?kaydedildi=1 olur.
if (isset($_GET["kaydedildi"])) {
    // Başarı yazısı.
    $tamam = "Kaydedildi. Siteyi yenileyince yeni yazılar görünür.";
}

// HARİTA: Site yazılarını kaydetme (ayarlar)
// Form gönderildiyse ve yazılar liste olarak geldiyse:
if (form_geldi_mi() && isset($_POST["ayar"]) && is_array($_POST["ayar"])) {
    // Her kutu ayar[anahtar] adıyla gelir. foreach ile tek tek gezeriz.
    foreach ($_POST["ayar"] as $anahtar => $deger) {
        // Listedeki bilgisi (etiket, yer, ilk değer, tür). Listede yoksa boş dizi.
        $bilgi = site_yazisi_bilgisi($anahtar);
        // Veritabanında bu yazının satırı var mı?
        $satir_var = sayi_getir("SELECT COUNT(*) FROM site_ayarlari WHERE anahtar = '" . temizle($anahtar) . "'");
        // Değer yazıysa ve yazı ya listede ya veritabanında varsa kaydederiz. Uydurma anahtar kabul edilmez.
        if (is_string($deger) && (count($bilgi) > 0 || $satir_var > 0)) {
            // Baştaki ve sondaki boşlukları sileriz.
            $deger = trim($deger);
            // Sayı olan yazıysa (slayt süresi gibi):
            if (count($bilgi) > 0 && $bilgi[3] == "sayi") {
                // Sayıya çeviririz. Harf yazıldıysa 0 olur.
                $sayi = (int) $deger;
                // 1'den küçükse 1 yaparız (0 saniye olmaz).
                if ($sayi < 1) {
                    // En az 1.
                    $sayi = 1;
                }
                // Sayıyı yazıya çevirip saklarız.
                $deger = (string) $sayi;
            }
            // Satır varsa:
            if ($satir_var > 0) {
                // Değeri güncelleriz.
                guncelle("site_ayarlari", array("deger" => $deger), "anahtar = '" . temizle($anahtar) . "'");
            } else {
                // Yoksa yeni satır ekleriz. Etiket listeden gelir.
                kaydet("site_ayarlari", array("anahtar" => $anahtar, "deger" => $deger, "etiket" => $bilgi[0]));
            }
        }
    }
    // HARİTA: Yazı stillerini kaydetme
    // Stil kutuları stil[anahtar][renk] adıyla gelir. Geldiyse ve listeyse:
    if (isset($_POST["stil"]) && is_array($_POST["stil"])) {
        // Her yazının seçimleri için:
        foreach ($_POST["stil"] as $anahtar => $secimler) {
            // Seçimler liste olarak geldiyse:
            if (is_array($secimler)) {
                // Kaydederiz. Listede olmayan değerleri fonksiyon kendisi atar.
                yazi_stili_kaydet($anahtar, $secimler);
            }
        }
    }
    // Değişikliği kayda yazarız.
    log_yaz("Site yazıları kaydedildi", "");
    // Sayfayı yeniden açarız. Böylece sayfa yenilenince form ikinci kez gönderilmez.
    yonlendir("ayarlar.php?kaydedildi=1");
}

// HARİTA: Site yazılarını gruplu gösterme
// Veritabanındaki bütün yazıları okuruz.
$ayar_satirlari = satirlar("SELECT * FROM site_ayarlari ORDER BY id");
// Yazıları anahtara göre bir diziye koyarız. Örnek: $degerler["telefon"] = "0500..."
$degerler = array();
// Listede olmayan eski yazılar "Diğer yazılar" grubuna gider.
$diger_yazilar = array();
// Her satır için:
foreach ($ayar_satirlari as $satir) {
    // Değeri anahtarıyla saklarız.
    $degerler[$satir["anahtar"]] = $satir["deger"];
    // Listede yoksa:
    if (count(site_yazisi_bilgisi($satir["anahtar"])) == 0) {
        // Tür: uzun yazıysa büyük kutu.
        $tur = "kisa";
        // 60 harften uzunsa:
        if (mb_strlen($satir["deger"], "UTF-8") > 60) {
            // Büyük kutu.
            $tur = "uzun";
        }
        // Diğer gruba ekleriz. İlk değeri yok, şimdiki değeri yazarız.
        $diger_yazilar[$satir["anahtar"]] = array($satir["etiket"], "Bu yazının yeri listede yazılı değil", $satir["deger"], $tur);
    }
}
// Ekranda gösterilecek gruplar: listedeki gruplar.
$gosterilecek_gruplar = $site_yazilari_gruplari;
// Diğer yazı varsa:
if (count($diger_yazilar) > 0) {
    // Listenin sonuna "Diğer yazılar" grubunu ekleriz.
    $gosterilecek_gruplar["diger"] = array("baslik" => "Diğer yazılar", "ikon" => "belge", "aciklama" => "Veritabanında olup listede yeri yazılmamış yazılar.", "adres" => "index.php", "yazilar" => $diger_yazilar);
}
// Bütün yazıların sayısı (soldaki listede yazar).
$toplam_yazi = 0;
// Her grubun yazı sayısını toplarız.
foreach ($gosterilecek_gruplar as $grup) {
    // Ekleriz.
    $toplam_yazi = $toplam_yazi + count($grup["yazilar"]);
}

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Site yazıları</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Sitedeki bütün kısa yazılar burada, sayfalara göre gruplu. Her kutunun üstünde o yazının sitede <strong>nerede</strong> göründüğü yazar. Renk, boyut, kalın gibi ayarlar her kutunun altındaki <strong>Yazı stili</strong> düğmesinde. Uzun yazılar (Hakkımızda hikayesi) <a href="sayfa-icerik.php">Sayfa yazıları</a> ekranında.</p>
        <!-- Başarı kutusu. -->
        <?php include "../ortak/mesajlar.php"; ?>

        <!-- İki sütun: solda grup listesi ve arama, sağda form. -->
        <div class="yazi-yonetimi">
            <!-- Sol: arama ve gruplar. Büyük ekranda kayarken yerinde durur. -->
            <aside class="yazi-menusu">
                <!-- Arama kutusu. js/site.js (12. bölüm) yazdıkça kutuları süzer. -->
                <label for="yazi-ara">Yazı ara</label>
                <!-- Arama kutusu. type="search": sağında silme çarpısı çıkar. -->
                <input id="yazi-ara" type="search" placeholder="Örnek: telefon, başlık, düğme" autocomplete="off">
                <!-- Toplam yazı sayısı. -->
                <p class="yazi-sayisi"><?php echo $toplam_yazi; ?> yazı, <?php echo count($gosterilecek_gruplar); ?> grup</p>
                <!-- Grupların listesi. Tıklayınca o gruba kayar. -->
                <nav class="grup-linkleri">
                    <!-- Her grup bir link. -->
                    <?php foreach ($gosterilecek_gruplar as $grup_kodu => $grup) { ?>
                        <!-- Link: #grup-genel gibi. -->
                        <a href="#grup-<?php echo temiz_yaz($grup_kodu); ?>"><?php echo ikon($grup["ikon"]); ?> <span><?php echo temiz_yaz($grup["baslik"]); ?></span> <em><?php echo count($grup["yazilar"]); ?></em></a>
                    <?php } ?>
                </nav>
                <!-- Uzun yazılar sayfasına link. -->
                <a class="btn btn-sm btn-outline" href="sayfa-icerik.php"><?php echo ikon("belge"); ?> Sayfa yazıları</a>
            </aside>

            <!-- Sağ: bütün yazıların formu. -->
            <form id="yazi-formu" class="yazi-formu" method="post" action="ayarlar.php">
                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                <?php echo guvenlik_alani(); ?>
                <!-- Arama hiçbir yazıyı bulamazsa görünen yazı. js/site.js gösterir. -->
                <p id="arama-sonuc-yok" class="bos-liste gizli">Aradığınız kelimeyle eşleşen yazı yok.</p>

                <!-- Her grup bir kutu. -->
                <?php foreach ($gosterilecek_gruplar as $grup_kodu => $grup) { ?>
                    <!-- Grup kutusu. id: soldaki link buraya kaydırır. -->
                    <section class="yazi-grubu" id="grup-<?php echo temiz_yaz($grup_kodu); ?>">
                        <!-- Grubun başlık satırı. -->
                        <div class="yazi-grubu-baslik">
                            <!-- İkon kutusu. -->
                            <span class="kart-ikonu"><?php echo ikon($grup["ikon"]); ?></span>
                            <!-- Başlık ve açıklama. -->
                            <div>
                                <!-- Grubun adı. -->
                                <h2><?php echo temiz_yaz($grup["baslik"]); ?></h2>
                                <!-- Açıklama. -->
                                <p><?php echo temiz_yaz($grup["aciklama"]); ?></p>
                            </div>
                            <!-- Sayfayı yeni sekmede açan düğme. target="_blank": yeni sekme. rel: yeni sekme bu sayfaya dokunamaz. -->
                            <a class="btn btn-sm btn-outline" href="<?php echo $kok . temiz_yaz($grup["adres"]); ?>" target="_blank" rel="noopener">Sayfada gör <?php echo ikon("ok"); ?></a>
                        </div>

                        <!-- Gruptaki her yazı. $bilgi: array(etiket, yer, ilk değer, tür) -->
                        <?php foreach ($grup["yazilar"] as $anahtar => $bilgi) { ?>
                            <?php
                            // Kutuda görünecek değer: veritabanındaki, yoksa ilk değer.
                            $deger = $bilgi[2];
                            // Veritabanında varsa:
                            if (isset($degerler[$anahtar])) {
                                // Veritabanındaki değer.
                                $deger = $degerler[$anahtar];
                            }
                            // Yazının türü: kisa, uzun veya sayi.
                            $tur = $bilgi[3];
                            // Sayı değilse yazı stili ayarı çıkar.
                            $stil_olsun_mu = ($tur != "sayi");
                            // Araç çubuğu ve önizleme hangi yazı için çizilecek.
                            $stil_anahtar = $anahtar;
                            // Önizlemede yazının kendisi görünür.
                            $stil_ornek_yazi = $deger;
                            // Arama bu yazıların içinde arar: kutu adı, yeri, anahtarı ve değeri.
                            $arama_yazisi = $bilgi[0] . " " . $bilgi[1] . " " . $anahtar . " " . $deger . " " . $grup["baslik"];
                            // Kayıtlı stil var mı? Bir seçim bile doluysa "Stil var" yazar.
                            $stil_var = false;
                            // Stil olan yazıysa:
                            if ($stil_olsun_mu) {
                                // Kayıtlı seçimler.
                                $secimler = yazi_stili_oku($anahtar);
                                // Her seçim için:
                                foreach ($secimler as $secim) {
                                    // Boş değilse:
                                    if ($secim != "") {
                                        // Stil var.
                                        $stil_var = true;
                                    }
                                }
                            }
                            ?>
                            <!-- Bir yazının alanı. stil-alani: js/site.js araç çubuğunu, kutuyu ve önizlemeyi bu kutuda arar. -->
                            <!-- data-arama: arama kutusu bu yazıya bakar. -->
                            <div class="yazi-alani <?php if ($stil_olsun_mu) { echo "stil-alani"; } ?>" data-arama="<?php echo temiz_yaz($arama_yazisi); ?>">
                                <!-- Üst satır: kutunun adı ve sitede nerede olduğu. -->
                                <div class="yazi-ust">
                                    <!-- Kutunun adı. -->
                                    <label for="ayar-<?php echo temiz_yaz($anahtar); ?>"><?php echo temiz_yaz($bilgi[0]); ?></label>
                                    <!-- Değiştirildi işareti. js/site.js kutu değişince gösterir. -->
                                    <span class="degisti-isareti">Değiştirildi</span>
                                </div>
                                <!-- Sitede nerede göründüğü. -->
                                <p class="yazi-yeri"><?php echo ikon("konum"); ?> <?php echo temiz_yaz($bilgi[1]); ?></p>
                                <!-- Türe göre kutu. -->
                                <?php if ($tur == "uzun") { ?>
                                    <!-- Büyük kutu. name="ayar[anahtar]" PHP'ye dizi olarak gider. data-ilk: "İlk haline dön" bu yazıyı koyar. -->
                                    <textarea class="stil-hedef yazi-kutusu" id="ayar-<?php echo temiz_yaz($anahtar); ?>" name="ayar[<?php echo temiz_yaz($anahtar); ?>]" rows="3" data-ilk="<?php echo temiz_yaz($bilgi[2]); ?>"><?php echo temiz_yaz($deger); ?></textarea>
                                <?php } else if ($tur == "sayi") { ?>
                                    <!-- Sayı kutusu. min="1": 1'den küçük sayı seçilemez. -->
                                    <input class="yazi-kutusu kutu-dar" id="ayar-<?php echo temiz_yaz($anahtar); ?>" name="ayar[<?php echo temiz_yaz($anahtar); ?>]" type="number" min="1" max="60" value="<?php echo temiz_yaz($deger); ?>" data-ilk="<?php echo temiz_yaz($bilgi[2]); ?>">
                                <?php } else { ?>
                                    <!-- Tek satır kutu. -->
                                    <input class="stil-hedef yazi-kutusu" id="ayar-<?php echo temiz_yaz($anahtar); ?>" name="ayar[<?php echo temiz_yaz($anahtar); ?>]" type="text" value="<?php echo temiz_yaz($deger); ?>" data-ilk="<?php echo temiz_yaz($bilgi[2]); ?>">
                                <?php } ?>
                                <!-- Alt satır: düğmeler. -->
                                <div class="yazi-alt">
                                    <!-- İlk haline dön. Diğer yazılarda ilk değer bilinmediği için çıkmaz. -->
                                    <?php if ($grup_kodu != "diger") { ?>
                                        <!-- Düğme. js/site.js kutuya data-ilk yazısını koyar. Kaydet'e basınca kalıcı olur. -->
                                        <button type="button" class="ilk-hale-don"><?php echo ikon("geri"); ?> İlk haline dön</button>
                                    <?php } ?>
                                    <!-- Yazıysa stil ayarı: tıklayınca açılan kutu. Stil seçiliyse açık başlar. -->
                                    <?php if ($stil_olsun_mu) { ?>
                                        <!-- details: tıklayınca açılır. -->
                                        <details class="stil-ayari" <?php if ($stil_var) { echo "open"; } ?>>
                                            <!-- Görünen düğme. -->
                                            <summary><?php echo ikon("kalem"); ?> Yazı stili <?php if ($stil_var) { ?><span class="stil-var-rozeti">Stil var</span><?php } ?></summary>
                                            <!-- Araç çubuğu: yazı tipi, boyut, K, T, renk, hizalama, temizle. -->
                                            <?php include "../ortak/stil-araclari.php"; ?>
                                            <!-- Koyu ve açık zeminde önizleme. -->
                                            <?php include "../ortak/stil-onizleme.php"; ?>
                                        </details>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </section>
                <?php } ?>

                <!-- Kaydet. Sayfanın altında sabit durur, uzun sayfada aşağı inmeye gerek kalmaz. -->
                <div class="sabit-kaydet kaydet-seridi">
                    <!-- Kaydedilmemiş değişiklik uyarısı. js/site.js gösterir. -->
                    <span id="kaydedilmedi-uyarisi" class="kaydedilmedi gizli"><?php echo ikon("uyari"); ?> Kaydedilmemiş değişiklik var</span>
                    <!-- Kaydet düğmesi. -->
                    <button class="btn btn-primary" type="submit"><?php echo ikon("onay"); ?> Bütün yazıları kaydet</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
