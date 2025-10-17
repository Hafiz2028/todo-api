# Technical Test - Todo API (Talenavi)

RESTful API aplikasi Todo List menggunakan Laravel 12.

---

## ✨ Fitur Utama

* **Create Todo**: Endpoint membuat task baru dengan validasi.
* **Excel Report**: Endpoint mengunduh laporan semua task dalam format `.xlsx`, lengkap dengan ringkasan total dan fungsionalitas filter yang kompleks.
* **Chart Data**: Endpoint untuk menyediakan chart data dalam format JSON (berdasarkan status, prioritas, dan penanggung jawab).

---

## 🚀 Setup & Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://github.com/Hafiz2028/todo-api.git
    cd todo-api
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Setup Environment**
    Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
    ```bash
    cp .env.example .env
    ```

4.  **Generate App Key**
    ```bash
    php artisan key:generate
    ```

5.  **Jalankan Migrasi Database**
    ```bash
    php artisan migrate
    ```

6.  **Jalankan Server Lokal**
    ```bash
    php artisan serve
    ```
    Aplikasi akan berjalan di `http://127.0.0.1:8000`.

---

## 📮 Pengujian API (Postman)

Semua endpoint dapat diuji menggunakan Postman. Koleksi Postman yang berisi semua *request* yang diperlukan telah disiapkan untuk mempermudah pengujian.

### Endpoint Utama:

| Method | Endpoint                                 | Deskripsi                                        |
| :----- | :--------------------------------------- | :------------------------------------------------- |
| `POST` | `/api/todos`                             | Membuat task baru.                                 |
| `GET`  | `/api/todos/report/excel`                | Mengunduh laporan Excel (mendukung filter).        |
| `GET`  | `/api/todos/chart?type={tipe}`           | Mendapatkan data JSON untuk chart (`status`, `priority`, `assignee`). |
