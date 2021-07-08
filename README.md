# FlashBox/FlashBox-Api-PHP

[![License](https://poser.pugx.org/flashbox/flashbox-api-php/license)](https://packagist.org/packages/flashbox/flashbox-api-php)
[![Latest Stable Version](https://poser.pugx.org/flashbox/flashbox-api-php/v/stable)](https://packagist.org/packages/flashbox/flashbox-api-php)
[![Monthly Downloads](https://poser.pugx.org/flashbox/flashbox-api-php/d/monthly)](https://packagist.org/packages/flashbox/flashbox-api-php)

This package is built to facilitate application development for FlashBox RESTful API. For more information about this api, please visit [FlashBox Documents](https://developers.flashbox.co/)

## Installation
First of all, You need an [ACCESS-TOKEN](https://flashbox.co/contact?unit=sales). 
All FlashBox API endpoints support the JWT authentication protocol. To start sending authenticated HTTP requests you will need to use your JWT authorization token which is sent to you.
Then you can install this package by using [Composer](http://getcomposer.org), running this command:

```sh
composer require flashbox/flashbox-api-php
```
Link to Packagist: https://packagist.org/packages/flashbox/flashbox-api-php

## Usage

#### 0. Set Token & Endpoint

```PHP
use FlashBox\FlashBoxApiHandler;

FlashBoxApiHandler::setToken(**YOUR_TOKEN**);
FlashBoxApiHandler::setEndpoint();
```

#### 1. Authenticate

```PHP
use FlashBox\FlashBoxApiHandler;

$apiResponse = FlashBoxApiHandler::authenticate();
if ($apiResponse && $apiResponse->status == "success") {
    $user = $apiResponse->object->user;
    echo $user->firstname . " " . $user->lastname;
}
```

##### Sample Api Response
```JSON
{
  "status": "success",
  "object": {
    "user": {
      "id": 99,
      "phone": "09701234567",
      "firstname": "john",
      "lastname": "doe",
      "type": "CUSTOMER",
      "email": "john_doe@gmail.com",
      "email_verified": 0,
      "verify": 1,
      "found_us": "",
      "referral_code": null,
      "referred_by": null,
      "created_at": "2017-09-16T17:06:28+04:30",
      "updated_at": "2017-09-18T16:07:02+04:30",
      "deleted_at": null,
      "jwt_token": **YOUR_TOKEN**,
      "avatar": {
            "url": "/uploads/user/99/avatar.jpg?var=1505744313"
      },
      "last_online": null,
      "is_online": null,
      "banks": []
    }
  }
}
```


#### 2. Get Address

This endpoint retrieves place information by its latitude and longitude.

```PHP
use FlashBox\Model\Location;

$apiResponse = Location::getAddress("35.732595", "51.413379");
if ($apiResponse && $apiResponse->status == "success") {
    echo $apiResponse->object->province;
}
```

##### Sample Api Response
```JSON
{
  "status": "success",
  "message": "",
  "object": {
    "address": [
        "288 Bremner Blvd, Toronto, ON M5V 3L9, Canada"
    ],
    "region": "",
    "country": "canada",
    "city": "gta",
    "province": "old toronto"
  }
}
```


#### 3. Location Suggestions

This endpoint retrieves suggestions by search input.
The result will be an array of suggestions. Each one includes the region and the name of the retrieved place, and offers coordinates for that item.

```PHP
use FlashBox\Model\Location;

// $locationName = null;   // returns FlashBox Exception
// $locationName = '';     // returns FlashBox Exception
$locationName = "Bremner";
$apiResponse = Location::getSuggestions($locationName, "43.642691,-79.385852");
if ($apiResponse && $apiResponse->status == "success") {
    $locations = $apiResponse->object;
    echo "<ol>";
    foreach ($locations as $location) {
        echo "<li>";
        echo $location->region . ": " . $location->title;
        echo "</li>";
    }
    echo "</ol>";
}
```

##### Sample Api Response
```JSON

{
  "status": "success",
  "message": "autoComplete",
  "object": [
    {
      "lat": "43.6415306",
      "lng": "-79.3868642",
      "title": "Bremner Boulevard ,Toronto ,ON ,Canada"
    },
    {
      "lat": "43.9107096",
      "lng": "-78.9255206",
      "title": "Bremner Street ,Whitby ,ON ,Canada"
    },
    {
      "lat": "43.816452",
      "lng": "-79.1180552",
      "title": "Bremner Pool And Spa ,Kingston Road ,Pickering ,ON ,Canada"
    },
    {
      "lat": "43.640736",
      "lng": "-79.392715",
      "title": "Bremner Blvd at Spadina Ave ,Toronto ,ON ,Canada"
    },
    {
      "lat": "43.640602",
      "lng": "-79.392487",
      "title": "Bremner Blvd at Spadina Ave East Side ,Toronto ,ON ,Canada"
    }
  ]
}
```


#### 4. Get Price

Request a quote for an order with origin address and destination address.

```PHP
use FlashBox\Model\Address;
use FlashBox\Model\Order;

/*
 * Create Origin Address
 */
$origin = new Address('origin', '43.642691', '-79.385852');

/*
 * Create First Destination
 */
$firstDest = new Address('destination', '43.660949', '-79.371432');

/*
 * Create Second Destination
 */
$secondDest = new Address('destination', '43.685905', '-79.403533');

/*
 * Create New Order
 */
$order = new Order('motorbike', 3, $origin, [$firstDest, $secondDest]);
$order->setHasReturn(true);

$apiResponse = $order->getPrice();

if ($apiResponse && $apiResponse->status == "success") {
    $addresses = $apiResponse->object->addresses;

    $origin = $addresses[0];
    echo "ORIGIN: {$origin->city} ({$origin->lat} , {$origin->lng})";
    echo "<br/>";
    echo "Transport Type: " . $apiResponse->object->transport_type;
    echo "<hr/>";

    $destinations = array_shift($addresses);

    echo "<table border='1' cellspacing='0'>
            <thead>
                <tr style='background: #bddaf5'>
                    <th>#</th>
                    <th>City</th>
                    <th>Distance</th>
                    <th>Duration</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
            ";
    foreach ($addresses as $destination) {
        echo "<tr>
                <td>{$destination->priority}</td>
                <td>{$destination->city}</td>
                <td>{$destination->distance}</td>
                <td>{$destination->duration}</td>
                <td>{$destination->price}</td>
              </tr>";
    }
    echo "<tr style='background: #7ab2a5; text-align: center'>
            <td colspan='2'>Total</td>
            <td>{$apiResponse->object->distance}(meters)</td>
            <td>{$apiResponse->object->duration}(seconds)</td>
            <td>{$apiResponse->object->price}(toman)</td>
          </tr>";
}

```

##### Sample Api Response
```JSON
{
  "status": "success",
  "message": null,
  "object": {
    "addresses": [
      {
        "type": "origin",
        "lat": "43.642691",
        "lng": "-79.385852",
        "description": null,
        "unit": null,
        "number": null,
        "person_fullname": null,
        "person_phone": null,
        "address": "288 Bremner Blvd, Toronto, ON M5V 3L9, Canada",
        "city": "gta",
        "priority": 0
      },
      {
        "type": "destination",
        "lat": "43.660949",
        "lng": "-79.371432",
        "description": null,
        "unit": null,
        "number": null,
        "person_fullname": null,
        "person_phone": null,
        "address": "203 Gerrard St E, Toronto, ON M5A 2E7, Canada",
        "city": "gta",
        "priority": 1,
        "distance": 3541,
        "duration": 768,
        "coefficient": 0.6458176983879247,
        "price": 2
      },
      {
        "type": "destination",
        "lat": "43.685905",
        "lng": "-79.403533",
        "description": null,
        "unit": null,
        "number": null,
        "person_fullname": null,
        "person_phone": null,
        "address": "273 Poplar Plains Rd, Toronto, ON M4V 2N9, Canada",
        "city": "gta",
        "priority": 2,
        "distance": 4811,
        "duration": 815,
        "coefficient": 0.568113300769364,
        "price": 2
      },
      {
        "type": "return",
        "lat": "43.642691",
        "lng": "-79.385852",
        "description": null,
        "unit": null,
        "number": null,
        "person_fullname": null,
        "person_phone": null,
        "address": "288 Bremner Blvd, Toronto, ON M5V 3L9, Canada",
        "city": "gta",
        "priority": 3,
        "distance": 5686,
        "duration": 902,
        "coefficient": 0.5326247656357299,
        "price": 2
      }
    ],
    "price": 11.95,
    "payment_type": "3",
    "credit": false,
    "distance": 14038,
    "duration": 2485,
    "status": "OK",
    "user_credit": 0,
    "delay": 0,
    "city": "gta",
    "transport_type": "motorbike",
    "has_return": true,
    "cashed": false,
    "price_with_return": null,
    "score": 6,
    "score_detail": {
      "Order Completion": 5.975
    },
    "final_price": 11.95,
    "discount": 0,
    "discount_coupons": [],
    "invalid_discount_coupons": [],
    "failed_final_price": 0,
    "failed_discount": 0,
    "failed_discount_coupons": [],
    "scheduled": false,
    "attributes": []
  }
}
```


#### 5. Create Order

Once you calculated your the price of your order, you can use this endpoint in order to create a new order.

```PHP
use FlashBox\Model\Address;
use FlashBox\Model\Order;

/*
 * Create Origin: Behjat Abad
 */
$origin = new Address('origin', '43.642691', '-79.385852');
$origin->setDescription("Behjat Abad");                                            // optional                            
$origin->setUnit("44");                                                            // optional
$origin->setNumber("1");                                                           // optional
$origin->setPersonFullname("Leonardo DiCaprio");                                   // optional
$origin->setPersonPhone("09370000000");                                            // optional

/*
 * Create Second Destination: Ahmad Qasir Bokharest St
 */
$secondDest = new Address('destination', '35.895452', '51.589632');
$secondDest->setDescription("Ahmad Qasir Bokharest St");                            // optional
$secondDest->setUnit("66");                                                         // optional
$secondDest->setNumber("3");                                                        // optional
$secondDest->setPersonFullname("Matt Damon");                                       // optional
$secondDest->setPersonPhone("09390000000");                                         // optional

$order = new Order('motorbike', 3, $origin, [$firstDest, $secondDest]);
$order->setHasReturn(true);

$apiResponse = $order->create($order);

var_dump($apiResponse);
```

#### 6. Get Order Detail

In order to get the order details, call this method.

```PHP
use FlashBox\Model\Order;

// $orderID = "   309 ";
// $orderID = "   309<p>";
// $orderID = '';
// $orderID = null;
$orderID = 309;
$apiResponse = Order::getDetails($orderID);

var_dump($apiResponse);

```

#### 7. Cancel Order

You can cancel any order before courier arrival (before the accepted status)

```PHP
use FlashBox\Model\Order;

// $orderID = "   300 ";     // works fine as 300
// $orderID = "   300<p>";   // works fine as 300
// $orderID = '';            // throws FlashBoxException
// $orderID = null;          // throws FlashBoxException
$orderID = 300;
$apiResponse = Order::cancel($orderID);

var_dump($apiResponse);
```

#### 8. Get Batch Price
This endpoint is the same as Normal Price But the difference is you can calculate up to 15 pairs of Normal Price in one request.

```PHP
use FlashBox\Model\Address;
use FlashBox\Model\Order;

/*
 * Create Origin Address
 */
$origin = new Address('origin', '43.642691', '-79.385852');

/*
 * Create First Destination
 */
$firstDest = new Address('destination', '43.660949', '-79.371432');

/*
 * Create Second Destination
 */
$secondDest = new Address('destination', '43.685905', '-79.403533');

/*
 * Create New Order
 */
$orders[] = new Order('motorbike', 3, $origin, [$firstDest, $secondDest]);
$orders[] = new Order('car', 3, $origin, [$firstDest, $secondDest]);
$orders[] = new Order('cargo_s', 3, $origin, [$firstDest, $secondDest]);
$orders[] = new Order('cargo', 3, $origin, [$firstDest, $secondDest]);

$apiResponse = FlashBoxApiHandler::getBatchPrice($orders);
```


## License

This package is released under the __MIT license__.

Copyright (c) 2019-2021 Markus Poerschke

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
