/*
Автор: Сергей Галочкин
email: decole@rambler.ru
Данный скетч для ESP8266 01S Relay.
*/

#include <ESP8266WiFi.h>
#include <PubSubClient.h>

#define RELAY 0

const char* ssid = "WIFI";
const char* password = "password";
const char* mqtt_server = "192.168.1.5";

WiFiClient espClient;
PubSubClient client(espClient);

long lastMsg = 0;
int value = 0;

void setup_wifi() {
  delay(10);

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
  
  if(topic = "server/fan-module"){
    if(String(topicValue).indexOf("on") >= 0) {
      digitalWrite(RELAY, LOW);
    }
    else if(String(topicValue).indexOf("off") >= 0) {
      digitalWrite(RELAY, HIGH);
    }

    client.publish("server/fan-module/status",  String(!digitalRead(RELAY)).c_str(), true);
  }
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "espfanmodule-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str()), "espfan", "99669966q") {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("server/fan-module/status", "start");
      // ... and resubscribe
      client.subscribe("server/fan-module");
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
  pinMode(RELAY,OUTPUT);
  digitalWrite(RELAY, HIGH);
  pinMode(BUILTIN_LED, OUTPUT);     // Initialize the BUILTIN_LED pin as an output
  digitalWrite(BUILTIN_LED, HIGH);
  Serial.begin(115200);
  setup_wifi();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }

  client.loop();

  long now = millis();
  
  if (now - lastMsg > 15000) {
    client.publish("server/fan-module/status",  String(!digitalRead(RELAY)).c_str(), true);
      
    lastMsg = now;
  }
}