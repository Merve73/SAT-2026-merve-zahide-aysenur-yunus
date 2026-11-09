<?php
/*
DOSYA: ortak/baglanti.php
KİM: Kişi 3
NE YAPAR: MySQL veritabanına bağlanır. Veritabanı ile ilgili bütün kısa fonksiyonlar burada.
Sayfalar bu dosyayı doğrudan değil, ortak/hazirla.php üzerinden çağırır.

EZBER İÇİN FONKSİYON LİSTESİ:
  satirlar("SELECT ...")             çok satır okur, foreach ile gezilir
  tek_satir("SELECT ...")            tek satır okur, yoksa null
  sayi_getir("SELECT COUNT(*) ...")  tek sayı okur
  kaydet("tablo", array(...))        yeni satır ekler (INSERT), yeni numarayı verir
  guncelle("tablo", array(...), "id = 5")   satırı değiştirir (UPDATE)
  sil("tablo", "id = 5")             satırı siler (DELETE)
  kaydet_veya_guncelle("tablo", array(...), $id)   id 0 ise ekler, değilse günceller
  temizle($yazi)                     SQL'e koymadan önce yazıyı güvenli yapar
  ayar_getir("telefon", "")          site yazısını okur
*/

// HARİTA: Veritabanı bağlantısı (sunucu, kullanıcı, şifre)
// Bağlantı hatası olursa PHP sayfayı öldürmesin, biz kontrol edelim.
mysqli_report(MYSQLI_REPORT_OFF);
// Bağlanır: sunucu (bu bilgisayar), kullanıcı (root), şifre (boş), veritabanı adı.
$baglanti = mysqli_connect("127.0.0.1", "root", "", "tamirbul");
// Bağlandıysa true, bağlanamadıysa false olur.
$veritabani_hazir = ($baglanti != false);
// Bağlanamadıysa sebebi. Ekrandaki sarı uyarıda görünür.
$veritabani_hata = mysqli_connect_error();
// Bağlandıysa:
if ($veritabani_hazir) {
    // Türkçe harfler bozulmasın diye karakter seti.
    mysqli_set_charset($baglanti, "utf8mb4");
}

// HARİTA: Sorgu çalıştırma (sorgula, satirlar, tek_satir, sayi_getir)
// Bütün sorgular bu fonksiyondan geçer. Veritabanı yoksa false verir, sayfa çökmez.
function sorgula($sorgu) {
    // global: dosyanın en üstündeki $baglanti değişkenini fonksiyonun içinde kullanırız.
    global $baglanti;
    // Bağlantı yoksa:
    if (!$baglanti) {
        // Başarısız.
        return false;
    }
    // Sorguyu çalıştırıp sonucu veririz.
    return mysqli_query($baglanti, $sorgu);
}

// SELECT sorgusunun bütün satırlarını dizi olarak verir. Örnek: satirlar("SELECT * FROM hizmetler")
function satirlar($sorgu) {
    // Boş liste ile başlarız.
    $liste = array();
    // Sorguyu çalıştırırız.
    $sonuc = sorgula($sorgu);
    // Sonuç geldiyse her satırı listeye ekleriz.
    while ($sonuc && $satir = mysqli_fetch_assoc($sonuc)) {
        // Satırı listeye ekleriz.
        $liste[] = $satir;
    }
    // Listeyi veririz.
    return $liste;
}

// Tek satır verir. Satır yoksa null. Örnek: tek_satir("SELECT * FROM kullanicilar WHERE id = 3")
function tek_satir($sorgu) {
    // Bütün satırları okuruz.
    $liste = satirlar($sorgu);
    // Hiç satır yoksa:
    if (count($liste) == 0) {
        // Boş (null) veririz.
        return null;
    }
    // İlk satırı veririz.
    return $liste[0];
}

