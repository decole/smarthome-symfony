/*
Автор: Сергей Галочкин
email: decole@rambler.ru
Данный скетч для NodeMCU.
*/

// #include <Arduino.h>
#include <ESP8266WiFi.h>
#include <PubSubClient.h>

/* Required for the DS18B20                  */
#include <OneWire.h>
#include <DallasTemperature.h>

// DS18B20 Data wire is conntected to digital pin 2
#define ONE_WIRE_BUS 2
//#define LAMP 0
// Update these with values suitable for your network.

const char* ssid = "wifi";
const char* password = "password";
const char* mqtt_server = "192.168.1.5";

WiFiClient espClient;
PubSubClient client(espClient);
long lastMsg = 0;
char msg[50];
int value = 0;

// Create a OneWire instance
OneWire oneWire(ONE_WIRE_BUS);

// Pass OneWire reference to Dallas Temperature sensor
DallasTemperature sensors(&oneWire);

void setup_wifi() {

  delay(10);
  // We start by connecting to a WiFi network
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  randomSeed(micros());

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

}

void callback(char* topic, byte* payload, unsigned int length) {  
  String topicValue = "";
  for (int i = 0; i < length; i++) {
    topicValue += (char)payload[i];
  }
  /*
  if(topic = "margulis/lamp01"){
    if(String(topicValue).indexOf("on") >= 0) {
      Serial.println("r-on");
      digitalWrite(LAMP, LOW);
      topicValue = "";      
    }
    else if(String(topicValue).indexOf("off") >= 0) {
      Serial.println("r-off");
      digitalWrite(LAMP, HIGH);
      topicValue = "";  
    }
  }
*/
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "ESP8266Client-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str()), "esp", "esp99669966q") {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("outTopic", "greenhouse temperature start
      ");
      // ... and resubscribe
      client.subscribe("server/relay01");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      // Wait 5 seconds before retrying
      delay(5000);
    }
  }
}

void setup() {
  pinMode(BUILTIN_LED, OUTPUT);     // Initialize the BUILTIN_LED pin as an output
  Serial.begin(115200);
  setup_wifi();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
  //pinMode(LAMP,OUTPUT);
  //digitalWrite(LAMP, HIGH);
  
  /* Start Dallas library */
  sensors.begin();
  
  /* Get ChipID and convert to char array */
  char strChipID[33];
  itoa(ESP.getChipId(),strChipID,10);
}

void loop() {

  if (!client.connected()) {
    reconnect();
  }

  client.loop();
  long now = millis();

  if (now - lastMsg > 15000) {    
    /* Read temps from all devices on one wire bus */
    sensors.requestTemperatures();
    /* Read temp from sensor as float.
       Convert into string of format "-##.##" */
    float fTempC = sensors.getTempCByIndex(0);
    
    /* Valid reading */
    char strTempC[7];
    dtostrf(fTempC, 6, 2, strTempC);
      
    client.publish("greenhouse/temperature", strTempC, 1);

    //client.publish("server/check/relay01",  String(!digitalRead(LAMP)).c_str(), true); // прихожка
    lastMsg = now;
  }

}
