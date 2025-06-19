# Laravel Assessment - Mbnty_client1

---

## Solutions

### Problem 1 — the detailed solution can be found in [docs/problem-1-solution.md](docs/problem-1-solution.md)
### Database schema can be found in [docs/database-schema.md](docs/database-schema.md)

---

## Tech Stack Context

* **MySQL version:** 8.0.39
* **Laravel version:** 11.44.1
* **PHP version:** 8.3
* **Redis:** Available for caching

---

## Problem 1

### Overview

We’d like to understand how you’d approach a real-world problem our team is currently working on. We’re not expecting
working code, but rather a high-level outline of your thinking around architecture, data relationships, performance
optimisation, and any relevant constraints.

This exercise helps us see how you tackle challenges similar to those you’d encounter in this role.

### Scenario

We operate a booking engine platform that continuously polls and collects property information, rate prices, and rate
restrictions from an Oracle-based system via several APIs. This data is collected for the twelve months ahead and stored
in Laravel using a series of local storage tables. These include:

* **oracle\_hotels** – Contains hotel names and codes.
* **oracle\_roomtypes** – Contains room names and type codes. Room types are linked to hotels using a pivot table.
* **oracle\_rates** – Contains rate codes and names. Rates are hotel- and room-specific, so pivot relationships are
  required.
* **oracle\_room\_counts** – Stores room availability by date, per room. Each record includes a room code, hotel code,
  number of rooms available, the date of the availability.
* **oracle\_rate\_prices** – Stores rate prices per date, linked to rate code, hotel code, and room code via pivot
  tables.
* **oracle\_restrictions** – Contains information about room or rate closures on particular dates. Relationships are
  formed via pivot tables.
* **oracle\_promo\_codes** – Stores valid promo codes with their associated rate codes and validity date ranges.

There are several other pivot tables that manage the relationships between these entities.
In addition to the Oracle data, we also have a **custom management** layer within the booking engine that allows
overrides.
These include:

* **Custom room restrictions** – Defines occupancy limits, such as whether children are allowed, maximum guests per
  room, and any additional closer rules i.e when the room is closed on specific dates.
* **Price modifiers** – Applied per rate and per hotel, modifying prices based on per guest or per room
  additions/reductions. This applies to all dates.

### Your Task

We want to build a **room availability** search endpoint using this dataset. Laravel will serve this via a first-party
API to a customer-facing frontend (built on a different platform).

The Laravel API endpoint might look like this:

```http
GET /api/hotel/availability
```

**Expected request parameters:**

* Number of guests
* Ages of guests
* Booking start and end dates
* Number of rooms required
* Hotel code
* Promo code (optional)

### Expectations

Please explain your approach to the following:

1. **Schema design**

    * How would you structure the local storage schemas and define the relationships between entities, particularly
      where many-to-many associations are required (e.g. via linking tables or equivalent relational structures)?

2. **Handling the availability search request**

    * How would you query the various tables to determine availability based on the parameters listed above?
    * Please ensure the booking dates align with the corresponding records in local storage, while considering room and
      rate restrictions, custom rules (such as occupancy limits and closures), promo code validity, and any applicable
      price modifiers.

3. **Performance optimization**

    * The data covers the next 12 calendar months and is detailed per individual day, per room type, per rate. Given the
      dataset size, how would you optimise your queries to prevent timeouts or poor performance?

4. **Result structure & traceability**

    * How would you progressively narrow down availability results (e.g.
      ``hotel → rooms → rates → availability → promo code``)?
    * How would you structure your code to allow visibility and debugging at each stage during development (ex: logging,
      intermediate data output)?

5. **Constraints**

    * We cannot make large infrastructure changes such as swapping out the database engine or drivers.
    * However, feel free to suggest improvements that remain within the Laravel application and local database scope.

6. **Optional enhancements**

    * If relevant, feel free to suggest additional features or considerations that could help us scale, maintain or
      extend this service efficiently (e.g. future-proofing the data layer, background processing, job queues, API
      throttling).

---

## Problem 2

### Overview

We would like you to provide a working code example for this task, hosted in an accessible Git repository.

This scenario builds on the booking engine platform described in Problem 1. In this case, we need to expose a
first-party API endpoint from Laravel for a customer-facing frontend to allow users to make bookings. These booking
requests will then be passed through to Oracle.

### Scenario

When a reservation is submitted from the frontend, the Laravel application needs to:

1. Receive the request and validate the parameters.
2. Check whether a valid Oracle access token is already stored. If not, request a new one.
3. Format and forward the reservation request to Oracle’s reservation API.
4. For each individual room in the booking request, a separate reservation request must be sent to Oracle. That is, if
   three rooms are being booked in the same session, Laravel must send three separate API calls to Oracle’s reservation
   endpoint - one per room.

> **Note:**
>
> * Oracle tokens expire after 1 hour.
> * The Laravel application must cache or persist the token and re-use it when possible.
> * If the token is expired or not found, it must request a new one from the Oracle token API.

### Your Task

Create a Laravel API endpoint to accept reservation requests from the frontend application.

Example endpoint:

```http
POST /api/hotel/booking
```

**Expected request payload:**

* ``arrivalDate`` and ``departureDate``
* ``hotelCode``
* ``promoCode`` (optional)
* `rooms`: array of room booking requests, where each item includes per individual room in the array:
    * ``adults``
    * ``children``
    * ``roomCode``
    * ``rateCode``
    * ``totalPrice``

**What Laravel needs to do:**

1. Validate and process the incoming request.
2. For each room in the rooms array, construct and send a separate reservation request to Oracle.

Example payload per room (sent individually):

```json
{
    "reservations": {
        "reservation": [
            {
                "sourceOfSale": {
                    "sourceType": "WEB",
                    "sourceCode": "{{hotelId}}"
                },
                "roomStay": {
                    "roomRates": [
                        {
                            "total": {
                                "price": "{{price}}"
                            },
                            "roomType": "{{code}}",
                            "sourceCode": "WEB",
                            "ratePlanCode": "{{code}}"
                        }
                    ],
                    "guestCounts": {
                        "adult": "{{count}}",
                        "child": "{{count}}"
                    },
                    "arrivalDate": "{{date}}",
                    "departureDate": "{{date}}",
                    "promotion": {
                        "promotionCode": "{{promotionCode}}"
                    }
                },
                "hotelId": "{{hotelId}}"
            }
        ]
    }
}
```

**Oracle API endpoints:**

* **Reservation API:** ``POST /rsv/v1/hotels/{{HotelId}}/reservations``
    * Headers:
        * ``Authorization Bearer {{token}}``
        * ``Content-Type: application/json``
    * Body:
        * As in the example above.


* **Token API:** ``POST /oauth/v1/tokens``
    * Headers:
        * ``Authorization: Basic {{client_id}}:{{client_secret}}``
        * ``Content-Type: application/json``
    * Body:
        * ``username``
        * ``password``

### Expectations

Please demonstrate your approach to the following:

1. How would you structure the controller, service layers, and helper utilities?
2. How would you implement input validation and define required vs optional parameters?
3. How would you handle success and error responses with appropriate status codes?
4. How would you manage logging at different stages (e.g. request received, external API request/response)?
5. How would you handle external API failures, including retry logic or fallbacks, queues?
6. How would you persist and manage the Oracle token (e.g. in cache or database)?
7. Any improvements you’d suggest to ensure robustness, maintainability, and clarity.