// COUNT(*) gibi tek sayı veren sorgunun sonucunu verir. Örnek: 5
function sayi_getir($sorgu) {
    // Tek satırı okuruz.
    $satir = tek_satir($sorgu);
    // Satır yoksa:
    if (!$satir) {
        // Sıfır veririz.
        return 0;
    }
    // reset: dizinin ilk değerini verir (sütunun adı ne olursa olsun). Sayıya çeviririz.
    return (int) reset($satir);
}

// HARİTA: SQL injection koruması (temizle fonksiyonu)
// Kullanıcıdan gelen yazıyı SQL'e koymadan önce güvenli yapar. Tırnak (') sorguyu bozamaz.
function temizle($yazi) {
    // Bağlantıyı fonksiyonun içine alırız.
    global $baglanti;
    // Bağlantı yoksa:
    if (!$baglanti) {
        // Yazıyı aynen veririz.
        return $yazi;
    }
    // Tehlikeli işaretlerin önüne \ koyar.
    return mysqli_real_escape_string($baglanti, $yazi);
}

// HARİTA: Kayıt ekleme, güncelleme, silme (kaydet, guncelle, sil)
// Diziyi SQL'in anlayacağı "ad = 'Ali', soyad = 'Kaya'" yazısına çevirir. kaydet ve guncelle kullanır.
// null değer NULL yazılır (örnek: seçilmeyen hizmet).
function alanlar_yazisi($bilgiler) {
    // Parçaları bu diziye toplarız.
    $parcalar = array();
    // Her sütun ve değeri için:
    foreach ($bilgiler as $sutun => $deger) {
        // Değer boşsa (null) SQL'de NULL yazılır.
        if ($deger === null) {
            // Örnek: hizmet_id = NULL
            $parcalar[] = "$sutun = NULL";
        } else {
            // Değer temizlenip tırnak içine konur. Örnek: ad = 'Ali'
            $parcalar[] = "$sutun = '" . temizle($deger) . "'";
        }
    }
    // Parçaları virgülle birleştiririz.
    return implode(", ", $parcalar);
}

// Tabloya yeni satır ekler. Yeni satırın numarasını verir, olmazsa 0.
// Örnek: kaydet("mesajlar", array("ad" => $ad, "konu" => $konu))
function kaydet($tablo, $bilgiler) {
    // Bağlantıyı fonksiyonun içine alırız.
    global $baglanti;
    // INSERT INTO tablo SET ad = 'Ali', ... (MySQL bu yazımı da kabul eder, daha kısadır.)
    if (sorgula("INSERT INTO $tablo SET " . alanlar_yazisi($bilgiler))) {
        // Yeni satırın numarası.
        return mysqli_insert_id($baglanti);
    }
    // Eklenemedi.
    return 0;
}

// Satırları değiştirir. Örnek: guncelle("talepler", array("durum" => "iptal"), "id = 5")
function guncelle($tablo, $bilgiler, $kosul) {
    // UPDATE tablo SET ... WHERE kosul
    return sorgula("UPDATE $tablo SET " . alanlar_yazisi($bilgiler) . " WHERE $kosul") != false;
}

// Yönetim formları için: numara 0 ise yeni satır ekler, değilse o numaralı satırı günceller.
// Örnek: kaydet_veya_guncelle("kategoriler", array("ad" => $ad), $id)
function kaydet_veya_guncelle($tablo, $bilgiler, $id) {
    // Yeni kayıt:
    if ($id == 0) {
        // Ekleriz.
        return kaydet($tablo, $bilgiler);
    }
    // Var olan kaydı güncelleriz.
    return guncelle($tablo, $bilgiler, "id = " . (int) $id);
}

// Satırları siler. Örnek: sil("hizmetler", "id = 5")
function sil($tablo, $kosul) {
    // DELETE FROM tablo WHERE kosul
    return sorgula("DELETE FROM $tablo WHERE $kosul") != false;
}

