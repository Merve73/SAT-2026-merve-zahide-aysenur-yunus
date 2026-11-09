<?php
/*
DOSYA: ortak/guvenlik.php
KİM: Kişi 3
NE YAPAR: Oturumu açar. Her sayfanın kullandığı küçük yardımcı fonksiyonlar burada.
Sayfalar bu dosyayı doğrudan değil, ortak/hazirla.php üzerinden çağırır.

EZBER İÇİN FONKSİYON LİSTESİ:
  form_geldi_mi()          form gönderildi mi?
  formdan_al("ad")         formdaki yazıyı okur
  gelen_sayi("id")         formdaki veya adresteki sayıyı okur
  sadece_rol("admin")      sayfayı sadece o rol açar
  yonlendir("panel.php")   başka sayfaya gider
  temiz_yaz($yazi)         ekrana güvenli yazar
  simdi()                  şimdiki tarih ve saat
*/

// HARİTA: Oturum (session) açma
// Oturum daha önce açılmadıysa açarız. Giriş yapan kişiyi sayfadan sayfaya hatırlar.
if (session_status() == PHP_SESSION_NONE) {
    // Sunucu oturumu 30 gün saklasın. Yoksa "Beni hatırla" 24 dakika sonra unutulur.
    ini_set("session.gc_maxlifetime", 60 * 60 * 24 * 30);
    // Oturumu başlatır.
    session_start();
}

// ----- Giriş ve yetki -----

// Giriş yapılmış mı? Oturumda kullanici_id varsa evet.
function giris_yapildi_mi() {
    // isset: değişken var mı diye bakar.
    return isset($_SESSION["kullanici_id"]);
}

// HARİTA: Sayfa yetki kontrolü (sadece_rol)
// Sayfayı sadece bu roldeki kişi açabilir. Örnek: sadece_rol("admin")
// Başka biri açarsa giriş sayfasına gönderilir.
function sadece_rol($rol) {
    // Giriş yoksa veya rol farklıysa:
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != $rol) {
        // Giriş sayfasına gönderilir. Bu fonksiyon alt klasördeki sayfalarda kullanılır.
        yonlendir("../giris.php");
    }
}

// HARİTA: Sayfa yönlendirme (header Location)
// Tarayıcıyı başka sayfaya gönderir ve bu sayfayı durdurur.
function yonlendir($adres) {
    // Location başlığı tarayıcıya "şu sayfaya git" der.
    header("Location: " . $adres);
    // Sayfanın geri kalanı çalışmasın.
    exit;
}

// ----- Formdan veri okuma -----

// HARİTA: Form verisi okuma ($_POST, $_GET)
// Form gönderildi mi? Sayfa ilk açılışta false, form gönderilince true.
function form_geldi_mi() {
    // POST: form gönderme yöntemi.
    return $_SERVER["REQUEST_METHOD"] == "POST";
}

// Formdan (POST) gelen yazıyı baş ve sondaki boşlukları silerek verir. Yoksa boş yazı.
function formdan_al($ad) {
    // Kutu formda varsa:
    if (isset($_POST[$ad])) {
        // trim baştaki ve sondaki boşlukları siler.
        return trim($_POST[$ad]);
    }
    // Kutu yoksa boş yazı.
    return "";
}

// Formdan (POST) ya da adres çubuğundan (GET) gelen sayıyı verir. Yoksa 0.
// Örnek: talep-detay.php?id=5 -> gelen_sayi("id") = 5
function gelen_sayi($ad) {
    // Önce formda ararız.
    if (isset($_POST[$ad])) {
        // (int) yazıyı sayıya çevirir. Harf gelirse 0 olur.
        return (int) $_POST[$ad];
    }
    // Sonra adres çubuğunda ararız.
    if (isset($_GET[$ad])) {
        // Sayıya çeviririz.
        return (int) $_GET[$ad];
    }
    // İkisinde de yoksa 0.
    return 0;
}

// ----- Ekrana yazma -----

// HARİTA: XSS koruması (temiz_yaz fonksiyonu)
// Ekrana yazılacak yazıyı zararsız yapar. İçinde HTML kodu varsa çalışmaz, düz yazı görünür.
function temiz_yaz($metin) {
    // < > " ' işaretlerini HTML kodlarına çevirir.
    return htmlspecialchars($metin, ENT_QUOTES, "UTF-8");
}

// Fiyatı "1.500 TL" gibi yazar.
function fiyat_yaz($fiyat) {
    // 0 hane kuruş, "," ondalık, "." binlik ayracı.
    return number_format((float) $fiyat, 0, ",", ".") . " TL";
}

// Şimdiki tarih ve saat, veritabanının istediği biçimde. Örnek: 2026-10-19 14:30:00
function simdi() {
    // Y yıl, m ay, d gün, H saat, i dakika, s saniye.
    return date("Y-m-d H:i:s");
}

