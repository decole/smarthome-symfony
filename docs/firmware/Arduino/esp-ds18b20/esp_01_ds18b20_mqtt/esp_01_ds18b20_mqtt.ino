/*

  ESP-01 DS18B20 Sensor (MQTT)

  Device sends MQTT temperature update via WiFi.
  Deepsleep is unused to put the device to sleep and
  wake it every 15 minutes.

  Assume ESP-01 has been modified to allow waking
  from deepslep.

  The Sketch :
  - Imports secrets.h with your details
  - Imports device.h with your device details
  - Connects to WiFi
  - Reads temperature from DS18B20
  - Publishes a single message via MQTT
  - Goes into deep sleep for a period of time

  ---------------------------------------

  Requires libraries:

  PubSubClient
  https://pubsubclient.knolleary.net/api.html

  OneWire
  https://github.com/PaulStoffregen/OneWire/archive/master.zip

  DallasTempSensors
  https://github.com/milesburton/Arduino-Temperature-Control-Library/archive/master.zip

  For information on installing libraries :
  https://www.arduino.cc/en/Guide/Libraries

  ---------------------------------------

  Matt Hawkins
  https://www.tech-spy.co.uk/
  06/04/2019

*/

#include <ESP8266WiFi.h>
#include <PubSubClient.h>

/* Required for the DS18B20                  */
#include <OneWire.h>
#include <DallasTemperature.h>

/* Update these files with your details      */
#include "config.h"

// DS18B20 Data wire is conntected to digital pin 2
#define ONE_WIRE_BUS 2

WiFiClient wifiClient;
PubSubClient client(wifiClient);

// Create a OneWire instance
OneWire oneWire(ONE_WIRE_BUS);

// Pass OneWire reference to Dallas Temperature sensor
DallasTemperature sensors(&oneWire);

int connect() {

  int counter = 0;
  int result = 0;

  Serial.print("Connecting to ");
  Serial.println(WIFI_SSID);

  /* Set to station mode and connect to network */
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWD);

  /* Wait for connection */
  while ((WiFi.status() != WL_CONNECTED) && (counter < RETRY_COUNT)) {
    counter++;
    Serial.print(".");
    delay(500);
  }
  Serial.println("");

  /* Check if we obtained an IP address */
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Failed to connect. Sleeping!");
    result = 0;
  } else {
    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println("IP address: ");
    Serial.println(WiFi.localIP());
    result = 1;
  }

  return result;

}

void setup() {

  int result = 0;
  int counter = 0;

  /* Serial connection */
  Serial.begin(115200);

  /* Start Dallas library */
  sensors.begin();

  delay(200);
  Serial.println("");
  Serial.println("ESP-01 Temperature Sensor (MQTT)");
  Serial.println("");

  /* Get ChipID and convert to char array */
  char strChipID[33];
  itoa(ESP.getChipId(),strChipID,10);

  /* Concatenate to form MQTT topic */
  String strTopic=MQTT_TOPIC_PREFIX;
  strTopic=strTopic+"ESP"+strChipID;

  /* Convet to char array */
  char charTopic[strTopic.length()+1];
  strTopic.toCharArray(charTopic,strTopic.length()+1);
  Serial.println(charTopic);

  /* Attempt to connect to WiFi */
  result = connect();

  if (result == 1) {

    /* Read temps from all devices on one wire bus */
    sensors.requestTemperatures();

    /* Read temp from sensor as float.
       Convert into string of format "-##.##" */
    float fTempC = sensors.getTempCByIndex(0);

    if (fTempC != DEVICE_DISCONNECTED_C) {
      /* Valid reading */
      char strTempC[7];
      dtostrf(fTempC, 6, 2, strTempC);

      /* Connect to MQTT broker */
      client.setServer(MQTT_SERVER, MQTT_PORT);

      if (client.connect(strChipID, MQTT_USER, MQTT_PASSWD))
      {
        Serial.println("Publishing ...");
        if (client.publish(charTopic, strTempC, 1)) {
          Serial.println("Message published");
        } else {
          Serial.println("Error publishing message");
        }

      } else {
        Serial.println("Error connecting to MQTT broker");
      }

      /* Close MQTT client cleanly */
      client.disconnect();

    }
  }
  /* Close WiFi connection */
  wifiClient.stop();

  /* Put device into deep sleep.
     Although without hardware mod it will never wake up from this! */
  Serial.println("Time to sleep!");
  ESP.deepSleep(DEVICE_SLEEP * 60 * 1000000);

}

void loop() {

}
