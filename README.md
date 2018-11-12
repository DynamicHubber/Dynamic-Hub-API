# Dynamic-Hub-API

Dynamic Hub API Examples

This repository contain's example PHP code to connect to the Dynamic Hub API.

To start with you need to get the following details from Dynamic Hub

* CLIENT_ID
* TOKEN_REQUEST_ENDPOINT
* REQUEST_ENDPOINT
* CLIENT_SECRET

These values will then need to be added to the top of the DynamicHubApiCall.php file as follows:

```
const CLIENT_ID = '';
const TOKEN_REQUEST_ENDPOINT = '';
const REQUEST_ENDPOINT = '';
const CLIENT_SECRET = '';
```

To send a PUT request with the latest statuses of orders use the following code:

```
<?php 

//Make the call to the Dynamic Hub API
//Valid statuses:
//paused, assigned, in_progress, in_qa, rejected, complete

include_once('DynamicHubApiCall.php');

$callData = [
                [
                    'ecomus_nucleus_entity_code' => 'workshop_wsitemstatus',
                    'ecomus_nucleus_job_id' => 1,
                    'order_id' => 'PTD-0001',
                    'order_item_index' => 1,
                    'product_name' => 'Test Product 1',
                    'status' => 'paused'
                ], [
                    'ecomus_nucleus_entity_code' => 'workshop_wsitemstatus',
                    'ecomus_nucleus_job_id' => 2,
                    'order_id' => 'PTD-0002',
                    'order_item_index' => 1,
                    'product_name' => 'Test Product 2',
                    'status' => 'complete'
                ]
            ];

try {
    $client = new DynamicHub_Client;
    $response = print_r($client->pushEntities($callData, "PUT"), true);
    echo "Successful call, response was {$response}\n";
} catch (Exception $e) {
    echo "Failed to make call, error was {$e->getMessage()}\n";
}

```

The http method are as follows:
* GET
* POST
* PUT
* DELETE
* PATCH

Here is a possible reponse from the above test API call (converted to an array from the returned JSON):
```
Array
(
    [0] => Array
        (
            [job_id] => 1
            [status] => success
            [error_description] => 
        )

    [1] => Array
        (
            [job_id] => 2
            [status] => success
            [error_description] => 
        )

)
```



If you would like more information on the product Dynamic Hub please visit our website at: https://www.dynamichub.co.uk/
