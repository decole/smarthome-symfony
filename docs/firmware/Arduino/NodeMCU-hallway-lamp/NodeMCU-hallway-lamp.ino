/*
Автор: Сергей Галочкин
email: decole@rambler.ru
Данный скетч для NodeMCU.
*/

#include <ESP8266WiFi.h>
#include <PubSubClient.h>

#define LAMP 4 //D2
#define DOOR1 D5 //D5 14
#define DOOR2 D6 //D6 12

const char* ssid = "WIFI";
const char* password = "password";
const char* mqtt_server = "192.168.1.5";

WiFiClient espClient;
PubSubClient client(espClient);

char msg[50];
long lastMsg = 0;
long lastMsg1 = 0;
int value = 0;
int valueCommandBlock = 0;
int door1State = 0;
int door2State = 0;
int door1StateOld = 0;
int door2StateOld = 0;

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
  
  if(topic = "hallway/lamp01"){
    if(String(topicValue).indexOf("on") >= 0) {
//      Serial.println("r-on");
      digitalWrite(LAMP, LOW);
      topicValue = "";
      valueCommandBlock = 1;
    }
    else if(String(topicValue).indexOf("off") >= 0) {
//      Serial.println("r-off");
      digitalWrite(LAMP, HIGH);
      topicValue = "";
      valueCommandBlock = 0;
      value = 1;
    }
  }
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "HallwayLamp01-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str()), "esp", "esp99669966q") {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("outTopic", "hallway lamp start");
      // ... and resubscribe
      client.subscribe("hallway/lamp01");
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
  pinMode(DOOR1, INPUT);
  pinMode(DOOR2, INPUT);
  Serial.begin(115200);
  setup_wifi();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
  pinMode(LAMP,OUTPUT);
  digitalWrite(LAMP, HIGH);
  //digitalWrite(BUILTIN_LED, HIGH);
}

void loop() {

  if (!client.connected()) {
    reconnect();
  }

  client.loop();
  long now = millis();
  int offTimer = 8;
  
  if (now - lastMsg1 > 1000) {
    door1State = digitalRead(DOOR1);
    door2State = digitalRead(DOOR2);
    
    if (door1State == LOW) {
      value = offTimer;
      digitalWrite(LAMP, LOW);
      digitalWrite(BUILTIN_LED, LOW);
    }

    if (door2State == LOW) {
      value = offTimer;
      digitalWrite(LAMP, LOW);
      digitalWrite(BUILTIN_LED, LOW);
    }

    if (door1State != door1StateOld) {
      client.publish("hallway/check/door1",  String(!door1State).c_str(), true);
      door1StateOld = door1State;
      Serial.print("DOOR1 ");
      Serial.println(door1State);

      if (digitalRead(LAMP) == LOW) {
        value = offTimer;
      }
    }

    if (door2State != door2StateOld) {
      client.publish("hallway/check/door2",  String(!door2State).c_str(), true);
      door2StateOld = door2State;
      Serial.print("DOOR2 ");
      Serial.println(door2State);

      if (digitalRead(LAMP) == LOW) {
        value = offTimer;
      }
    }

    if (value == 1 && valueCommandBlock == 0 && door1State == HIGH && door2State == HIGH) {
      value = 0;
      digitalWrite(LAMP, HIGH);
      digitalWrite(BUILTIN_LED, HIGH);
    }

//    Serial.println(value);
    if (value > 0) {
      value = value - 1;
    }
    
    lastMsg1 = now;
  }

  if (now - lastMsg > 15000) {
    client.publish("hallway/check/lamp01",  String(!digitalRead(LAMP)).c_str(), true);
    client.publish("hallway/check/door1",  String(!door1State).c_str(), true);
    client.publish("hallway/check/door2",  String(!door2State).c_str(), true);
      
    lastMsg = now;
  }

}
