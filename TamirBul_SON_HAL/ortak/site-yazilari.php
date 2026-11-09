<?php
/*
DOSYA: ortak/site-yazilari.php
KİM: Kişi 4
NE YAPAR: Sitede yönetimden değişen bütün kısa yazıların LİSTESİ burada durur.
Her yazının: kutu adı (etiket), sitede nerede göründüğü (yer), ilk değeri (varsayilan) ve türü yazar.
Yazılar sayfalara göre gruplanır. admin/ayarlar.php bu listeyi okuyup gruplu bir form çizer.
Sayfalar yazıyı yazi_getir("anahtar") ile okur. Örnek: echo temiz_yaz(yazi_getir("anasayfa_rozet"));
Yeni bir yazı eklemek için ilgili grubun "yazilar" dizisine bir satır yazmak yeterli.
Veritabanında satırı yoksa site_yazilarini_hazirla() onu ilk değeriyle kendisi ekler.
Türler: "kisa" tek satır kutu, "uzun" büyük kutu, "sayi" sadece sayı (yazı stili çıkmaz).
*/

// HARİTA: Site yazılarının listesi (gruplar, yer bilgisi, ilk değer)
// Her grup: başlık, ikon, açıklama, "Sayfada gör" adresi ve yazıları.
// Her yazı: anahtar => array(etiket, sitede nerede, ilk değer, tür)
$site_yazilari_gruplari = array(

    // 1) Her sayfada görünen yazılar.
    "genel" => array(
        // Grubun başlığı.
        "baslik" => "Genel (her sayfada)",
        // Grubun ikonu (ortak/ikonlar.php).
        "ikon" => "ev",
        // Grubun kısa açıklaması.
        "aciklama" => "Bütün sayfalarda görünen yazılar.",
        // "Sayfada gör" düğmesinin açtığı sayfa.
        "adres" => "index.php",
        // Bu gruptaki yazılar.
        "yazilar" => array(
            // Logo yazısı.
            "site_adi" => array("Site adı (logo)", "Her sayfanın sol üstündeki logo ve sayfanın en altındaki ad", "TamirBul", "kisa")
        )
    ),

    // 2) Ana sayfanın en üstündeki fotoğraflı alan.
    "anasayfa_ust" => array(
        // Grubun başlığı.
        "baslik" => "Ana sayfa: en üstteki büyük alan",
        // Grubun ikonu.
        "ikon" => "resim",
        // Grubun açıklaması.
        "aciklama" => "Siteye girenin ilk gördüğü fotoğraflı alan. Fotoğrafların kendisi Ana sayfa resimleri sayfasından değişir.",
        // Sayfada gör.
        "adres" => "index.php",
        // Yazılar.
        "yazilar" => array(
            // Başlığın üstündeki küçük hap.
            "anasayfa_rozet" => array("Başlığın üstündeki küçük yazı", "Ana sayfa > büyük başlığın hemen üstündeki yuvarlak köşeli küçük kutu", "Hızlı teklif, güvenilir tamirci", "kisa"),
            // En büyük yazı.
            "anasayfa_baslik" => array("Büyük başlık", "Ana sayfa > fotoğrafın ortasındaki en büyük yazı", "Teknolojik Cihazınız veya Ev Aletiniz mi Bozuldu?", "kisa"),
            // Başlığın altındaki yazı.
            "anasayfa_aciklama" => array("Açıklama", "Ana sayfa > büyük başlığın altındaki yazı", "Cihaz türünüzü, markasını ve arızasını seçin. Yakınınızdaki onaylı tamircilerden hızlıca teklif alın, karşılaştırın ve seçin!", "uzun"),
            // Mavi düğme.
            "anasayfa_buton" => array("Birinci düğme", "Ana sayfa > mavi düğme (talep formunu açar)", "Hemen Tamir Talebi Oluştur", "kisa"),
            // Çerçeveli düğme.
            "anasayfa_buton2" => array("İkinci düğme", "Ana sayfa > çerçeveli düğme (talep takibini açar)", "Talebimi Sorgula", "kisa"),
            // Güven şeridinin birinci sözü.
            "anasayfa_guven1" => array("Güven şeridi: 1. söz", "Ana sayfa > düğmelerin altındaki yeşil tikli üç sözün birincisi", "Ücretsiz teklif", "kisa"),
            // İkinci söz.
            "anasayfa_guven2" => array("Güven şeridi: 2. söz", "Ana sayfa > düğmelerin altındaki yeşil tikli üç sözün ikincisi", "Onaylı tamirciler", "kisa"),
            // Üçüncü söz.
            "anasayfa_guven3" => array("Güven şeridi: 3. söz", "Ana sayfa > düğmelerin altındaki yeşil tikli üç sözün üçüncüsü", "Gerçek müşteri puanları", "kisa"),
            // Fotoğrafların değişme süresi.
            "slayt_suresi" => array("Fotoğraf değişme süresi (saniye)", "Ana sayfa > arkadaki fotoğraflar bu kadar saniyede bir değişir", "5", "sayi")
        )
    ),

    // 3) Ana sayfanın aşağısındaki bölümler.
    "anasayfa_bolumler" => array(
        // Grubun başlığı.
        "baslik" => "Ana sayfa: bölüm başlıkları",
        // Grubun ikonu.
        "ikon" => "duzen",
        // Grubun açıklaması.
        "aciklama" => "Ana sayfada aşağı inince görünen bölümlerin başlıkları. Kartların içi kendi sayfalarından değişir (Nasıl çalışır, Kategoriler, Hizmetler).",
        // Sayfada gör.
        "adres" => "index.php#nasil-calisir",
        // Yazılar.
        "yazilar" => array(
            // Adımlar bölümü.
            "adimlar_baslik" => array("Adımlar bölümünün başlığı", "Ana sayfa > numaralı dört kartın üstündeki başlık", "Nasıl Çalışır?", "kisa"),
            // Kategoriler bölümü.
            "kategoriler_baslik" => array("Kategoriler bölümünün başlığı", "Ana sayfa > kategori kartlarının üstündeki başlık", "Hangi Cihazlar İçin Hizmet Veriyoruz?", "kisa"),
            // Hizmetler bölümü.
            "hizmetler_baslik" => array("Hizmetler bölümünün başlığı", "Ana sayfa > en alttaki hizmet kartlarının üstündeki başlık", "Popüler Hizmetler", "kisa"),
            // Kaç hizmet kartı.
            "anasayfa_hizmet_sayisi" => array("Gösterilecek hizmet sayısı", "Ana sayfa > en altta bu kadar hizmet kartı görünür", "3", "sayi")
        )
    ),

    // 4) Hakkımızda sayfası.
    "hakkimizda" => array(
        // Grubun başlığı.
        "baslik" => "Hakkımızda sayfası",
        // Grubun ikonu.
        "ikon" => "bilgi",
        // Grubun açıklaması.
        "aciklama" => "Hikayemiz bölümündeki uzun yazı Sayfa yazıları ekranından değişir.",
        // Sayfada gör.
        "adres" => "hakkimizda.php",
        // Yazılar.
        "yazilar" => array(
            // Kapaktaki küçük hap.
            "hakkimizda_rozet" => array("Kapaktaki küçük yazı", "Hakkımızda > en üstteki fotoğrafta başlığın üstündeki küçük kutu", "Bilgisayar programcılığı dönem projesi", "kisa"),
            // Kapak başlığı.
            "hakkimizda_kapak_baslik" => array("Kapak başlığı", "Hakkımızda > en üstteki fotoğrafın ortasındaki büyük yazı", "Bozulan cihazla güvenilir ustayı buluşturuyoruz", "kisa"),
            // Kapak açıklaması.
            "hakkimizda_kapak_aciklama" => array("Kapak açıklaması", "Hakkımızda > kapak başlığının altındaki yazı", "TamirBul; arızalı cihazı olan herkesin, bulunduğu ildeki onaylı tamircilerden kolayca teklif alıp karşılaştırabileceği bir tamir pazaryeridir.", "uzun"),
            // Misyon kartı.
            "hakkimizda_misyon" => array("Misyonumuz kartı", "Hakkımızda > Neye inanıyoruz? > soldaki kart", "Tamirci aramayı telefon telefon dolaşmaktan kurtarmak. Arızanızı bir kez yazın, uygun ustalar size gelsin.", "uzun"),
            // Vizyon kartı.
            "hakkimizda_vizyon" => array("Vizyonumuz kartı", "Hakkımızda > Neye inanıyoruz? > ortadaki kart", "Her ilde, her cihaz için güvenilir bir tamirciye birkaç tıklamayla ulaşılabilen bir Türkiye.", "uzun"),
            // Değerler kartı.
            "hakkimizda_degerler" => array("Değerlerimiz kartı", "Hakkımızda > Neye inanıyoruz? > sağdaki kart", "Şeffaf fiyat, dürüst değerlendirme ve kişisel bilgilerin korunması. Şifreler bile bizim tarafımızdan okunamaz.", "uzun"),
            // Alt çağrı başlığı.
            "hakkimizda_cagri_baslik" => array("En alttaki çağrı başlığı", "Hakkımızda > sayfanın en altındaki mavi fotoğraflı alanın başlığı", "Cihazınız mı bozuldu?", "kisa"),
            // Alt çağrı açıklaması.
            "hakkimizda_cagri_aciklama" => array("En alttaki çağrı açıklaması", "Hakkımızda > en alttaki mavi alanda başlığın altındaki yazı", "Arızayı birkaç dakikada yazın, bölgenizdeki onaylı tamircilerden teklifler gelsin.", "uzun")
        )
    ),

    // 5) Hakkımızda sayfasındaki sık sorulan sorular.
    "sss" => array(
        // Grubun başlığı.
        "baslik" => "Sık sorulan sorular",
        // Grubun ikonu.
        "ikon" => "soru",
        // Grubun açıklaması.
        "aciklama" => "Hakkımızda sayfasının altındaki, tıklayınca açılan sorular. Soruyu boş bırakırsanız o soru sitede görünmez.",
        // Sayfada gör.
        "adres" => "hakkimizda.php#sss",
        // Yazılar.
        "yazilar" => array(
            // 1. soru.
            "sss_1_soru" => array("1. soru", "Sık sorulan sorular > birinci soru", "TamirBul'u kullanmak ücretli mi?", "kisa"),
            // 1. cevap.
            "sss_1_cevap" => array("1. cevap", "Birinci soruya tıklayınca açılan yazı", "Hayır. Talep açmak, teklif almak ve teklifleri karşılaştırmak ücretsizdir. Tamir ücretini, teklifini kabul ettiğiniz tamirciye ödersiniz.", "uzun"),
            // 2. soru.
            "sss_2_soru" => array("2. soru", "Sık sorulan sorular > ikinci soru", "Tamirciler nasıl seçiliyor?", "kisa"),
            // 2. cevap.
            "sss_2_cevap" => array("2. cevap", "İkinci soruya tıklayınca açılan yazı", "Tamirci; işletme adı, adresi ve uzman olduğu cihazlarla başvurur. Yönetici bilgileri kontrol edip onaylar. Onaylanmayan tamirci sitede görünmez ve teklif veremez.", "uzun"),
            // 3. soru.
            "sss_3_soru" => array("3. soru", "Sık sorulan sorular > üçüncü soru", "Talep açmak için üye olmam gerekiyor mu?", "kisa"),
            // 3. cevap.
            "sss_3_cevap" => array("3. cevap", "Üçüncü soruya tıklayınca açılan yazı", "Evet, ücretsiz bir müşteri hesabı yeterli. Böylece gelen teklifleri ve tamirciyle yazışmalarınızı tek yerden görürsünüz.", "uzun"),
            // 4. soru.
            "sss_4_soru" => array("4. soru", "Sık sorulan sorular > dördüncü soru", "Talebimin durumunu nasıl takip ederim?", "kisa"),
            // 4. cevap.
            "sss_4_cevap" => array("4. cevap", "Dördüncü soruya tıklayınca açılan yazı", "Talep açınca size bir takip kodu verilir. Talep Takip sayfasına bu kodu yazarak talebinizin hangi aşamada olduğunu görebilirsiniz. Giriş yaptıysanız panelinizde de görünür.", "uzun"),
            // 5. soru.
            "sss_5_soru" => array("5. soru", "Sık sorulan sorular > beşinci soru", "Gelen teklifi kabul etmek zorunda mıyım?", "kisa"),
            // 5. cevap.
            "sss_5_cevap" => array("5. cevap", "Beşinci soruya tıklayınca açılan yazı", "Hayır. Teklifleri fiyat, süre ve tamircinin puanına göre karşılaştırırsınız. Hiçbiri uygun değilse kabul etmeyebilirsiniz.", "uzun"),
            // 6. soru.
            "sss_6_soru" => array("6. soru", "Sık sorulan sorular > altıncı soru", "Ben tamirciyim, nasıl katılırım?", "kisa"),
            // 6. cevap.
            "sss_6_cevap" => array("6. cevap", "Altıncı soruya tıklayınca açılan yazı", "Menüdeki Tamirci Olarak Katıl sayfasından başvurun. Yönetici onayladıktan sonra ilinizdeki talepleri görüp teklif vermeye başlarsınız.", "uzun")
        )
    ),

    // 6) Hizmetler sayfası.
    "hizmetler" => array(
        // Grubun başlığı.
        "baslik" => "Hizmetler sayfası",
        // Grubun ikonu.
        "ikon" => "anahtar",
        // Grubun açıklaması.
        "aciklama" => "Hizmet kartlarının kendisi Hizmetler yönetim sayfasından değişir.",
        // Sayfada gör.
        "adres" => "hizmetler.php",
        // Yazılar.
        "yazilar" => array(
            // Kapaktaki küçük hap.
            "hizmetler_rozet" => array("Kapaktaki küçük yazı", "Hizmetler > en üstteki fotoğrafta başlığın üstündeki küçük kutu", "Şeffaf başlangıç fiyatları", "kisa"),
            // Kapak açıklaması.
            "hizmetler_sayfa_aciklama" => array("Kapak açıklaması", "Hizmetler > en üstteki 'Hizmetlerimiz' başlığının altındaki yazı", "Ekran değişiminden beyaz eşya bakımına kadar en çok istenen tamirler ve başlangıç fiyatları burada. Kesin fiyatı, arızanızı inceleyen onaylı tamirciler size teklif olarak verir.", "uzun"),
            // Alt çağrı başlığı.
            "hizmetler_cagri_baslik" => array("En alttaki çağrı başlığı", "Hizmetler > sayfanın en altındaki mavi alanın başlığı", "Aradığınız hizmeti bulamadınız mı?", "kisa"),
            // Alt çağrı açıklaması.
            "hizmetler_cagri_aciklama" => array("En alttaki çağrı açıklaması", "Hizmetler > en alttaki mavi alanda başlığın altındaki yazı", "Listede olmasa da sorun değil. Arızanızı yazın, bölgenizdeki tamirciler teklif versin.", "uzun")
        )
    ),

    // 7) Tamirciler sayfası.
    "tamirciler" => array(
        // Grubun başlığı.
        "baslik" => "Tamirciler sayfası",
        // Grubun ikonu.
        "ikon" => "kisiler",
        // Grubun açıklaması.
        "aciklama" => "Tamirci kartları, onaylı tamircilerin kendi bilgilerinden gelir.",
        // Sayfada gör.
        "adres" => "tamirciler.php",
        // Yazılar.
        "yazilar" => array(
            // Kapaktaki küçük hap.
            "tamirciler_rozet" => array("Kapaktaki küçük yazı", "Tamirciler > en üstteki fotoğrafta başlığın üstündeki küçük kutu", "Hepsi yönetici onaylı", "kisa"),
            // Kapak açıklaması.
            "tamirciler_sayfa_aciklama" => array("Kapak açıklaması", "Tamirciler > en üstteki 'Onaylı Tamirciler' başlığının altındaki yazı", "Her biri yönetici onayından geçmiş tamirciler ve teknik servisler. Puanlar, işi tamamlanmış gerçek müşterilerin değerlendirmelerinden hesaplanır.", "uzun"),
            // Alt çağrı başlığı.
            "tamirciler_cagri_baslik" => array("En alttaki çağrı başlığı", "Tamirciler > sayfanın en altındaki mavi alanın başlığı", "Tamirci misiniz? Aramıza katılın", "kisa"),
            // Alt çağrı açıklaması.
            "tamirciler_cagri_aciklama" => array("En alttaki çağrı açıklaması", "Tamirciler > en alttaki mavi alanda başlığın altındaki yazı", "İlinizdeki tamir taleplerini görün, teklif verin, yeni müşterilere ulaşın. Başvuru ücretsizdir.", "uzun")
        )
    ),

    // 8) İletişim bilgileri.
    "iletisim" => array(
        // Grubun başlığı.
        "baslik" => "İletişim sayfası ve bilgileri",
        // Grubun ikonu.
        "ikon" => "zarf",
        // Grubun açıklaması.
        "aciklama" => "Telefon, e-posta, adres ve saat hem İletişim sayfasındaki kartlarda hem her sayfanın en altında görünür.",
        // Sayfada gör.
        "adres" => "iletisim.php",
        // Yazılar.
        "yazilar" => array(
            // Kapaktaki küçük hap.
            "iletisim_rozet" => array("Kapaktaki küçük yazı", "İletişim > en üstteki fotoğrafta başlığın üstündeki küçük kutu", "Size yardım etmekten mutluluk duyarız", "kisa"),
            // Kapak açıklaması.
            "iletisim_kapak_aciklama" => array("Kapak açıklaması", "İletişim > en üstteki 'İletişim' başlığının altındaki yazı", "Sorunuz, öneriniz ya da yaşadığınız bir sorun mu var? Aşağıdaki formdan yazın veya bizi arayın, ekibimiz size dönüş yapsın.", "uzun"),
            // Telefon.
            "telefon" => array("Telefon", "İletişim > Telefon kartı ve her sayfanın en altı. Tıklayınca telefonda arama açılır", "0500 000 00 00", "kisa"),
            // E-posta.
            "eposta" => array("E-posta", "İletişim > E-posta kartı ve her sayfanın en altı", "info@tamirbul.com", "kisa"),
            // Adres.
            "adres" => array("Adres", "İletişim > Adres kartı ve her sayfanın en altı", "Örnek Mahallesi, Okul Sokak No: 1", "kisa"),
            // Çalışma saatleri.
            "calisma_saatleri" => array("Çalışma saatleri", "İletişim > Çalışma saatleri kartı ve her sayfanın en altı", "Hafta içi 09:00 - 18:00", "kisa")
        )
    ),

    // 9) Talep formu.
    "talep" => array(
        // Grubun başlığı.
        "baslik" => "Talep formu",
        // Grubun ikonu.
        "ikon" => "liste",
        // Grubun açıklaması.
        "aciklama" => "Müşterinin arızasını yazdığı form.",
        // Sayfada gör.
        "adres" => "talep-olustur.php",
        // Yazılar.
        "yazilar" => array(
            // Formun üstündeki yazı.
            "talep_formu_aciklama" => array("Formun üstündeki açıklama", "Talep Oluştur > sayfa başlığının altındaki yazı", "Cihaz bilgilerinizi ne kadar detaylı girerseniz, tamircilerden o kadar doğru fiyat teklifi alırsınız.", "uzun")
        )
    ),

    // 10) Sayfanın en altı.
    "altlik" => array(
        // Grubun başlığı.
        "baslik" => "Sayfanın en altı",
        // Grubun ikonu.
        "ikon" => "katman",
        // Grubun açıklaması.
        "aciklama" => "Her sayfanın en altındaki koyu şerit.",
        // Sayfada gör: sayfanın sonuna gider.
        "adres" => "index.php#altlik",
        // Yazılar.
        "yazilar" => array(
            // Logonun altındaki yazı.
            "altlik_aciklama" => array("Logonun altındaki açıklama", "Her sayfanın en altında, sol sütunda logonun altındaki yazı", "Arızalı cihazı olan müşteri ile bölgesindeki onaylı tamirciyi buluşturur.", "uzun"),
            // En alttaki küçük not.
            "altlik_not" => array("En alttaki küçük not", "Her sayfanın en en altındaki ince satır", "Bilgisayar programcılığı dönem projesi. İçerikler yönetim panelinden değiştirilir.", "uzun")
        )
    )
);

