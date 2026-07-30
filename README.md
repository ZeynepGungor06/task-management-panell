# 📝 Gelişmiş To-Do List ve Görev Yönetim Sistemi

Bu proje, görevlerinizi kolayca takip edebileceğiniz, istatistiklerinizi görüntüleyebileceğiniz ve yaklaşan görevler için otomatik hatırlatmalar alabileceğiniz çok katmanlı (full-stack) bir görev yönetim uygulamasıdır. 

Modern yazılım mimarileri kullanılarak geliştirilmiş olup, Docker üzerinde mikroservis mantığıyla çalışmaktadır.

## 🚀 Proje Özellikleri

*   **Görev Yönetimi (CRUD):** Görev ekleme, düzenleme, silme ve tamamlama işlemleri.
*   **İstatistik Paneli:** Tamamlanan, bekleyen ve süresi geçen görevlerin sayısal analizi ve görselleştirilmesi.
*   **Otomatik Hatırlatıcılar (Microservice):** Node.js ile yazılmış arka plan işçisi (worker) sayesinde, teslim tarihi yaklaşan görevler için kullanıcılara otomatik e-posta bildirimleri.
*   **Konteyner Mimarisi:** Uygulamanın her bir parçası (Backend, Veritabanı, Worker) izole Docker konteynerlerinde çalışır.

## 🛠️ Kullanılan Teknolojiler

*   **Backend:** PHP, Laravel
*   **Arka Plan İşçisi (Worker):** Node.js, JavaScript
*   **Veritabanı:** MySQL
*   **Frontend:** Blade, HTML/CSS, Bootstrap
*   **DevOps & Dağıtım:** Docker, Docker Compose

## ⚙️ Kurulum ve Çalıştırma

Projeyi yerel ortamınızda çalıştırmak için bilgisayarınızda **Docker** ve **Docker Desktop**'ın kurulu olması gerekmektedir.

1. Projeyi bilgisayarınıza klonlayın.
2. Terminali açın ve projenin ana dizinine gidin.
3. Aşağıdaki komutu çalıştırarak tüm konteynerleri inşa edip ayağa kaldırın:

\`\`\`bash
docker-compose up -d --build
\`\`\`

4. Konteynerler çalıştıktan sonra tarayıcınızdan şu adrese giderek uygulamayı kullanmaya başlayabilirsiniz:
**http://localhost:9000**

## 📂 Mimari Detaylar

Bu proje, ana web sunucusunu yormamak adına ayrıştırılmış bir mimari kullanır:
- `todo_backend`: Laravel uygulamasını barındırır ve web arayüzünü sunar.
- `todo_database`: Projenin MySQL veritabanını tutar.
- `ping_worker`: Node.js tabanlıdır. Belirli aralıklarla (cron job mantığıyla) Laravel API'sine istek atarak zamanlanmış görevleri ve e-posta kuyruklarını tetikler.

---
*Geliştirici: Zeynep Güngör*
