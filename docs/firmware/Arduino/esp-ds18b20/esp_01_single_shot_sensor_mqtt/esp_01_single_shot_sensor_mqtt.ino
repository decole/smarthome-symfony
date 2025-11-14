/*

  ESP-01 Single Shot Sensor (MQTT)

  Device sends a single MQTT update via WiFi when power
  is connected. This could be via a magnetic switch.

  The Sketch :
  - Imports secrets.h with your details
  - Imports device.h with your device details
  - Connects to WiFi
  - Publishes a single message via MQTT
  - Goes into deep sleep

  It will stay in deep sleep until the switch disconnects the
  power.

  Requires PubSubClient library:
  https://pubsubclient.knolleary.net/api.html

  Matt Hawkins
  https://www.tech-spy.co.uk/
  25/03/2019

*/

#include <ESP8266WiFi.h>
#include <PubSubClient.h>

/* Update these files with your details      */
#include "secrets.h"
#include "device.h"

/* WiFi details from secrets.h               */
const char* ssid = WIFI_SSID;
const char* password = WIFI_PASSWD;

/* MQTT broker details from secrets.h        */
const int mqtt_port = MQTT_PORT;
const char* mqtt_server = MQTT_SERVER;
const char *mqtt_user = MQTT_USER;
const char *mqtt_pass = MQTT_PASSWD;

/* MQTT device details from device.h         */
const char *mqtt_client_name = MQTT_CLIENT_NAME;
const char *mqtt_topic = MQTT_TOPIC;
const char *mqtt_message = MQTT_MESSAGE;
const char *mqtt_message_lw = MQTT_LAST_WILL;

WiFiClient wifiClient;
PubSubClient client(wifiClient);

void setup() {

  /* Serial connection */
  Serial.begin(115200);
  delay(200);
  Serial.println("");
  Serial.println("ESP-01 Single Shot Sensor (MQTT)");
  Serial.println("");

  /* Connect to WiFi */
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  /* Wait for connection */
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  /* Connect to MQTT broker */
  client.setServer(mqtt_server, mqtt_port);

  if (client.connect(mqtt_client_name, mqtt_user, mqtt_pass, mqtt_topic, 0, 1, mqtt_message_lw))
  {
    Serial.println("Publishing ...");
    if (client.publish(mqtt_topic, mqtt_message)) {
      Serial.println("Message published");
    } else {
      Serial.println("Error publishing message");
    }

  } else {
    Serial.println("Error connecting to MQTT broker");
  }

  /* Close MQTT client cleanly.
     Uncomment line below if you don't want the "Last Will" message to be used */
  /* client.disconnect(); */

  /* Close WiFi connection */
  wifiClient.stop();

  /* Put device into deep sleep.
     Although without hardware mod it will never wake up from this! */
  Serial.println("Time to sleep!");
  ESP.deepSleep(0);

}

void loop() {

}
