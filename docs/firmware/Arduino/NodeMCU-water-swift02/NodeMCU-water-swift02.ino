/*
Автор: Сергей Галочкин
email: decole@rambler.ru
Данный скетч для NodeMCU.
*/

#include <ESP8266WiFi.h>
#include <PubSubClient.h>

#define RELAY1 D1
#define RELAY2 D2
#define RELAY3 D3
#define RELAY4 D4
#define RELAY5 D5
#define RELAY6 D6
#define RELAY7 D7
#define RELAY8 D8

const char* ssid = "WIFI";
const char* password = "password";
const char* mqtt_server = "192.168.1.5";

WiFiClient espClient;
PubSubClient client(espClient);

char msg[50];
long lastMsg = 0;
int value = 0;

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
  byte* p = (byte*)malloc(length);
  // Copy the payload to the new buffer
  memcpy(p,payload,length);
  //client.publish("outTopic", p, length);

  if (strcmp(topic,"water/relay1")==0){
    if(p[0] == '0') {
      Serial.println("relay1-off");
      digitalWrite(RELAY1, HIGH);  
    }
    else if(p[0] == '1') {
      Serial.println("relay1-on");
      digitalWrite(RELAY1, LOW);  
    }
  }
  
  if (strcmp(topic,"water/relay2")==0){
    if(p[0] == '0') {
      Serial.println("relay2-off");
      digitalWrite(RELAY2, HIGH);     
    }
    else if(p[0] == '1') {
      Serial.println("relay2-on");
      digitalWrite(RELAY2, LOW); 
    }
  }
  
  if (strcmp(topic,"water/relay3")==0){
    if(p[0] == '0') {
      Serial.println("relay3-off");
      digitalWrite(RELAY3, HIGH); 
    }
    else if(p[0] == '1') {
      Serial.println("relay3-on");
      digitalWrite(RELAY3, LOW); 
    }
  }
  
  if (strcmp(topic,"water/relay4")==0){
    if(p[0] == '0') {
      Serial.println("relay4-off");
      digitalWrite(RELAY4, HIGH); 
    }
    else if(p[0] == '1') {
      Serial.println("relay4-on");
      digitalWrite(RELAY4, LOW); 
    }
  }
  
  if (strcmp(topic,"water/relay5")==0){
    if(p[0] == '0') {
      Serial.println("relay5-off");
      digitalWrite(RELAY5, HIGH); 
    }
    else if(p[0] == '1') {
      Serial.println("relay5-on");
      digitalWrite(RELAY5, LOW); 
    }
  }
  
  if (strcmp(topic,"water/relay6")==0){
    if(p[0] == '0') {
      Serial.println("relay6-off");
      digitalWrite(RELAY6, HIGH); 
    }
    else if(p[0] == '1') {
      Serial.println("relay6-on");
      digitalWrite(RELAY6, LOW); 
    }
  }

  if (strcmp(topic,"water/relay7")==0){
    if(p[0] == '0') {
      Serial.println("relay7-off");
      digitalWrite(RELAY7, HIGH); 
    }
    else if(p[0] == '1') {
      Serial.println("relay7-on");
      digitalWrite(RELAY7, LOW); 
    }
  }

  if (strcmp(topic,"water/relay8")==0){
    if(p[0] == '0') {
      Serial.println("relay8-off");
      digitalWrite(RELAY8, HIGH); 
    }
    else if(p[0] == '1') {
      Serial.println("relay8-on");
      digitalWrite(RELAY8, LOW); 
    }
  }
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "WaterCLS02-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str()), "esp", "esp99669966q") {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("outTopic", "water controller 2 start");
      // ... and resubscribe
      
      client.subscribe("water/relay1");
      client.subscribe("water/relay2");
      client.subscribe("water/relay3");
      client.subscribe("water/relay4");
      client.subscribe("water/relay5");
      client.subscribe("water/relay6");
      client.subscribe("water/relay7");
      client.subscribe("water/relay8");
      
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

  pinMode(RELAY1, OUTPUT);
  pinMode(RELAY2, OUTPUT);
  pinMode(RELAY3, OUTPUT);
  pinMode(RELAY4, OUTPUT);
  pinMode(RELAY5, OUTPUT);
  pinMode(RELAY6, OUTPUT);
  pinMode(RELAY7, OUTPUT);
  pinMode(RELAY8, OUTPUT);
  
  digitalWrite(RELAY1, HIGH);
  digitalWrite(RELAY2, HIGH);
  digitalWrite(RELAY3, HIGH);
  digitalWrite(RELAY4, HIGH);
  digitalWrite(RELAY5, HIGH);
  digitalWrite(RELAY6, HIGH);
  digitalWrite(RELAY7, HIGH);
  digitalWrite(RELAY8, HIGH);
    
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
    client.publish("water/check/relay1",  String(!digitalRead(RELAY1)).c_str(), true);
    client.publish("water/check/relay2",  String(!digitalRead(RELAY2)).c_str(), true);
    client.publish("water/check/relay3",  String(!digitalRead(RELAY3)).c_str(), true);
    client.publish("water/check/relay4",  String(!digitalRead(RELAY4)).c_str(), true);
    client.publish("water/check/relay5",  String(!digitalRead(RELAY5)).c_str(), true);
    client.publish("water/check/relay6",  String(!digitalRead(RELAY6)).c_str(), true);
    client.publish("water/check/relay7",  String(!digitalRead(RELAY7)).c_str(), true);
    client.publish("water/check/relay8",  String(!digitalRead(RELAY8)).c_str(), true);
             
    lastMsg = now;
  }
}
