# Home Automation
This collection of scripts is focused on home automation devices. Currently they are sketches for use on the ESP-01 module.

## ESP_01_single_shot_mqtt
This device uses a standard ESP-01 module with no modifications. When power is applied the sketch connects to WiFi and sends a MQTT message to a MQTT broker. It then goes into deepsleep but without any hardware modifications it will not wake up until the power is disconnected and reapplied.

## ESP_01_single_shot_po
This device uses a standard ESP-01 module with no modifications. When power is applied the sketch connects to WiFi and sends a Pushover notification. It then goes into deepsleep but without any hardware modifications it will not wake up until the power is disconnected and reapplied.

## ESP_01_ds18b20_mqtt
This device reads a temperature from a DS18B20 sensor and sends the value via a MQTT message. It goes into deepsleep. When it wakes it goes through the process again. It is assumed the ESP-01 module has been modified to allow wake from deepsleep.
