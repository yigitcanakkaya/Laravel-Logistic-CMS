<?php
/**
 * Global CMS - Setup Script
 * Tarayıcıdan çalıştırılarak kurulumu tamamlar
 *
 * Kullanım: Bu dosyayı tarayıcıda açın
 * Örnek: https://site.com/setup.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = dirname(__DIR__);
$envFile = $basePath.'/.env';
$storageApp = $basePath.'/storage/app';
$storagePublic = $basePath.'/storage/app/public';
$publicStorage = $basePath.'/public/storage';
$bootstrapCache = $basePath.'/bootstrap/cache';

$message = '';
$success = true;
$step = isset($_GET['step']) ? (int) $_GET['step'] : 0;

function getEnvValue($file, $key)
{
    if (! file_exists($file)) {
        return '';
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        if (trim($k) === $key) {
            return trim($v);
        }
    }

    return '';
}

function setEnvValue($file, $key, $value)
{
    if (! file_exists($file)) {
        return false;
    }
    $content = file_get_contents($file);
    if (preg_match("/^{$key}=.*/m", $content)) {
        $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
    } else {
        $content .= "\n{$key}={$value}";
    }

    return file_put_contents($file, $content);
}

function clearDir($dir)
{
    if (! is_dir($dir)) {
        return true;
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir.'/'.$file;
        is_dir($path) ? clearDir($path) : @unlink($path);
    }

    return true;
}

function deleteDir($dir)
{
    if (! is_dir($dir)) {
        return true;
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir.'/'.$file;
        is_dir($path) ? deleteDir($path) : @unlink($path);
    }

    return @rmdir($dir);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_env':
                $dbHost = $_POST['db_host'] ?? '';
                $dbPort = $_POST['db_port'] ?? '3306';
                $dbName = $_POST['db_name'] ?? '';
                $dbUser = $_POST['db_user'] ?? '';
                $dbPass = $_POST['db_pass'] ?? '';
                $appUrl = $_POST['app_url'] ?? '';

                if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
                    $message = 'Lütfen tüm zorunlu alanları doldurun.';
                    $success = false;
                    break;
                }

                setEnvValue($envFile, 'DB_HOST', $dbHost);
                setEnvValue($envFile, 'DB_PORT', $dbPort);
                setEnvValue($envFile, 'DB_DATABASE', $dbName);
                setEnvValue($envFile, 'DB_USERNAME', $dbUser);
                setEnvValue($envFile, 'DB_PASSWORD', $dbPass);
                setEnvValue($envFile, 'APP_URL', $appUrl);
                setEnvValue($envFile, 'APP_ENV', 'production');

                $message = '.env dosyası güncellendi!';
                $step = 1;
                break;

            case 'test_db':
                $host = getEnvValue($envFile, 'DB_HOST');
                $port = getEnvValue($envFile, 'DB_PORT');
                $dbname = getEnvValue($envFile, 'DB_DATABASE');
                $user = getEnvValue($envFile, 'DB_USERNAME');
                $pass = getEnvValue($envFile, 'DB_PASSWORD');

                try {
                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

                    $message = 'Veritabanı bağlantısı başarılı! '.count($tables).' tablo bulundu.';
                    $step = 2;
                } catch (PDOException $e) {
                    $message = 'Veritabanı hatası: '.$e->getMessage();
                    $success = false;
                }
                break;

            case 'clear_cache':
                clearDir($bootstrapCache);
                @mkdir($bootstrapCache, 0755, true);

                $message = 'Önbellek temizlendi!';
                $step = 3;
                break;

            case 'finalize':
                clearDir($bootstrapCache);
                @mkdir($bootstrapCache, 0755, true);

                $installedLock = $storageApp.'/installed.lock';
                if (! file_exists(dirname($installedLock))) {
                    @mkdir(dirname($installedLock), 0755, true);
                }
                if (! file_exists($installedLock)) {
                    file_put_contents($installedLock, date('Y-m-d H:i:s'));
                }

                $message = 'Kurulum tamamlandı! Ana sayfaya gidebilirsiniz.';
                $step = 4;

                @unlink(__FILE__);
                break;
        }
    }
}

