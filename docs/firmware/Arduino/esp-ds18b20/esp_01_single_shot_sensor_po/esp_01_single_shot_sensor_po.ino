/*

  ESP-01 Single Shot Sensor (Pushover)

  Device sends a Pushover update via WiFi when power
  is connected. This could be via a magnetic switch.

  The Sketch :
  - Imports secrets.h with your details
  - Imports device.h with your device details
  - Connects to WiFi
  - Sends a message to Pushover
  - Goes into deep sleep

  It will stay in deep sleep until the switch disconnects the
  power.

  Matt Hawkins
  https://www.tech-spy.co.uk/
  25/03/2019

*/

#include <ESP8266WiFi.h>

/* Update these files with your details      */
#include "secrets.h"
#include "device.h"

/* WiFi details from secrets.h               */
const char* ssid = WIFI_SSID;
const char* password = WIFI_PASSWD;

/* Pushover details from secrets.h           */
const char* host = PUSHO_HOST;
const char* user = PUSHO_USER;
const char* token = PUSHO_TOKEN;

/* Sensor details from device.h              */
const char* device = PUSHO_DEVICE;
const char* message = PUSHO_MESSAGE;

void setup() {

  /* Serial connection */
  Serial.begin(115200);
  delay(200);
  Serial.println("ESP-01 Single Shot Sensor (Pushover)");
  Serial.print("");

  /* Connect to WiFi */
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  /* Connect to Pushover */
  Serial.print("connecting to ");
  Serial.println(host);

  WiFiClient client;
  const int httpPort = 80;
  if (!client.connect(host, httpPort)) {
    Serial.println("connection failed");
    return;
  } else {
    Serial.println("connection success");
  }

  /* Send message to Pushover */
  String content = String("");
  content = content + "title=" + device + "&token=" + token + "&user=" + user + "&message=" + message;

  Serial.println("Sending Pushover notification ...");
  Serial.println(content);
  client.print(String("POST ") + "/1/messages.json" + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n" +
               "Content-Type: application/x-www-form-urlencoded\r\n" +
               "Content-Length: " + content.length() + "\r\n\r\n" +
               content + "\r\n");

  /* Collect HTML response from Pushover */
  String fullresponse = "";
  while (client.connected())
  {
    while (client.available())
    {
      char ch = client.read();
      fullresponse += ch;
    }
  }
  client.stop();

  /* Check response was OK */
  String htmlresponse = "";
  if (fullresponse.length() >= 15) {
    htmlresponse = fullresponse.substring(0, 15);
    if (htmlresponse == "HTTP/1.1 200 OK") {
      Serial.println("Pushover message sent");
    } else {
      Serial.println("Pushover message failed");
    }
  }

  /* Put device into deep sleep.
     Although without hardware mod it will never wake up from this!
     3600 seconds = 60 minutes */
  Serial.println("Time to sleep!");
  ESP.deepSleep(0);

}

void loop() {

}
