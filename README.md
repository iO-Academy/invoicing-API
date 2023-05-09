# Invoice system RESTful API

**For trainers:** The API is deployed at https://invoicing-api.dev.io-academy.uk, use this when deploying front-end applications made by students.

<details>
<summary>Run the API locally</summary>

<p></p>
<p>
Clone this repo into your docker `html` folder:

```bash
git clone git@github.com:iO-Academy/invoicing-API.git
```

Once cloned, first install the database stored in `db/invoices.sql`.
Create a database named `invoices`, then open the SQL file in your MySQL GUI and run all queries.

After installing the database, install the vendor code by running the following from the root of the repo:

```bash
composer install
```

To run the application locally:
```bash
composer start
```

**Do not close this terminal tab, it is a running process.**

The API will now be accessible at `http://localhost:8080/`.

That's it! Now go build something cool.
</p>
</details>

## API documentation

### Return all invoices

* **URL**

  /invoices

* **Method:**

  `GET`

* **URL Params**

  **Required:**

  There are no required URL params, this URL will return all invoices if no params are passed

  **Optional:**

  `status=[numeric]` - only return invoices with the provided status.
  `sort=[invoice_id|invoice_total|created|due]` - sort the invoices by the supplied field, defaults to `invoice_id`.

  **Example:**

  `/invoices?status=2&sort=invoice_total`

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** <br />

  ```json
  {
  "message": "Successfully found invoices.",
  "data": [
    {
      "id": "150",
      "invoice_id": "RX1150",
      "name": "Holly-anne Crothers",
      "due": "2021-12-30",
      "invoice_total": "4485.93",
      "status": "1",
      "status_name": "Paid"
    },
    {
      "id": "149",
      "invoice_id": "RX1149",
      "name": "Joeann Riccardini",
      "due": "2022-03-01",
      "invoice_total": "1427.09",
      "status": "3",
      "status_name": "Cancelled"
    }
  ]
  }
  ```

* **Error Response:**

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message": "Invalid sort parameter", "data": []}`

  * **Code:** 500 SERVER ERROR <br />
    **Content:** `{"message": "Unexpected error", "data": []}`

### Return specific invoice by ID

* **URL**

  /invoices/{id}

* **Method:**

  `GET`

* **URL Params**

  There are no URL params

  **Example:**

  `/invoices/150`

  * **Success Response:**

    * **Code:** 200 <br />
      **Content:** <br />

    ```json
    {
    "message": "Successfully found invoice.",
    "data": {
      "id": "150",
      "invoice_id": "RX1150",
      "name": "Holly-anne Crothers",
      "street_address": "47 Eagle Crest Point",
      "city": "Imatra",
      "created": "2021-11-30",
      "due": "2021-12-30",
      "invoice_total": "4485.93",
      "paid_to_date": "4485.93",
      "status": "1",
      "status_name": "Paid",
      "details": [
      {
        "description": "Duis bibendum.",
        "quantity": "15",
        "rate": "542",
        "total": "8130.00"
      },
      {
        "description": "Donec posuere metus vitae ipsum.",
        "quantity": "8",
        "rate": "260",
        "total": "2080.00"
      }
      ]
    }
    }
    ```

* **Error Response:**

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message":"No invoice found with id: 189","data":[]}`

  * **Code:** 500 SERVER ERROR <br />
    **Content:** `{"message": "Unexpected error", "data": []}`


### Create new invoices

* **URL**

  /invoices

* **Method:**

  `POST`

* **URL Params**

  There are no URL params,

* **Body Data**

  ```json
  {
    "client": 1,
    "total": 1500,
    "details": [
        {
            "quantity": 1,
            "rate": 1500,
            "total": 1500,
            "description": "Optional text field"
        }
    ]
  }
  ```

* **Example:**

  `/invoices`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** `{"message":"Successfully created new invoice.","data":{"invoice_id":"RX1307","id":"307"}}`

* **Error Response:**

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message": "Invalid invoice data.", "data": []}`

  * **Code:** 500 SERVER ERROR <br />
    **Content:** `{"message": "Unable to create invoice, check the DB as it may have stored part of the new invoice.", "data": []}`

* **Code:** 500 SERVER ERROR <br />
  **Content:** `{"message": "Unexpected error.", "data": []}`

### Update invoice to mark as paid

* **URL**

  /invoices/{id}

* **Method:**

  `PUT`

* **URL Params**

  There are no URL params

  **Example:**

  `/invoices/150`

  * **Success Response:**

    * **Code:** 200 <br />
      **Content:** `{"message":"Successfully marked invoice as paid.","data":[]}`

* **Error Response:**

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message":"No invoice found with id: 189","data":[]}`

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message":"Invalid invoice ID","data":[]}`

  * **Code:** 500 SERVER ERROR <br />
    **Content:** `{"message": "Unexpected error", "data": []}`

### Cancel invoice

* **URL**

  /invoices/{id}

* **Method:**

  `DELETE`

* **URL Params**

  There are no URL params

  **Example:**

  `/invoices/150`

  * **Success Response:**

    * **Code:** 200 <br />
      **Content:** `{"message":"Successfully cancelled invoice.","data":[]}`

* **Error Response:**

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message":"No invoice found with id: 189","data":[]}`

  * **Code:** 400 BAD REQUEST <br />
    **Content:** `{"message":"Invalid invoice ID","data":[]}`

  * **Code:** 500 SERVER ERROR <br />
    **Content:** `{"message": "Unexpected error", "data": []}`

### Return all clients

* **URL**

  /clients

* **Method:**

  `GET`

* **URL Params**

  There are no URL params

* **Example:**

  `/clients`

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** <br />

  ```json
  {
  "message": "Successfully found clients.",
  "data": [
    {
      "id": "1",
      "name": "Portia Jirick",
      "street_address": "69 Summerview Circle",
      "city": "Dundburd"
    },
    {
      "id": "2",
      "name": "Brendan Moxley",
      "street_address": "9 Corben Way",
      "city": "Lughaye"
    }
  ]
  }
  ```

* **Error Response:**

  * **Code:** 500 SERVER ERROR <br />
    **Content:** `{"message": "Unexpected error", "data": []}`