// HARİTA: Site yazılarını okuma (ayar_getir)
// site_ayarlari tablosundan bir yazı okur. Bulamazsa $yoksa_bu verilir. Örnek: ayar_getir("telefon", "")
function ayar_getir($anahtar, $yoksa_bu) {
    // Bu anahtarın satırını okuruz.
    $satir = tek_satir("SELECT deger FROM site_ayarlari WHERE anahtar = '" . temizle($anahtar) . "'");
    // Satır yoksa:
    if (!$satir) {
        // Yedek yazıyı veririz.
        return $yoksa_bu;
    }
    // Tablodaki yazıyı veririz.
    return $satir["deger"];
}

// HARİTA: İl-ilçe doğrulama (sunucu tarafı)
// Formdan gelen il ve ilçe tabloda var mı, ilçe o ile mi ait? Doğruysa true.
function il_ilce_dogru_mu($il, $ilce) {
    // INNER JOIN: ilçeyi bağlı olduğu il ile eşleştirir.
    return sayi_getir("SELECT COUNT(*) FROM ilceler INNER JOIN iller ON iller.id = ilceler.il_id WHERE iller.ad = '" . temizle($il) . "' AND ilceler.ad = '" . temizle($ilce) . "'") > 0;
}

// Seçilen cihaz türü seçilen kategoriye mi ait? Örnek: Buzdolabı, Mobil kategorisinde olmaz.
function cihaz_turu_dogru_mu($kategori_id, $cihaz_turu) {
    // Bu kategoride bu adda tür var mı sayarız.
    return sayi_getir("SELECT COUNT(*) FROM cihaz_turleri WHERE kategori_id = " . (int) $kategori_id . " AND ad = '" . temizle($cihaz_turu) . "'") > 0;
}

// Bu numarada kategori var mı?
function kategori_var_mi($kategori_id) {
    // Sayı 0'dan büyükse var.
    return sayi_getir("SELECT COUNT(*) FROM kategoriler WHERE id = " . (int) $kategori_id) > 0;
}

// Seçilen hizmet sitede açık mı ve seçilen kategoriye mi ait? Örnek: "Ekran Değişimi" Beyaz Eşya'da olmaz.
function hizmet_kategoride_mi($hizmet_id, $kategori_id) {
    // Hem numarası hem kategorisi tutan açık hizmet sayısı 0'dan büyükse doğru.
    return sayi_getir("SELECT COUNT(*) FROM hizmetler WHERE aktif = 1 AND id = " . (int) $hizmet_id . " AND kategori_id = " . (int) $kategori_id) > 0;
}

// HARİTA: Hizmetin cihaz türleri (hizmet_cihazlari tablosu)
// Tablo yoksa oluşturur. baslat.bat eski veritabanını yeniden yüklemediği için gerekir.
// Hizmet seçimi olan sayfalar (talep oluştur, talep düzenle, yönetim hizmetler) çağırır.
function hizmet_cihaz_tablosunu_hazirla() {
    // IF NOT EXISTS: tablo zaten varsa hiçbir şey yapmaz.
    sorgula("CREATE TABLE IF NOT EXISTS hizmet_cihazlari (id INT AUTO_INCREMENT PRIMARY KEY, hizmet_id INT NOT NULL, cihaz_turu_id INT NOT NULL)");
}

// Hizmet seçilen cihaz türüne uygun mu? Örnek: "Bilgisayar formatı" Akıllı Telefon'a uygun değil.
// Hizmetin hiç cihaz satırı yoksa kategorideki bütün cihazlara uygundur.
function hizmet_cihaza_uygun_mu($hizmet_id, $kategori_id, $cihaz_adi) {
    // Hizmetin kaç cihaz satırı var?
    $satir_sayisi = sayi_getir("SELECT COUNT(*) FROM hizmet_cihazlari WHERE hizmet_id = " . (int) $hizmet_id);
    // Hiç satır yoksa bütün cihazlara uygun.
    if ($satir_sayisi == 0) {
        // Uygun.
        return true;
    }
    // Satırlardan biri seçilen cihaz türü mü? Cihaz adı ve kategorisiyle cihaz_turleri tablosunda eşleştiririz.
    return sayi_getir("SELECT COUNT(*) FROM hizmet_cihazlari INNER JOIN cihaz_turleri ON cihaz_turleri.id = hizmet_cihazlari.cihaz_turu_id WHERE hizmet_cihazlari.hizmet_id = " . (int) $hizmet_id . " AND cihaz_turleri.kategori_id = " . (int) $kategori_id . " AND cihaz_turleri.ad = '" . temizle($cihaz_adi) . "'") > 0;
}

