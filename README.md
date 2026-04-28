# Meeting Room Booking API

A JSON API microservice for booking meeting rooms.

## Setup

```bash
# Clone and install
composer install

# Copy env
cp .env.example .env
php artisan key:generate

# Run with Docker
docker-compose up -d

# Migrate and seed
docker-compose exec app php artisan migrate --seed

# Run tests
docker-compose exec app php artisan test
# or
./vendor/bin/pest
```

## API Endpoints

### Create a booking

```
POST /api/bookings
Content-Type: application/json

{
    "user_id": 1,
    "room_id": 2,
    "starts_at": "2025-06-01T10:00:00+00:00",
    "ends_at":   "2025-06-01T11:00:00+00:00",
    "title":     "Team Sync"
}
```

**Response 201:**
```json
{
    "data": {
        "id": 1,
        "user_id": 1,
        "room": {
            "id": 2,
            "name": "Beta",
            "capacity": 10
        },
        "title": "Team Sync",
        "starts_at": "2025-06-01T10:00:00+00:00",
        "ends_at":   "2025-06-01T11:00:00+00:00",
        "created_at": "2025-05-28T09:00:00+00:00"
    }
}
```

**Response 409 (conflict):**
```json
{
    "error": "conflict",
    "message": "The room is already booked for the requested time slot."
}
```

---

### List bookings by user

```
GET /api/bookings/my?user_id=1
```

**Response 200:**
```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "room": { "id": 2, "name": "Beta", "capacity": 10 },
            "title": "Team Sync",
            "starts_at": "2025-06-01T10:00:00+00:00",
            "ends_at":   "2025-06-01T11:00:00+00:00",
            "created_at": "2025-05-28T09:00:00+00:00"
        }
    ]
}
```

---

### List bookings by room

```
GET /api/bookings/by-room?room_id=2
```

**Response 200:** same structure as above.

---

## Design Decisions

- **DB transaction in BookingService** — conflict check and insert run atomically to prevent double-booking under concurrent requests
- **Overlap detection** — three-condition query covers all overlap cases: starts inside, ends inside, and fully contains existing booking
- **DTO pattern** — `CreateBookingDTO` keeps controllers and services decoupled from request objects
- **Custom exception** — `RoomConflictException` maps to HTTP 409 cleanly without leaking internals
- **No auth** — `user_id` passed in request body/query as per spec
- **PHPStan level 8** — strict type safety across all app code

## Seeded Rooms

| ID | Name    | Capacity |
|----|---------|----------|
| 1  | Alpha   | 6        |
| 2  | Beta    | 10       |
| 3  | Gamma   | 4        |
| 4  | Delta   | 20       |
| 5  | Epsilon | 8        |
