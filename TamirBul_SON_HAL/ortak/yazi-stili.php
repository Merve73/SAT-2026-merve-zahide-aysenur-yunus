<?php
/*
DOSYA: ortak/yazi-stili.php
KİM: Kişi 4
NE YAPAR: Yönetim panelinden değişen yazıların görünüşünü (renk, boyut, kalınlık, eğiklik,
hizalama, yazı tipi) yönetir. Seçimler yazi_stilleri tablosunda durur.
ortak/hazirla.php bu dosyayı her sayfada yükler.

EZBER İÇİN FONKSİYON LİSTESİ:
  yazi_stili("anasayfa_baslik")       etikete eklenecek style="..." yazısını verir, seçim yoksa boş
  yazi_stili_kaydet("anahtar", $dizi)  yönetim formundan gelen seçimleri kaydeder
  yazi_stili_oku("anahtar")            kayıtlı seçimleri dizi olarak verir (formu doldurmak için)
  yazi_stili_tablosunu_hazirla()       tablo yoksa oluşturur (eski veritabanları için)

Örnek kullanım:  <h2<?php echo yazi_stili("anasayfa_baslik"); ?>>Başlık</h2>
*/

// HARİTA: Yazı stili seçenekleri
// Yönetim ekranındaki Word benzeri araç çubuğunun listesi (ortak/stil-araclari.php çizer).
// Kalın ve Eğik tek düğmedir: basılıysa seçili, değilse varsayılan.
// Her sütun için: ekranda yazan ad ve seçenekler. Seçenek: kod => array(ekranda yazan ad, CSS).
// Kod veritabanına yazılır. CSS sadece buradan gelir, böylece kimse forma kötü CSS yazamaz.
// Yeni renk veya boyut eklemek için ilgili listeye bir satır yazmak yeterli.
$yazi_stil_secenekleri = array(
    "renk" => array(
        "ad" => "Renk",
        "secenekler" => array(
            "siyah" => array("Siyah", "color: #0f172a;"),
            "beyaz" => array("Beyaz", "color: #ffffff;"),
            "gri" => array("Gri", "color: #64748b;"),
            "lacivert" => array("Lacivert", "color: #1e3a8a;"),
            "mavi" => array("Mavi", "color: #2563eb;"),
            "acik_mavi" => array("Açık mavi", "color: #60a5fa;"),
            "yesil" => array("Yeşil", "color: #10b981;"),
            "kirmizi" => array("Kırmızı", "color: #dc2626;"),
            "turuncu" => array("Turuncu", "color: #f59e0b;"),
            "sari" => array("Sarı", "color: #facc15;"),
            "mor" => array("Mor", "color: #7c3aed;"),
            "pembe" => array("Pembe", "color: #db2777;")
        )
    ),
    "boyut" => array(
        "ad" => "Boyut",
        "secenekler" => array(
            "cok_kucuk" => array("Çok küçük", "font-size: 12px;"),
            "kucuk" => array("Küçük", "font-size: 14px;"),
            "normal" => array("Normal", "font-size: 16px;"),
            "orta" => array("Orta", "font-size: 20px;"),
            "buyuk" => array("Büyük", "font-size: 28px;"),
            "cok_buyuk" => array("Çok büyük", "font-size: 40px;"),
            "dev" => array("Dev", "font-size: 56px;")
        )
    ),
    "kalinlik" => array(
        "ad" => "Kalın",
        "secenekler" => array(
            "kalin" => array("Kalın", "font-weight: 800;")
        )
    ),
    "egik" => array(
        "ad" => "Eğik",
        "secenekler" => array(
            "egik" => array("Eğik", "font-style: italic;")
        )
    ),
    "hizalama" => array(
        "ad" => "Hizalama",
        "secenekler" => array(
            "sol" => array("Sola", "text-align: left;"),
            "orta" => array("Ortaya", "text-align: center;"),
            "sag" => array("Sağa", "text-align: right;")
        )
    ),
    "yazi_tipi" => array(
        "ad" => "Yazı tipi",
        "secenekler" => array(
            "arial" => array("Arial", "font-family: Arial, sans-serif;"),
            "verdana" => array("Verdana", "font-family: Verdana, sans-serif;"),
            "tahoma" => array("Tahoma", "font-family: Tahoma, sans-serif;"),
            "georgia" => array("Georgia", "font-family: Georgia, serif;"),
            "times" => array("Times New Roman", "font-family: 'Times New Roman', serif;"),
            "courier" => array("Courier New", "font-family: 'Courier New', monospace;")
        )
    )
);

