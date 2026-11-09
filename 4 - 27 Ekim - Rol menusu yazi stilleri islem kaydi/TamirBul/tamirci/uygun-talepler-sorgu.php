<?php
/*
DOSYA: tamirci/uygun-talepler-sorgu.php
KİM: Kişi 2
NE YAPAR: Tamircinin görebileceği talepleri bulur, $uygun_talepler dizisine yazar. Ekrana bir şey yazmaz.
panel.php ve gelen-talepler.php kullanır. Çağırmadan önce sayfa $benim_id değişkenini doldurur.
Bir talep şu üç koşulu sağlarsa listeye girer:
1) Durumu beklemede (teklif bekliyor)
2) Kategorisi tamircinin yetkinliklerinden biri
3) İli tamircinin iliyle aynı
*/

// HARİTA: Tamirciye uygun talepler (il + yetkinlik eşleştirme)
// Tamircinin ili.
$ben = tek_satir("SELECT sehir FROM kullanicilar WHERE id = $benim_id");
// İli temizleriz.
$benim_sehir = temizle($ben["sehir"]);
// Sorguyu parça parça kuruyoruz ki her parçayı açıklayabilelim.
// Alınacak sütunlar: talep, kategori adı, müşteri adı, varsa benim teklifimin fiyatı ve süresi.
$sorgu = "SELECT talepler.*, kategoriler.ad AS kategori_adi, kullanicilar.ad AS musteri_ad, kullanicilar.soyad AS musteri_soyad, benim_teklif.fiyat AS benim_fiyat, benim_teklif.sure AS benim_sure FROM talepler";
// Kategori adını almak için kategoriler tablosu.
$sorgu = $sorgu . " INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id";
// Müşterinin adını almak için kullanicilar tablosu.
$sorgu = $sorgu . " INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id";
// INNER JOIN yetkinlikler: sadece tamircinin kategorilerindeki talepler kalır.
$sorgu = $sorgu . " INNER JOIN tamirci_yetkinlikleri ON tamirci_yetkinlikleri.kategori_id = talepler.kategori_id AND tamirci_yetkinlikleri.tamirci_id = $benim_id";
// LEFT JOIN teklifler: teklif verdiysem fiyatım gelir, vermediysem boş (NULL) gelir.
$sorgu = $sorgu . " LEFT JOIN teklifler AS benim_teklif ON benim_teklif.talep_id = talepler.id AND benim_teklif.tamirci_id = $benim_id";
// Sadece teklif bekleyen ve benim ilimdeki talepler.
$sorgu = $sorgu . " WHERE talepler.durum = 'beklemede' AND talepler.sehir = '$benim_sehir'";
// En yeni talep en üstte.
$sorgu = $sorgu . " ORDER BY talepler.id DESC";
// Sorguyu çalıştırıp satırları alırız.
$uygun_talepler = satirlar($sorgu);