$dbHost = getEnvValue($envFile, 'DB_HOST');
$dbPort = getEnvValue($envFile, 'DB_PORT');
$dbName = getEnvValue($envFile, 'DB_DATABASE');
$dbUser = getEnvValue($envFile, 'DB_USERNAME');
$appUrl = getEnvValue($envFile, 'APP_URL');
$isInstalled = file_exists($storageApp.'/installed.lock');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global CMS - Kurulum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 600px; width: 100%; padding: 40px; }
        h1 { color: #333; margin-bottom: 10px; font-size: 28px; }
        h2 { color: #666; font-size: 18px; font-weight: normal; margin-bottom: 30px; }
        .step { display: flex; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .step-num { width: 32px; height: 32px; border-radius: 50%; background: #007bff; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; }
        .step.active .step-num { background: #28a745; }
        .step.done .step-num { background: #6c757d; }
        .step.done .step-num::after { content: '✓'; }
        .step.done .step-num { font-size: 0; }
        .step-text { flex: 1; }
        .step-title { font-weight: 600; color: #333; }
        .step-desc { font-size: 13px; color: #666; margin-top: 2px; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        input:focus { outline: none; border-color: #007bff; }
        button { background: #007bff; color: white; border: none; padding: 14px 30px; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 500; }
        button:hover { background: #0056b3; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .info-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .info-box strong { color: #856404; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .auto-url { font-size: 12px; color: #666; margin-top: -10px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Global CMS</h1>
        <h2>Kurulum ve Yapılandırma</h2>
        
        <?php if ($message) { ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>
        
        <?php if ($isInstalled && $step < 4) { ?>
            <div class="alert alert-info">
                Sistem zaten kurulmuş görünüyor. Yapılandırmayı güncellemek için devam edin.
            </div>
        <?php } ?>
        
        <div class="step <?php echo $step >= 0 ? 'active' : ''; ?> <?php echo $step > 0 ? 'done' : ''; ?>">
            <div class="step-num">1</div>
            <div class="step-text">
                <div class="step-title">Veritabanı Ayarları</div>
                <div class="step-desc">Yeni sunucunun veritabanı bilgilerini girin</div>
            </div>
        </div>
        
        <?php if ($step === 0) { ?>
            <form method="POST">
                <input type="hidden" name="action" value="update_env">
                
                <div class="info-box">
                    <strong>Not:</strong> SQL dosyasını phpMyAdmin'den import ettikten sonra bu sayfayı açın.
                </div>
                
                <label>Site Adresi (APP_URL)</label>
                <input type="text" name="app_url" value="<?php echo htmlspecialchars($appUrl ?: 'https://'.($_SERVER['HTTP_HOST'] ?? '')); ?>" placeholder="https://www.example.com">
                <div class="auto-url">Otomatik algılandı, gerekiyorsa düzeltin</div>
                
                <label>Veritabanı Host *</label>
                <input type="text" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>" placeholder="localhost" required>
                
                <label>Veritabanı Port</label>
                <input type="text" name="db_port" value="<?php echo htmlspecialchars($dbPort ?: '3306'); ?>" placeholder="3306">
                
                <label>Veritabanı Adı *</label>
                <input type="text" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>" placeholder="veritabani_adi" required>
                
                <label>Veritabanı Kullanıcı *</label>
                <input type="text" name="db_user" value="<?php echo htmlspecialchars($dbUser); ?>" placeholder="kullanici_adi" required>
                
                <label>Veritabanı Şifre</label>
                <input type="password" name="db_pass" value="" placeholder="Şifreniz varsa girin">
                
                <button type="submit">Kaydet ve Devam Et</button>
            </form>
        <?php } ?>
        
        <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'done' : ''; ?>">
            <div class="step-num">2</div>
            <div class="step-text">
                <div class="step-title">Veritabanı Bağlantı Testi</div>
                <div class="step-desc">Veritabanına bağlanabildiğini doğrula</div>
            </div>
        </div>
        
        <?php if ($step === 1) { ?>
            <form method="POST">
                <input type="hidden" name="action" value="test_db">
                <button type="submit">Bağlantıyı Test Et</button>
            </form>
        <?php } ?>
        
        <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'done' : ''; ?>">
            <div class="step-num">3</div>
            <div class="step-text">
                <div class="step-title">Önbellek Temizleme</div>
                <div class="step-desc">Eski önbellek dosyalarını temizle</div>
            </div>
        </div>
        
        <?php if ($step === 2) { ?>
            <form method="POST">
                <input type="hidden" name="action" value="clear_cache">
                <button type="submit">Önbelleği Temizle</button>
            </form>
        <?php } ?>
        
        <div class="step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'done' : ''; ?>">
            <div class="step-num">4</div>
            <div class="step-text">
                <div class="step-title">Kurulumu Tamamla</div>
                <div class="step-desc">Son ayarları yap ve siteyi aktif et</div>
            </div>
        </div>
        
        <?php if ($step === 3) { ?>
            <form method="POST">
                <input type="hidden" name="action" value="finalize">
                <button type="submit">Kurulumu Tamamla</button>
            </form>
        <?php } ?>
        
        <?php if ($step === 4) { ?>
            <div class="alert alert-success">
                <strong>Tebrikler!</strong> Kurulum tamamlandı.<br><br>
                <a href="/" style="color: #155724;">Ana sayfaya git →</a>
            </div>
        <?php } ?>
    </div>
</body>
</html>
