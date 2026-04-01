# Laravel 12x Flowtrack Logistics & Transportation Service Template - Installation Guide

Welcome to **Laravel 12x Flowtrack Template**!  
This comprehensive guide will walk you through multiple installation methods to get your project up and running on your local machine or production server.

---

## 🧰 System Requirements

Before installing, ensure your system meets these requirements:

- **PHP Version**: >= 8.2.0  
- **Composer Version**: >= 2.2  
- **Required PHP Extensions**:  
  - OpenSSL  
  - PDO  
  - Mbstring  
  - Tokenizer  
  - XML  
  - Ctype  
  - JSON  
  - BCMath  
  - cURL  
  - ZIP  

- **Database Support**: MySQL, PostgreSQL, or SQLite  
- **Recommended Local Servers**: XAMPP, WAMP, LARAGON, or LAMPP  

---

## Local Development Server (Optional – For Developers)

If you’d like to set up and preview the project on your **local computer**, follow these simple steps:

---

### **Step 1 – Install a Local Server (XAMPP / WAMP / Laragon)**
We recommend **XAMPP** for an easy setup.

**How to install XAMPP:**
1. Download it from 👉 [https://www.apachefriends.org/](https://www.apachefriends.org/)  
2. Install and ensure it includes **PHP 8.2 or higher**  
3. Start **Apache** and **MySQL** services from the XAMPP control panel  
4. Verify your PHP version by running:

```bash
php --version
```

If the version displayed is **8.2 or higher**, you’re good to go!

---

### **Step 2 – Install Composer (PHP Dependency Manager)**

Make sure **Composer** is installed on your system:

```bash
composer --version
```

If you see a version number, it’s already installed.  
If not, download and install Composer from 👉 [https://getcomposer.org/download](https://getcomposer.org/download)

---

### **Step 3 – Unzip Your Downloaded File**

- Extract the **Envato downloaded ZIP file** to your preferred local directory (for example: `htdocs/flowtrack/`)  
- Open your **Command Prompt (CMD)** or **Terminal**  
- Navigate to the project folder, for example:

```bash
cd C:\xampp\htdocs\flowtrack
```

---

### **Step 4 – Start the Development Server**

You can now proceed with the project setup using either of the following methods:

- **Method 1: Quick CLI Setup (Recommended for Developers)**  
- **Method 2: GUI Installation (Best for Beginners / Non-Technical Users)**  

Please continue reading below to choose your preferred setup method.

---

✅ **Tip:** The GUI installer provides a step-by-step installation via browser,  
while the CLI method is faster for developers who prefer command line setup.


## 🚀 Installation Methods

Choose one of the three installation methods below:

### Method 1: Quick CLI Setup (Recommended)

The fastest way to get started using our automated setup command:

1. Clone/Download the project and navigate to the project directory  
2. Run the automated setup:

```bash
php artisan setup
```

3. Follow the interactive prompts to configure:  
   - Application URL  
   - Database connection (SQLite recommended for local development)  
   - Admin user credentials  
   - Demo data import (optional)  

4. If local then start the development server:

```bash
php artisan serve
```

5. Access your application at the configured URL (default: [http://127.0.0.1:8000](http://127.0.0.1:8000))

The CLI setup automatically handles environment configuration, database setup, migrations, seeders, and creates your admin user account.

---

### Method 2: GUI Installation (User-Friendly)

For those who prefer a web-based installation interface:

1. Clone/Download the project and navigate to the project directory  
2. If local then start the development server:

```bash
php artisan serve
```

3. Open your browser and visit: [http://localhost:8000/install](http://localhost:8000/install)  
   Follow the installation wizard:

   - Step 1: System requirements check  
   - Step 2: File permissions verification  
   - Step 3: Environment configuration (database, app settings)  
   - Step 4: Admin user creation  
   - Step 5: Final setup and completion  

The GUI installer provides a step-by-step visual interface with validation and error handling.

---

## 🎯 Post-Installation

After successful installation:

### Default Admin Credentials
- **Email**: As configured during setup  
- **Password**: As set during installation  
- **Role**: Admin  

### Important Next Steps
- Change default passwords for security  
- Update application settings in admin panel  
- Customize demo content with your own data  
- Configure email settings in `.env` for notifications  
- Set up proper permissions for production deployment  

### Recommended Development Tools
- Visual Studio Code  
- PhpStorm  
- Laravel Telescope (for debugging, already integrated)  

---

## 🛠 Troubleshooting

### Common Issues
- **Permission Errors**  

If needed, Laravel caching issues:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 📄 License

This project is provided under the **MIT License**.  
Please refer to `license.txt` or `license.pdf` for full terms and conditions.

---

## 🆘 Support

If you encounter any issues during installation:

- Check the troubleshooting section above  
- Verify all system requirements are met  
- Ensure proper file permissions are set  
- Review Laravel documentation for additional help  

---

**Happy Coding! 🚀**