// HARİTA: Yazı stili tablosu (eski veritabanına ekleme)
// Tablo yoksa oluşturur. baslat.bat eski veritabanını yeniden yüklemediği için gerekir.
// Yönetimdeki "Site yazıları" ve "Sayfa yazıları" sayfaları açılınca çağrılır.
function yazi_stili_tablosunu_hazirla() {
    // IF NOT EXISTS: tablo zaten varsa hiçbir şey yapmaz.
    sorgula("CREATE TABLE IF NOT EXISTS yazi_stilleri (
        id INT AUTO_INCREMENT PRIMARY KEY,
        anahtar VARCHAR(80) NOT NULL UNIQUE,
        renk VARCHAR(20) NOT NULL DEFAULT '',
        boyut VARCHAR(20) NOT NULL DEFAULT '',
        kalinlik VARCHAR(20) NOT NULL DEFAULT '',
        egik VARCHAR(20) NOT NULL DEFAULT '',
        hizalama VARCHAR(20) NOT NULL DEFAULT '',
        yazi_tipi VARCHAR(20) NOT NULL DEFAULT ''
    ) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci");
}

// HARİTA: Yazı stilini okuma
// Bir yazının kayıtlı seçimlerini dizi olarak verir. Kayıt yoksa bütün seçimler boş.
// Örnek sonuç: array("renk" => "kirmizi", "boyut" => "", ...)
function yazi_stili_oku($anahtar) {
    // Seçenek listesini fonksiyonun içine alırız.
    global $yazi_stil_secenekleri;
    // Bu yazının satırını okuruz. Tablo yoksa sorgu boş döner, sayfa bozulmaz.
    $satir = tek_satir("SELECT * FROM yazi_stilleri WHERE anahtar = '" . temizle($anahtar) . "'");
    // Sonucu bu diziye yazarız.
    $secimler = array();
    // Her sütun için (renk, boyut ...):
    foreach ($yazi_stil_secenekleri as $sutun => $bilgi) {
        // Önce boş kabul ederiz. Boş: sitenin kendi görünüşü.
        $secimler[$sutun] = "";
        // Satır varsa tablodaki değeri alırız.
        if ($satir) {
            // Örnek: "kirmizi"
            $secimler[$sutun] = $satir[$sutun];
        }
    }
    // Diziyi veririz.
    return $secimler;
}

// Etikete eklenecek style yazısını verir. Seçim yoksa boş yazı verir, etiket eski haliyle kalır.
// Örnek sonuç: ' style="color: #dc2626; font-size: 28px;"'
function yazi_stili($anahtar) {
    // Seçenek listesini fonksiyonun içine alırız.
    global $yazi_stil_secenekleri;
    // Kayıtlı seçimleri okuruz.
    $secimler = yazi_stili_oku($anahtar);
    // CSS parçalarını bu yazıya ekleriz.
    $css = "";
    // Her sütun için:
    foreach ($secimler as $sutun => $kod) {
        // Seçilen kod listede varsa CSS'ini ekleriz. Listede olmayan kod yok sayılır.
        if (isset($yazi_stil_secenekleri[$sutun]["secenekler"][$kod])) {
            // Listedeki 2. değer CSS yazısıdır.
            $css = $css . $yazi_stil_secenekleri[$sutun]["secenekler"][$kod][1] . " ";
        }
    }
    // Hiç seçim yoksa:
    if ($css == "") {
        // Boş yazı. Etikete bir şey eklenmez.
        return "";
    }
    // Başında boşluk olan style yazısı. trim sondaki boşluğu siler.
    return ' style="' . trim($css) . '"';
}

// HARİTA: Yazı stilini kaydetme
// Formdan gelen seçimleri kaydeder. $gelen örneği: array("renk" => "kirmizi", "boyut" => "buyuk")
function yazi_stili_kaydet($anahtar, $gelen) {
    // Seçenek listesini fonksiyonun içine alırız.
    global $yazi_stil_secenekleri;
    // Kaydedilecek sütunlar.
    $bilgiler = array();
    // Her sütun için:
    foreach ($yazi_stil_secenekleri as $sutun => $bilgi) {
        // Önce boş kabul ederiz.
        $kod = "";
        // Formda bu sütun geldiyse, yazıysa ve listede varsa kabul ederiz. Uydurma değer boş kalır.
        // is_string: biri yazı yerine liste gönderirse sayfa çökmesin.
        if (isset($gelen[$sutun]) && is_string($gelen[$sutun]) && isset($bilgi["secenekler"][$gelen[$sutun]])) {
            // Seçilen kod.
            $kod = $gelen[$sutun];
        }
        // Sütuna yazarız.
        $bilgiler[$sutun] = $kod;
    }
    // Bu yazının satırı var mı?
    $var_mi = sayi_getir("SELECT COUNT(*) FROM yazi_stilleri WHERE anahtar = '" . temizle($anahtar) . "'");
    // Varsa:
    if ($var_mi > 0) {
        // Satırı güncelleriz.
        guncelle("yazi_stilleri", $bilgiler, "anahtar = '" . temizle($anahtar) . "'");
    } else {
        // Yoksa anahtarı da ekleyip yeni satır açarız.
        $bilgiler["anahtar"] = $anahtar;
        // Yeni satır.
        kaydet("yazi_stilleri", $bilgiler);
    }
}
