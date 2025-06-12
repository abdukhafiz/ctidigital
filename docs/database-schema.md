# Database Schema Design

## Overview

This file includes the database schema for the Oracle hotel booking system integration. The schema manages hotels, room types, rates, availability, restrictions, and promotional codes with their relationships.

## Main Entities

| Table                            | Main fields                                                                                                                | Description                                                                 |
|----------------------------------|----------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------|
| `oracle_hotels`                  | `id, hotel_code, name`                                                                                                     | Hotels from Oracle                                                          |
| `oracle_roomtypes`               | `id, room_code, name`                                                                                                      | Room types from Oracle                                                      |
| `oracle_hotel_roomtypes`         | `id, hotel_id, room_type_id`                                                                                               | Pivot table for oracle hotels and oracle room type tables                   |
| `oracle_rates`                   | `id, rate_code, name`                                                                                                      | Rates from Oracle                                                           |
| `oracle_hotel_rates`             | `id, hotel_id, rate_id`                                                                                                    | Pivot table for oracle hotels and oracle rate tables                        |
| `oracle_room_type_rates`         | `id, room_type_id, rate_id`                                                                                                | Pivot table for oracle room type and oracle rate tables                     |
| `oracle_room_counts`             | `id, hotel_id, room_type_id, availability_date, rooms_available`                                                           | Room availability from Oracle by date, per room                             |
| `oracle_rate_prices`             | `id, hotel_id, room_type_id, rate_id, price_date, price, currency`                                                         | Store price for each rate per day                                           |
| `oracle_restrictions`            | `id, restriction_code, name, restriction_type`                                                                             | Restrictions from Oracle                                                    |
| `oracle_hotel_room_restrictions` | `id, hotel_id, room_type_id, restriction_id, date`                                                                         | Hotel room restrictions                                                     |
| `oracle_promo_codes`             | `id, promo_code, name, discount_type, valid_from, valid_to`                                                                | Promos from Oracle                                                          |
| `oracle_promo_code_rates`        | `id, promo_code_id, rate_id, hotel_id`                                                                                     | Pivot table for oracle promo codes and oracle rates and oracle hotels table |
| `custom_room_restrictions`       | `id, hotel_id, room_type_id, max_adults, max_children, children_allowed, max_guests, closure_start_date, closure_end_date` | Custom restrictions table                                                   |
| `price_modifiers`                | `id, hotel_id, rate_id, date, modifier_type, operation, price`                                                             | Price modifiers                                                             |

## 1. Database schema sql (DDL)

