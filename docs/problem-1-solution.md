# Problem 1 solution: Room availability search API

## Overview

This file includes the approach for building a room availability search endpoint for a booking engine platform that
integrates with Oracle hospitality system. The solution covers schema design, query optimization, performance
considerations and result structuring.

## 1. Database schema design

[Database schema design is provided in the file ``docs/database-schema.md``](database-schema.md)

## 2. Handling the availability search request

The availability search follows a progressive filtering approach:

```
Request → Hotel Lookup → Room Filtering → Restriction Checks → Rate Filtering → Price Calculation → Promo Application → Response
```

I will separate queries and in some cases use subqueries using eager loading to prevent n+1 problem. Below you can see
some examples:

### Step 1. Request validation and find hotel by code

Find hotel by hotel code:

```sql
SELECT id, hotel_code, name
FROM oracle_hotels
WHERE hotel_code = :hotel_code LIMIT 1;
```

**I would recommend caching hotels by hotel_code for at least 24 hours.**

### Step 2. Room availability filtering

Get list of available rooms

```sql
SELECT h.id                         as hotel_id,
       h.hotel_code,
       h.name                       as hotel_name,
       rt.id                        as room_type_id,
       rt.room_code,
       rt.name                      as room_name,
       MIN(orc.rooms_available)     as min_available_rooms,
       COUNT(orc.availability_date) as days_with_availability
FROM oracle_hotels h
         JOIN oracle_hotel_roomtypes ohr ON h.id = ohr.hotel_id
         JOIN oracle_roomtypes rt ON ohr.room_type_id = rt.id
         JOIN oracle_room_counts orc ON h.id = orc.hotel_id AND rt.id = orc.room_type_id
WHERE h.hotel_code = :hotel_code
  AND orc.availability_date BETWEEN :check_in_date AND DATE_SUB(:check_out_date, INTERVAL 1 DAY)
  AND orc.rooms_available >= :rooms
GROUP BY h.id, rt.id
HAVING min_available_rooms >= :rooms
   AND days_with_availability = DATEDIFF(:check_out_date, :check_in_date);
```

### Step 3: Apply custom room restrictions

Apply custom room restrictions for the hotel and room

```sql
SELECT crr.*
FROM custom_room_restrictions crr
WHERE crr.hotel_id = :hotel_id
  AND crr.room_type_id = :room_type_id;
```

### Step 4: Check restrictions from oracle

Get restrictions from oracle tables

```sql
SELECT ohrr.date,
       ores.restriction_type,
       ores.name as restriction_name
FROM oracle_hotel_room_restrictions ohrr
         JOIN oracle_restrictions ores ON ohrr.restriction_id = ores.id
WHERE ohrr.hotel_id = :hotel_id
  AND ohrr.room_type_id = :room_type_id
  AND ohrr.date BETWEEN :check_in_date AND DATE_SUB(:check_out_date, INTERVAL 1 DAY)
  AND ores.restriction_type IN ('room_closure', 'rate_closure');
```

### Step 5: Get available rates with prices

Get list of available rates with prices of the hotel

```sql
SELECT r.id      as rate_id,
       r.rate_code,
       r.name    as rate_name,
       orp.price_date,
       orp.price as base_price,
       orp.currency
FROM oracle_rates r
         JOIN oracle_hotel_rates ohr ON r.id = ohr.rate_id
         JOIN oracle_room_type_rates rtr ON r.id = rtr.rate_id
         JOIN oracle_rate_prices orp ON r.id = orp.rate_id
    AND orp.hotel_id = ohr.hotel_id
    AND orp.room_type_id = rtr.room_type_id
WHERE ohr.hotel_id = :hotel_id
  AND rtr.room_type_id = :room_type_id
  AND orp.price_date BETWEEN :check_in_date AND DATE_SUB(:check_out_date, INTERVAL 1 DAY)
ORDER BY r.rate_code, orp.price_date;
```

### Step 6: Apply price modifiers

Apply price modifiers for the hotel and rate for the date ranges

```sql
SELECT pm.rate_id,
       pm.date,
       pm.modifier_type,
       pm.operation,
       pm.price as modifier_amount
FROM price_modifiers pm
WHERE pm.hotel_id = :hotel_id
  AND pm.rate_id = :rate_id
  AND pm.date BETWEEN :check_in_date AND DATE_SUB(:check_out_date, INTERVAL 1 DAY)
ORDER BY pm.date;
```

### Step 7: Validate and apply promo codes

Validate promo code query

```sql
SELECT pc.id,
       pc.promo_code,
       pc.discount_type,
       pc.discount_value,
       pc.valid_from,
       pc.valid_to,
       pcr.rate_id,
       pcr.hotel_id
FROM oracle_promo_codes pc
         JOIN oracle_promo_code_rates pcr ON pc.id = pcr.promo_code_id
WHERE pc.promo_code = :promo_code
  AND :check_in_date BETWEEN pc.valid_from AND pc.valid_to
  AND pcr.hotel_id = :hotel_id
  AND pcr.rate_id = :rate_id;
```

## 3. Performance optimization

To prevent timeouts or poor performance, I'll use composite indexes for common query patterns, caching static data for a
long time, cache restriction data or some others for a short time, implement query result chunking for large datasets,
use pagination for the query results, etc.

## 4. Result structure and traceability

I would separate the whole availability process into small building blocks - ``hotel -> rooms -> rates -> availability
dates -> promo code``, which each handled by its own class. That way, each piece does one job only, which is "S" in
SOLID.

Visibility and debugging at every stage:

- Log data with Laravel’s Log facade (or Debugbar).
- Set breakpoints and trace through xDebug.
- Use quick checks with dd().
- I have limited experience with Laravel Telescope but ready to install and learn in a project.

## 5. Additional considerations and improvements

I would recommend to run search in the background (async) using queues and jobs. Once job is finishes, the result store
in the cache for 5 minutes and send notification to client using WebSocket.
