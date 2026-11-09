<?php
/*
SAYFA: Kayıt seçimi
DOSYA: kayit.php
KİM: Kişi 3

BU SAYFADA BUNLAR OLACAK:
- İki kart: Müşteri ol (musteri-kayit.php), Tamirci olarak katıl (tamirci-kayit.php)
*/

// Sekme başlığı.
$sayfa_basligi = "Kayıt ol";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";
// Üst menü.
include "ortak/baslik.php";
?>

<!-- Ortalanmış bölüm. -->
<section class="auth-section">
    <!-- Kapsayıcı. -->
    <div class="container auth-container">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Başlık. -->
            <h2>Hangi Hesabı Açmak İstiyorsunuz?</h2>
            <!-- Açıklama. -->
            <p>Arızalı cihazınız varsa müşteri, tamir yapıyorsanız tamirci hesabı açın.</p>
        </div>
        <!-- İki kart yan yana. -->
        <div class="secim-kartlari">
            <!-- Müşteri kartı, tamamı link. -->
            <a class="category-card-link" href="musteri-kayit.php">
                <!-- Kart. -->
                <div class="category-card">
                    <!-- Kartın ikonu. Üstüne gelince zıplar. -->
                    <div class="kart-ikonu ikon ikon-suzul">🙋</div>
                    <!-- Kart başlığı. -->
                    <h3>Müşteri Ol</h3>
                    <!-- Açıklama. -->
                    <p>Arızalı cihazın için talep aç, teklif topla ve en uygununu seç.</p>
                    <!-- Alt yazı. -->
                    <span class="card-action-text">Müşteri Kaydı →</span>
                </div>
            </a>
            <!-- Tamirci kartı, tamamı link. -->
            <a class="category-card-link" href="tamirci-kayit.php">
                <!-- Kart. -->
                <div class="category-card">
                    <!-- Kartın ikonu. -->
                    <div class="kart-ikonu ikon ikon-salla">🧰</div>
                    <!-- Kart başlığı. -->
                    <h3>Tamirci Olarak Katıl</h3>
                    <!-- Açıklama. -->
                    <p>İşletmeni kaydet, bölgendeki talepleri gör ve teklif ver.</p>
                    <!-- Alt yazı. -->
                    <span class="card-action-text">Tamirci Başvurusu →</span>
                </div>
            </a>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