```sql
CREATE TABLE oracle_hotels
(
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_code VARCHAR(200) UNIQUE NOT NULL,
    name       VARCHAR(255)        NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX      idx_hotel_code (hotel_code)
);

CREATE TABLE oracle_roomtypes
(
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_code  VARCHAR(50) UNIQUE NOT NULL,
    name       VARCHAR(255)       NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX      idx_room_type_code (room_code)
);

CREATE TABLE oracle_hotel_roomtypes
(
    hotel_id     BIGINT UNSIGNED,
    room_type_id BIGINT UNSIGNED,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL,
    PRIMARY KEY (hotel_id, roomtype_id),
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES oracle_roomtypes (id) ON DELETE CASCADE
);

CREATE TABLE oracle_rates
(
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rate_code  VARCHAR(50) UNIQUE NOT NULL,
    name       VARCHAR(255)       NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX      idx_rate_code (rate_code)
);

CREATE TABLE oracle_hotel_rates
(
    hotel_id   BIGINT UNSIGNED,
    rate_id    BIGINT UNSIGNED,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (hotel_id, rate_id),
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES oracle_rates (id) ON DELETE CASCADE
);

CREATE TABLE oracle_room_type_rates
(
    room_type_id BIGINT UNSIGNED,
    rate_id      BIGINT UNSIGNED,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL,
    PRIMARY KEY (room_type_id, rate_id),
    FOREIGN KEY (room_type_id) REFERENCES oracle_roomtypes (id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES oracle_rates (id) ON DELETE CASCADE
);

CREATE TABLE oracle_room_counts
(
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id          BIGINT UNSIGNED,
    room_type_id      BIGINT UNSIGNED,
    availability_date DATE NOT NULL,
    rooms_available   INT  NOT NULL,
    created_at        TIMESTAMP NULL,
    updated_at        TIMESTAMP NULL,
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES oracle_roomtypes (id) ON DELETE CASCADE,
    INDEX             idx_availability_date (availability_date),
    INDEX             idx_hotel_room_date (hotel_id, room_type_id, availability_date)
);

CREATE TABLE oracle_rate_prices
(
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id     BIGINT UNSIGNED,
    room_type_id BIGINT UNSIGNED,
    rate_id      BIGINT UNSIGNED,
    price_date   DATE           NOT NULL,
    price        DECIMAL(10, 2) NOT NULL,
    currency     VARCHAR(3)     NOT NULL,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL,
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES oracle_roomtypes (id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES oracle_rates (id) ON DELETE CASCADE,
    INDEX        idx_price_date (price_date),
    INDEX        idx_hotel_room_rate_date (hotel_id, room_type_id, rate_id, price_date)
);

CREATE TABLE oracle_restrictions
(
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restriction_code VARCHAR(200) UNIQUE NOT NULL,
    name             VARCHAR(255)        NOT NULL,
    restriction_type ENUM('room_closure', 'rate_closure')        NOT NULL,
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL,
    INDEX            idx_restriction_code (restriction_code),
    INDEX            idx_restriction_type (restriction_type),
);

CREATE TABLE oracle_hotel_room_restrictions
(
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id       BIGINT UNSIGNED,
    room_type_id   BIGINT UNSIGNED,
    restriction_id BIGINT UNSIGNED,
    date           DATE NOT NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL,
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES oracle_roomtypes (id) ON DELETE CASCADE,
    FOREIGN KEY (restriction_id) REFERENCES oracle_restrictions (id) ON DELETE CASCADE,
    INDEX          idx_date (date),
    INDEX          idx_hotel_room_restriction_date (hotel_id, room_type_id, restriction_id, date)
);

CREATE TABLE oracle_promo_codes
(
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    promo_code     VARCHAR(200) UNIQUE NOT NULL,
    name           VARCHAR(255)        NOT NULL,
    discount_type  ENUM('percentage', 'fixed')        NOT NULL,
    discount_value decimal(10, 2)      not null,
    valid_from     DATE                NOT NULL,
    valid_to       DATE                NOT NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL,
    INDEX          idx_promo_code (promo_code),
    INDEX          idx_discount_type (discount_type),
    INDEX          idx_valid_range (valid_from, valid_to),
);

CREATE TABLE oracle_promo_code_rates
(
    promo_code_id BIGINT UNSIGNED,
    rate_id       BIGINT UNSIGNED,
    hotel_id      BIGINT UNSIGNED,
    PRIMARY KEY (promo_code_id, rate_id, hotel_id),
    FOREIGN KEY (promo_code_id) REFERENCES oracle_promo_codes (id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES oracle_rates (id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE
);

CREATE TABLE custom_room_restrictions
(
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id           BIGINT UNSIGNED,
    room_type_id       BIGINT UNSIGNED,
    max_adults         INT NULL,
    max_children       INT NULL,
    children_allowed   BOOL DEFAULT TRUE,
    max_guests         INT NULL,
    closure_start_date DATE NULL,
    closure_end_date   DATE NULL,
    created_at         TIMESTAMP NULL,
    updated_at         TIMESTAMP NULL,
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES oracle_roomtypes (id) ON DELETE CASCADE,
    INDEX              idx_children_allowed (children_allowed),
    INDEX              idx_closure_date_range (closure_start_date, closure_end_date),
    INDEX              idx_hotel_room (hotel_id, room_type_id)
);

CREATE TABLE price_modifiers
(
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id      BIGINT UNSIGNED,
    rate_id       BIGINT UNSIGNED,
    date          DATE           NOT NULL,
    modifier_type ENUM('per_guest', 'per_room') NOT NULL,
    operation     ENUM('add', 'subtract') NOT NULL,
    price         DECIMAL(10, 2) NOT NULL,
    created_at    TIMESTAMP NULL,
    updated_at    TIMESTAMP NULL,
    FOREIGN KEY (hotel_id) REFERENCES oracle_hotels (id) ON DELETE CASCADE,
    FOREIGN KEY (rate_id) REFERENCES oracle_rates (id) ON DELETE CASCADE,
    INDEX         idx_date (date),
    INDEX         idx_modifier_type (modifier_type),
    INDEX         idx_operation (operation),
    INDEX         idx_hotel_rate_date (hotel_id, rate_id, date)
);

```
