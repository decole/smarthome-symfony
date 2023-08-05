# Monitoring smart home controllers

We monitor MQTT controllers by monitoring one controller topic.

The controller sends its state periodically, the service is connected to the mqtt listener.

Stores the latest published topic of the observed controller.

If necessary, notifies the user if the controller does not push data for more than a certain time.

Controller data is stored in the database once a minute. The site receives the latest cached data on controllers
or data from the database if there is no cache for this controller.


## What will it look like

Tab on the site "Setting PLC"

When we enter, a panel of controllers will be displayed with a column for the time of the last action, the time of the closest to
our time the date and time of the published topic.


## Settings

- controller
    - observation topic - recorded manually
    - alert flag
    - time of silence after which an alert will occur


---

A worker that caches data on controllers at startup.

We take the cached fresh data from the DeviceCacheService, match the mqtt topic and store it in the cache.