// Veritabanındaki tarihi "05 Ekim 2026" gibi yazar.
function tarih_yaz($tarih) {
    // strtotime yazıyı zamana çevirir.
    $zaman = strtotime($tarih);
    // Ay adları. 0. eleman boş ki 1 = Ocak olsun.
    $aylar = array("", "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık");
    // date("d") gün, date("n") ay numarası, date("Y") yıl.
    return date("d", $zaman) . " " . $aylar[(int) date("n", $zaman)] . " " . date("Y", $zaman);
}

// "Ayşe Yılmaz" yerine "Ayşe Y." yazar. Müşterinin soyadı tamirciye tam görünmez.
function kisa_ad($ad, $soyad) {
    // mb_substr Türkçe harfi bozmadan ilk harfi alır.
    return $ad . " " . mb_substr($soyad, 0, 1, "UTF-8") . ".";
}

// HARİTA: Talep durumları (beklemede, tamirci_atandi ...)
// Talep durumunun kodunu Türkçe yazar. Örnek: "beklemede" -> "Teklif bekliyor"
function durum_adi($durum) {
    // Kod => Türkçe ad tablosu.
    $adlar = array(
        "beklemede" => "Teklif bekliyor",
        "tamirci_atandi" => "Tamirci seçildi",
        "randevu_kuruldu" => "Randevu kuruldu",
        "tamirde" => "Tamirde",
        "tamamlandi" => "Tamamlandı",
        "iptal" => "İptal"
    );
    // Tabloda varsa Türkçe adı.
    if (isset($adlar[$durum])) {
        // Türkçe adı veririz.
        return $adlar[$durum];
    }
    // Yoksa kodun kendisi.
    return $durum;
}

// Durum etiketinin rengi için css sınıfı. Sarı bekliyor, yeşil bitti, kırmızı iptal, mavi diğer.
function durum_rozeti($durum) {
    // Teklif bekliyorsa sarı.
    if ($durum == "beklemede") {
        // Sarı etiket.
        return "badge badge-warning";
    }
    // Bittiyse yeşil.
    if ($durum == "tamamlandi") {
        // Yeşil etiket.
        return "badge badge-success";
    }
    // İptalse kırmızı.
    if ($durum == "iptal") {
        // Kırmızı etiket.
        return "badge badge-danger";
    }
    // Diğerleri mavi.
    return "badge badge-info";
}

// Randevu durumunun kodunu Türkçe yazar.
function randevu_durum_adi($durum) {
    // Kod => Türkçe ad tablosu.
    $adlar = array("bekliyor" => "Bekliyor", "tamamlandi" => "Tamamlandı", "iptal" => "İptal");
    // Tabloda varsa Türkçe adı.
    if (isset($adlar[$durum])) {
        // Türkçe adı veririz.
        return $adlar[$durum];
    }
    // Yoksa kodun kendisi.
    return $durum;
}

// ----- Şifre -----

// HARİTA: Şifre değiştirme
// Eski şifre doğruysa yenisini kaydeder. Hata varsa hata yazısını, başarılıysa boş yazı verir.
// Müşteri ve tamirci profil sayfaları kullanır.
function sifre_degistir($kullanici_id, $eski, $yeni, $yeni2) {
    // Kullanıcının kayıtlı şifresini (hash) okuruz.
    $kayit = tek_satir("SELECT sifre FROM kullanicilar WHERE id = " . (int) $kullanici_id);
    // Kullanıcı yoksa:
    if (!$kayit) {
        // Hata yazısı.
        return "Kullanıcı bulunamadı.";
    }
    // password_verify: yazılan eski şifre kayıtlı hash ile aynı mı?
    if (!password_verify($eski, $kayit["sifre"])) {
        // Hata yazısı.
        return "Eski şifre yanlış.";
    }
    // strlen: yazının uzunluğu.
    if (strlen($yeni) < 6) {
        // Hata yazısı.
        return "Yeni şifre en az 6 karakter olsun.";
    }
    // İki yeni şifre aynı olmalı.
    if ($yeni != $yeni2) {
        // Hata yazısı.
        return "Yeni şifre tekrarı aynı değil.";
    }
    // Yeni şifreyi geri çözülmeyen hash'e çevirip kaydederiz.
    guncelle("kullanicilar", array("sifre" => password_hash($yeni, PASSWORD_DEFAULT)), "id = " . (int) $kullanici_id);
    // Hata yok.
    return "";
}

// ----- Resim -----
// Veritabanında resim iki şekilde durur:
// 1) http ile başlayan internet adresi
// 2) yuklemeler/ klasöründeki dosyanın adı. Örnek: kategori_1729000000_123.jpg