// Hizmetin cihaz türlerini baştan yazar: önce eskileri siler, sonra işaretlenenleri ekler.
// Sadece hizmetin kategorisindeki cihaz türleri eklenir. $cihaz_listesi onay kutularından gelen dizi. Örnek: array(2, 4)
function hizmet_cihazlarini_kaydet($hizmet_id, $kategori_id, $cihaz_listesi) {
    // Eski satırları sileriz.
    sil("hizmet_cihazlari", "hizmet_id = " . (int) $hizmet_id);
    // İşaretlenen her cihaz türü için:
    foreach ($cihaz_listesi as $cihaz_turu_id) {
        // Bu cihaz türü hizmetin kategorisinde var mı? Yoksa eklemeyiz.
        if (sayi_getir("SELECT COUNT(*) FROM cihaz_turleri WHERE id = " . (int) $cihaz_turu_id . " AND kategori_id = " . (int) $kategori_id) > 0) {
            // Yeni satır ekleriz.
            kaydet("hizmet_cihazlari", array("hizmet_id" => (int) $hizmet_id, "cihaz_turu_id" => (int) $cihaz_turu_id));
        }
    }
}

// HARİTA: Tamirci yetkinlikleri (okuma ve kaydetme)
// Tamircinin yetkinliklerini virgüllü yazar. Örnek: "Mobil & Bilişim, Beyaz Eşya"
function yetkinlik_metni($tamirci_id) {
    // Adları bu diziye toplarız.
    $adlar = array();
    // Tamircinin her kategorisi için:
    foreach (satirlar("SELECT kategoriler.ad FROM tamirci_yetkinlikleri INNER JOIN kategoriler ON kategoriler.id = kategori_id WHERE tamirci_id = " . (int) $tamirci_id) as $kategori) {
        // Adı ekleriz.
        $adlar[] = $kategori["ad"];
    }
    // implode diziyi araya ", " koyarak tek yazı yapar.
    return implode(", ", $adlar);
}

// Tamircinin yetkinliklerini baştan yazar: önce eskileri siler, sonra seçilenleri ekler.
// $kategori_listesi onay kutularından gelen dizi. Örnek: array(1, 3)
function yetkinlikleri_kaydet($tamirci_id, $kategori_listesi) {
    // Eski yetkinlikleri sileriz.
    sil("tamirci_yetkinlikleri", "tamirci_id = " . (int) $tamirci_id);
    // Seçilen her kategori için:
    foreach ($kategori_listesi as $kategori_id) {
        // Böyle bir kategori varsa:
        if (kategori_var_mi($kategori_id)) {
            // Yeni satır ekleriz.
            kaydet("tamirci_yetkinlikleri", array("tamirci_id" => $tamirci_id, "kategori_id" => $kategori_id));
        }
    }
}

// HARİTA: Tamirci puan ortalaması (AVG)
// Tamircinin ortalama puanını yazar. Örnek: "4.8 / 5 (3 değerlendirme)"
function tamirci_puan_metni($tamirci_id) {
    // AVG ortalama, COUNT adet hesaplar. Puan 0 olanlar (puanlanmamış) sayılmaz.
    $satir = tek_satir("SELECT AVG(puan) AS ortalama, COUNT(*) AS adet FROM talepler WHERE tamirci_id = " . (int) $tamirci_id . " AND puan > 0");
    // Hiç puan yoksa:
    if (!$satir || $satir["adet"] == 0) {
        // Puan yok yazısı.
        return "Henüz değerlendirme yok";
    }
    // number_format: virgülden sonra 1 hane. Örnek: 4.8
    return number_format($satir["ortalama"], 1) . " / 5 (" . $satir["adet"] . " değerlendirme)";
}
