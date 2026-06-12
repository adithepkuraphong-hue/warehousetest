# ระบบจัดเก็บข้อมูลคลังสินค้า (Inventory Management System)

## 📋 คำอธิบาย
ระบบนี้เป็นเว็บแอปพลิเคชัน PHP ที่มีความสามารถ CRUD (Create, Read, Update, Delete) สำหรับจัดเก็บและบริหารข้อมูลคลังสินค้า

## 🏗️ โครงสร้างโครงการ
```
testapi/
├── config.php              # ไฟล์การเชื่อมต่อฐานข้อมูล
├── database.sql            # SQL script สำหรับสร้างฐานข้อมูล
├── README.md               # ไฟล์นี้
├── api/
│   └── inventory.php       # API endpoints สำหรับ CRUD
└── front/
    └── index.php           # Frontend user interface
```

## 🔧 ขั้นตอนการติดตั้ง

### 1. รันไฟล์ SQL เพื่อสร้างฐานข้อมูล
- เปิด phpMyAdmin (http://localhost/phpmyadmin)
- สร้างฐานข้อมูลหรือรันไฟล์ `database.sql`:
  - คลิก "Import"
  - เลือกไฟล์ `database.sql`
  - คลิก "Go"

### 2. แก้ไขการเชื่อมต่อฐานข้อมูล (ถ้าจำเป็น)
- เปิดไฟล์ `config.php`
- แก้ไขค่าต่อไปนี้ตามการตั้งค่าของคุณ:
  ```php
  define('DB_HOST', 'localhost');    // Hostname
  define('DB_USER', 'root');         // Username
  define('DB_PASSWORD', '');         // Password
  define('DB_NAME', 'inventory_db'); // Database name
  ```

### 3. เข้าถึงเว็บแอปพลิเคชัน
- URL: `http://localhost/testapi/front/`

### 4. อัปเดตฐานข้อมูลสำหรับเวอร์ชัน Live Update
- รันไฟล์ migration: `migrations/2026_06_12_live_inventory_updates.sql`
- Migration นี้เปลี่ยนสถานะสินค้าเป็น `In Stock` / `Out Stock` และล็อกชื่อสินค้าให้อยู่ใน Master Data: `Paper`, `Wood`, `Plastic`
- รันไฟล์ migration เพิ่มเติมสำหรับพิกัดคลัง FG: `migrations/2026_06_12_fgwarehouse_location.sql`
- หน้า Layout สำหรับคลังสินค้าสำเร็จรูปอยู่ที่ `http://localhost/testapi/front/fg_warehouse_layout.php`

### 5. เปิด WebSocket Server
ระบบ Live Update ใช้ Node.js แบบไม่ต้องติดตั้ง dependency เพิ่ม:
```bash
npm run ws
```
ค่าเริ่มต้นจะเปิดที่ `ws://127.0.0.1:8090/ws`

## 📊 ฟีเจอร์

### ฐานข้อมูล (Database)
ตารางเก็บข้อมูลต่อไปนี้:
- **id** - รหัสประจำตัว (Auto Increment, Primary Key)
- **product_id** - ID สินค้า (Unique, String)
- **product_name** - ชื่อสินค้า (String)
- **quantity** - ปริมาณ (Integer, Default: 0)
- **status** - สถานะ (Enum: 'Keep' / 'Empty')
- **warehouse** - โรงเก็บสินค้า (A-B)
- **row_location** - โซน (A-C)
- **column_location** - แถว (1-5)
- **level** - ชั้น (0-3)
- **location_id** - ID ของที่อยู่ (เช่น AA-1-0) - auto-generate
- **created_at** - วันที่สร้าง (Timestamp)
- **updated_at** - วันที่อัปเดต (Timestamp)

### ความสามารถ CRUD

#### 1. **CREATE (เพิ่มสินค้า)**
- ส่ง POST request ไป `/api/inventory.php`
- ข้อมูลที่ต้องการ:
  ```json
  {
    "product_id": "P001",
    "product_name": "สินค้า A",
    "quantity": 100,
    "status": "Keep"
  }
  ```

#### 2. **READ (ดูข้อมูล)**
- ดูทั้งหมด: GET request ไป `/api/inventory.php`
- ดูรายการเดียว: GET request ไป `/api/inventory.php?id=1`

#### 3. **UPDATE (แก้ไขสินค้า)**
- ส่ง PUT request ไป `/api/inventory.php`
- ข้อมูลที่ต้องการ:
  ```json
  {
    "id": 1,
    "product_name": "สินค้า A (แก้ไข)",
    "quantity": 150,
    "status": "Keep"
  }
  ```

#### 4. **DELETE (ลบสินค้า)**
- ส่ง DELETE request ไป `/api/inventory.php`
- ข้อมูลที่ต้องการ:
  ```json
  {
    "id": 1
  }
  ```

## 🎨 Frontend Features

### ฟอร์มเพิ่ม/แก้ไขสินค้า
- กรอก ID สินค้า (ต้องไม่ซ้ำ)
- กรอกชื่อสินค้า
- กรอกปริมาณ
- เลือกสถานะ (Keep / Empty)

### 📍 Location Information (ข้อมูลที่อยู่)
- **Warehouse** - เลือกระหว่าง A หรือ B
- **Zone** - เลือกระหว่าง A, B, หรือ C
- **Row** - เลือกระหว่าง 1-5
- **Level** - เลือกระหว่าง 0-3
- **Location ID** - auto-generate เมื่อกรอกข้อมูล (เช่น AA-1-0)
  - รูปแบบ: `{Warehouse}{Zone}-{Row}-{Level}`
  - ตัวอย่าง: AB-3-2, BA-1-0, BC-5-3 เป็นต้น

### Warehouse Layout
- หน้า `/front/warehouse_layout.php` แสดงภาพรวมโกดัง A และ B
- แต่ละโกดังแบ่งเป็น Zone A-C และ Row 1-5
- คลิกตำแหน่งเพื่อดูรายการสินค้าตาม Level 0-3

### ตารางแสดงข้อมูล
- แสดงรายการสินค้าทั้งหมด
- ปุ่มแก้ไข - กรอกฟอร์มด้วยข้อมูลเดิม
- ปุ่มลบ - ยืนยันการลบก่อน
- แสดงวันที่สร้าง
- **หมายเหตุ**: วันที่อัปเดต (updated_at) เก็บไว้ในฐานข้อมูลเท่านั้น

### สถิติ
- จำนวนสินค้าทั้งหมด
- จำนวนสินค้าที่เก็บไว้ (Keep)
- จำนวนสินค้าที่หมด (Empty)
- ปุ่มรีโหลดข้อมูล

## 🔌 API Endpoints

### Get All Items
```
GET /testapi/api/inventory.php
Response:
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "product_id": "P001",
      "product_name": "สินค้า A",
      "quantity": 100,
      "status": "Keep",
      "warehouse": "A",
      "row_location": "A",
      "column_location": 1,
      "level": 0,
      "location_id": "AA-1-0",
      "created_at": "2024-06-10 10:30:00",
      "updated_at": "2024-06-10 10:30:00"
    }
  ]
}
```

### Get Single Item
```
GET /testapi/api/inventory.php?id=1
Response:
{
  "status": "success",
  "data": {
    "id": 1,
    "product_id": "P001",
    "product_name": "สินค้า A",
    "quantity": 100,
    "status": "Keep",
    "warehouse": "A",
    "row_location": "A",
    "column_location": 1,
    "level": 0,
    "location_id": "AA-1-0",
    "created_at": "2024-06-10 10:30:00",
    "updated_at": "2024-06-10 10:30:00"
  }
}
```

### Create Item
```
POST /testapi/api/inventory.php
Content-Type: application/json

{
  "productId": "P001",
  "productName": "สินค้า A",
  "quantity": 100,
  "status": "Keep",
  "warehouseCode": "A",
  "rowLocation": "A",
  "columnLocation": 1,
  "level": 0
}

Response:
{
  "status": "success",
  "message": "Item created successfully",
  "id": 1
}
```

### Update Item
```
PUT /testapi/api/inventory.php
Content-Type: application/json

{
  "id": 1,
  "productName": "สินค้า A (Updated)",
  "quantity": 150,
  "warehouseCode": "B",
  "rowLocation": "B",
  "columnLocation": 2,
  "level": 1
}

Response:
{
  "status": "success",
  "message": "Item updated successfully"
}
```

### Delete Item
```
DELETE /testapi/api/inventory.php
Content-Type: application/json

{
  "id": 1
}

Response:
{
  "status": "success",
  "message": "Item deleted successfully"
}
```

## ⚠️ ข้อผิดพลาดและการแก้ไข

### Connection failed
- ตรวจสอบว่า MySQL server กำลังทำงาน
- ตรวจสอบชื่อผู้ใช้และรหัสผ่านใน `config.php`

### Database not found
- รันไฟล์ `database.sql` เพื่อสร้างฐานข้อมูล
- หรือสร้างฐานข้อมูลด้วยตนเอง และนำเข้าไฟล์ SQL

### Duplicate entry
- ID สินค้าไม่สามารถซ้ำได้
- ใช้ ID ที่ไม่ซ้ำกับ ID เดิม

## 🚀 การใช้งาน

### 1. เพิ่มสินค้า
1. กรอกฟอร์มเพิ่มสินค้า
2. คลิกปุ่ม "เพิ่มสินค้า"
3. ข้อมูลจะปรากฏในตาราง

### 2. แก้ไขสินค้า
1. คลิกปุ่ม "แก้ไข" บนแถวสินค้า
2. ฟอร์มจะเต็มไปด้วยข้อมูลเดิม
3. แก้ไขข้อมูลตามต้องการ
4. คลิกปุ่ม "อัปเดตสินค้า"

### 3. ลบสินค้า
1. คลิกปุ่ม "ลบ" บนแถวสินค้า
2. ยืนยันการลบในไดเอล็ก
3. สินค้าจะถูกลบออกจากฐานข้อมูล

### 4. ดูสถิติ
- ดูจำนวนสินค้าในส่วนสถิติ
- คลิก "รีโหลดข้อมูล" เพื่อรีเฟรช

## 📱 Cross-Origin (CORS)
API รองรับ CORS headers สำหรับการร้องขอจากโดเมนอื่น

## 💾 ประเภท Character Set
ฐานข้อมูลใช้ `utf8mb4` เพื่อรองรับข้อความภาษาไทยและ emoji

## 📝 หมายเหตุ
- ข้อมูล `created_at` และ `updated_at` เก็บอยู่ในฐานข้อมูลแต่จะแสดง `created_at` เท่านั้นในตาราง
- API จะตรวจสอบความถูกต้องของข้อมูลโดยอัตโนมัติ
- สถานะสินค้าต้องเป็น "Keep" หรือ "Empty" เท่านั้น

---
**สร้างเมื่อ**: 10 มิถุนายน 2569
**เวอร์ชัน**: 1.0