// Resim değerini tarayıcının açabileceği adrese çevirir.
function resim_adresi($deger) {
    // $kok: alt klasördeki sayfada "../" olur (ortak/hazirla.php hesaplar).
    global $kok;
    // Resim yoksa boş.
    if ($deger == "") {
        // Boş yazı veririz.
        return "";
    }
    // http ile başlıyorsa internet adresidir, aynen kullanırız.
    if (substr($deger, 0, 4) == "http") {
        // Adresi aynen veririz.
        return $deger;
    }
    // Değilse yüklenmiş dosyadır.
    return $kok . "yuklemeler/" . $deger;
}

// HARİTA: Resimli kart (arka plan resmi)
// Kartın arka planina resim koyan style yazısını hazırlar. Resim yoksa boş yazı.
// Örnek sonuç: style="background-image: url('https://...');"
function arka_plan($deger) {
    // Resmin adresi.
    $adres = resim_adresi($deger);
    // Resim yoksa boş.
    if ($adres == "") {
        // Boş yazı veririz.
        return "";
    }
    // style yazısı.
    return "style=\"background-image: url('" . temiz_yaz($adres) . "');\"";
}

// HARİTA: Resim yükleme (dosya kontrolü ve kaydetme)
// Formdaki dosya kutusundan gelen resmi yuklemeler klasörüne kaydeder.
// Sonuç: dosya seçilmediyse "", dosya hatalıysa false, başarılıysa yeni dosya adı.
function resim_yukle($alan_adi, $on_ek) {
    // Dosya seçilmediyse boş yazı.
    if (!isset($_FILES[$alan_adi]) || $_FILES[$alan_adi]["error"] == UPLOAD_ERR_NO_FILE) {
        // Boş yazı: dosya seçilmedi.
        return "";
    }
    // Yükleme hatası olduysa (örnek: çok büyük dosya).
    if ($_FILES[$alan_adi]["error"] != UPLOAD_ERR_OK) {
        // Başarısız.
        return false;
    }
    // Dosya uzantısı küçük harfle. Örnek: Foto.JPG -> jpg
    $uzanti = strtolower(pathinfo($_FILES[$alan_adi]["name"], PATHINFO_EXTENSION));
    // İzin verilen uzantılar. .php gibi dosya yüklenemesin.
    $izinli = array("jpg", "jpeg", "png", "webp", "gif");
    // in_array: dizide var mı bakar.
    if (!in_array($uzanti, $izinli)) {
        // Başarısız: izinli uzantı değil.
        return false;
    }
    // En fazla 3 MB.
    if ($_FILES[$alan_adi]["size"] > 3 * 1024 * 1024) {
        // Başarısız: dosya çok büyük.
        return false;
    }
    // getimagesize: dosya gerçekten resim mi? Değilse false.
    if (getimagesize($_FILES[$alan_adi]["tmp_name"]) === false) {
        // Başarısız: resim değil.
        return false;
    }
    // __DIR__ bu dosyanın klasörü (ortak). Bir üstteki yuklemeler klasörüne kaydederiz.
    $klasor = __DIR__ . "/../yuklemeler/";
    // Klasör yoksa oluştururuz.
    if (!is_dir($klasor)) {
        // Klasörü oluştururuz.
        mkdir($klasor);
    }
    // Yeni dosya adı: ön ek + zaman + rastgele sayı. Aynı ad iki kez oluşmaz.
    $yeni_ad = $on_ek . "_" . time() . "_" . rand(100, 999) . "." . $uzanti;
    // Dosyayı geçici yerden bizim klasöre taşırız. Başarırsa adı veririz.
    if (move_uploaded_file($_FILES[$alan_adi]["tmp_name"], $klasor . $yeni_ad)) {
        // Yeni dosya adını veririz.
        return $yeni_ad;
    }
    // Taşınamadı.
    return false;
}

// Yönetim formundaki resim alanını okur (ortak/resim-alani.php).
// Dosya yüklendiyse onu, yüklenmediyse yazı kutusundaki adresi verir. Hatalıysa false.
function resim_formdan_al($on_ek) {
    // Önce dosyaya bakarız.
    $yuklenen = resim_yukle("resim_dosya", $on_ek);
    // Dosya hatalıysa false. (=== tam eşitlik, "" ile karışmasın.)
    if ($yuklenen === false) {
        // Başarısız.
        return false;
    }
    // Dosya yüklendiyse onun adı.
    if ($yuklenen != "") {
        // Yüklenen dosyanın adını veririz.
        return $yuklenen;
    }
    // Dosya yoksa yazı kutusundaki adres.
    $adres = formdan_al("resim_adresi");
    // strpbrk: yazıda ' " ( ) veya boşluk var mı bakar. Bunlar sayfanın stilini bozabilir.
    if (strpbrk($adres, "'\"() ") !== false) {
        // Başarısız.
        return false;
    }
    // Adres temiz.
    return $adres;
}
