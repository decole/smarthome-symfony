#include <ESP8266WiFi.h>
#include <PubSubClient.h>

#define RELAY D1
#define PIR01 D2
#define DELIMETER 500

const char* ssid = "YOUR-WIFI-SSID";
const char* password = "WIFI-PASSWORD";
const char* mqtt_server = "192.168.1.5";

WiFiClient espClient;
PubSubClient client(espClient);

long lastMsg = 0;
long lastMsg1 = 0;
int analogLineState = 0;
int detectState = 0;
int state = 0;
int relayDelayTime = 0;

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
  
  if(topic = "firesecure/state"){
    digitalWrite(RELAY, HIGH);
    delay(1000);
    state = 0;
    detectState = 0;
    digitalWrite(RELAY, HIGH);
  }
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "secure-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str()), "esp", "esp99669966q") {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("firesecure", "firesecure start");
      // ... and resubscribe
      client.subscribe("firesecure/state");
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
  pinMode(RELAY, OUTPUT);
  digitalWrite(RELAY, HIGH);

  Serial.begin(9600);

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
  
  if (now - lastMsg1 > 1000) {
    int analogLine = analogRead(A0);

    if (DELIMETER > analogLine && state == 0) {
      relayDelayTime = relayDelayTime + 1;
      
      if (digitalRead(RELAY) == HIGH) {
        digitalWrite(RELAY, LOW);
        client.publish("firesecure/fire-sensor/relay", String(digitalRead(RELAY)).c_str(), true);
      }

      client.publish("firesecure/fire-sensor/analogLineValue", String(analogLine).c_str(), true);
    }

    if (relayDelayTime > 6) {
      digitalWrite(RELAY, HIGH);
      relayDelayTime = 0;
      detectState = detectState + 1;
    }

    if (detectState > 1)  {
      state = 1;
      digitalWrite(RELAY, LOW);
    }

    lastMsg1 = now;
  }

  if (now - lastMsg > 10000) {
    client.publish("firesecure/fire-sensor/state",           String(state).c_str(), true);
    client.publish("firesecure/fire-sensor/relay",           String(digitalRead(RELAY)).c_str(), true);
    client.publish("firesecure/fire-sensor/detectState",     String(detectState).c_str(), true);
    client.publish("firesecure/fire-sensor/analogLineValue", String(analogRead(A0)).c_str(), true);
    client.publish("secure/PIR01",                           String(digitalRead(PIR01)).c_str(), true);

    lastMsg = now;
  }

}
