# Limit-Order Exchange Mini Engine

This project is a simplified limit-order exchange engine built as part of a technical assessment. It demonstrates safe balance handling, atomic order matching, commission calculation, and real-time updates using Laravel and Vue.js.

---

##  Tech Stack

- **Backend:** Laravel 12.x (latest stable)
- **Frontend:** Vue 3 (Composition API) + Pinia
- **Database:** MySQL
- **Auth:** Laravel Sanctum
- **Real-time:** Pusher + Laravel Broadcasting
- **Styling:** Tailwind CSS

---

## Features

- User authentication
- USD wallet + crypto asset balances
- Place BUY / SELL limit orders
- Full-match-only order matching
- 1.5% commission applied on executed trades
- Atomic transactions with row-level locking
- Cancel open orders (refund / unlock funds)
- Real-time balance & order updates via Pusher

---

## Core Business Rules

### BUY Order
- Requires sufficient USD balance
- USD is deducted immediately
- Order stored as OPEN

### SELL Order
- Requires sufficient asset amount
- Asset is moved to `locked_amount`
- Order stored as OPEN

### Matching Rules
- BUY matches SELL where `sell.price <= buy.price`
- SELL matches BUY where `buy.price >= sell.price`
- **Full match only** (no partial fills)

### Commission
- 1.5% of matched USD volume
- Fee deducted from seller proceeds

---

## Setup Instructions

### 1. Clone Repository

```bash
git clone https://github.com/AqibUllah/limit-order-exchange.git
cd limit-order-exchange
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env 
 OR
copy .env.example .env
```

### 4. key generating
```
php artisan key:generate
```

Configure your database and Pusher credentials in `.env` file.

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Install Frontend Dependencies with below commands

```bash
npm install
npm run dev
```

### 7. Starting Server to see output

```bash
php artisan serve
 or
use Laravel Herd
```

---

## Demo Data (Recommended)

After registering a user, assign demo balances using tinker:

```bash
php artisan tinker
```

```php
$user = App\\Models\\User::first();
$user->balance = 50000;
$user->save();

App\\Models\\Asset::firstOrCreate([
  'user_id' => $user->id,
  'symbol' => 'BTC'
], ['amount' => 1, 'locked_amount' => 0]);
```

---

## Real-Time Events

On every successful order match:

- `OrderMatched` event is broadcast
- Delivered via `private-user.{id}` channel
- Frontend listens via Pinia store
- Wallet and order list auto-refresh

---

## API Endpoints

| Method | Endpoint | Description |
|------|---------|-------------|
| GET | /api/profile | User balance & assets |
| GET | /api/orders?symbol=BTC | User orders |
| POST | /api/orders | Place limit order |
| POST | /api/orders/{id}/cancel | Cancel open order |

---

## Design Decisions

- Matching logic isolated in a service class
- Transactions + `lockForUpdate()` used for race safety
- Minimal real-time payloads; frontend refetches state
- Pinia stores manage global state & listeners

---

## Status

All mandatory requirements are implemented. And i can add optional trade history storage if required.

---

## Author

**Aqib Ullah**  
Laravel & Filament + Vue Developer (5+ years)

---

## Notes

This project is intentionally scoped for assessment purposes and focuses on correctness, clarity, and reliability over feature completeness.
