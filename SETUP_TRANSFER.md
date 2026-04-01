# Global CMS - Sunucu Taşıma Kılavuzu

## Taşınacak Dosyalar

Tüm proje klasörünü yeni sunucuya kopyalayın (FTP, cPanel Dosya Yöneticisi vb.)

## Kurulum Adımları

### 1. SQL Import
- Yeni sunucuda phpMyAdmin açın
- Sol menüden **Yeni Veritabanı** oluşturun
- Veritabanını seçin → **Import** sekmesi
- `database/export.sql` dosyasını yükleyin

### 2. Yapılandırma
- Tarayıcıda `site-adresi.com/setup.php` dosyasını açın
- Ekrandaki adımları takip edin:
  1. Veritabanı bilgilerini girin
  2. Bağlantıyı test edin
  3. Önbellek temizleyin
  4. Kurulumu tamamlayın

### 3. Hepsi Bu Kadar!
Artık siteyi kullanabilirsiniz.

---

## Manuel Alternatif (.env güncelleme)

setup.php yerine .env dosyasını elle düzenlemek isterseniz:

```env
APP_ENV=production
APP_URL=https://siteniz.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=veritabani_adi
DB_USERNAME=kullanici_adi
DB_PASSWORD=sifreniz
```

Sonra `bootstrap/cache` klasörünü boşaltın.
