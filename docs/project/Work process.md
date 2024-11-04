# How the system works

1. You need an mqtt message broker. Controllers connect to it and transfer their data
2. From the application side, a service written in go lang subscribes to topics, usually to everything (topic name `#`) and transfers all received data to the RabbitMQ message queue.
3. After the data is received from the message queue by a background process that processes the data and stores it in the Redis cache
    - Security system - if the entity is armed and triggered, a telegram message will be sent to all registered users in the system who have a telegram id in their accounts
    - Fire system - if the sensor is triggered, a telegram alert will be sent to all users
    - Sensors: - at an exit from a range of normal values - the notification.
        - Temperatures
        - Humidity
        - Pressure
        - Leaks
        - Dry contact
4. On the site, when viewing pages, you can configure the display of which sensors you want to show.
5. In the application, you can configure controllers with relays. And give commands to devices connected via mqtt.


![principle_of_working_project.drawio.png](../schemas%2Fprinciple_of_working_project.drawio.png)