// HARİTA: Site yazısını okuma (yazi_getir)
// Listedeki bir yazıyı veritabanından okur. Veritabanında yoksa listedeki ilk değeri verir.
// Örnek: yazi_getir("anasayfa_rozet") -> "Hızlı teklif, güvenilir tamirci"
function yazi_getir($anahtar) {
    // Fonksiyonun dışındaki listeyi içeri alırız.
    global $site_yazilari_gruplari;
    // Listede bulamazsak boş yazı kalır.
    $ilk_deger = "";
    // Her grup için:
    foreach ($site_yazilari_gruplari as $grup) {
        // Yazı bu gruptaysa:
        if (isset($grup["yazilar"][$anahtar])) {
            // İlk değer dizinin 3. elemanıdır (sıra 2).
            $ilk_deger = $grup["yazilar"][$anahtar][2];
        }
    }
    // Veritabanındaki yazıyı okuruz. Yoksa ilk değer gelir.
    return ayar_getir($anahtar, $ilk_deger);
}

// HARİTA: Eksik site yazılarını veritabanına ekleme
// Listede olup site_ayarlari tablosunda olmayan yazıları ilk değeriyle ekler.
// admin/ayarlar.php açılınca çalışır. Böylece eski veritabanında da bütün yazılar yönetimde görünür.
function site_yazilarini_hazirla() {
    // Fonksiyonun dışındaki listeyi içeri alırız.
    global $site_yazilari_gruplari;
    // Her grup için:
    foreach ($site_yazilari_gruplari as $grup) {
        // Gruptaki her yazı için:
        foreach ($grup["yazilar"] as $anahtar => $bilgi) {
            // Bu yazının satırı var mı? Kaç satır olduğunu sayarız.
            $var_mi = sayi_getir("SELECT COUNT(*) FROM site_ayarlari WHERE anahtar = '" . temizle($anahtar) . "'");
            // Yoksa:
            if ($var_mi == 0) {
                // İlk değeriyle ekleriz. etiket: yönetimdeki kutunun adı.
                kaydet("site_ayarlari", array("anahtar" => $anahtar, "deger" => $bilgi[2], "etiket" => $bilgi[0]));
            }
        }
    }
}

// Bir yazı listede var mı? Varsa bilgisini (etiket, yer, ilk değer, tür), yoksa boş dizi verir.
// admin/ayarlar.php kaydederken listede olmayan anahtarı kabul etmemek için kullanır.
function site_yazisi_bilgisi($anahtar) {
    // Fonksiyonun dışındaki listeyi içeri alırız.
    global $site_yazilari_gruplari;
    // Her grup için:
    foreach ($site_yazilari_gruplari as $grup) {
        // Yazı bu gruptaysa:
        if (isset($grup["yazilar"][$anahtar])) {
            // Bilgisini veririz.
            return $grup["yazilar"][$anahtar];
        }
    }
    // Listede yok.
    return array();
}
