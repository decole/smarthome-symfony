/*
Автор: Сергей Галочкин
email: decole@rambler.ru

Данный скетч для NodeMCU.
Примерно каждые 5 секунд Ардуино отправляет данные на модуль
примерно такую сроку:
s1:0;s2:0;s3:0;
в скетче строка разделяется на ключ/значение и передается по 
топикам:
- holl/security     - холодная прихожка PIR-датчик
- margulis/security - пристройка PIR-датчик
- hall/security     - зал PIR-датчик

*/
#include <ESP8266WiFi.h>
#include <PubSubClient.h>

// Update these with values suitable for your network.

//const char* ssid = "WiFi-DOM.ru-5269";
//const char* password = "pSSff4bFZe";
//const char* mqtt_server = "192.168.0.6";

const char* ssid = "WIFI";
const char* password = "password";
const char* mqtt_server = "192.168.1.5";
const char* mqttUser = "node1";
const char* mqttPassword = "99669966q";
    
WiFiClient espClient;
PubSubClient client(espClient);
long lastMsg = 0;
char msg[50];

// begin string to array in loop
#define INPUT_SIZE 55
String request = "";

void setup() {
  pinMode(BUILTIN_LED, OUTPUT);     // Initialize the BUILTIN_LED pin as an output
  Serial.begin(115200);
  
  setup_wifi();
  client.setServer(mqtt_server, 1883); // 8083 1883 9001
  client.setCallback(callback);  
}

void setup_wifi() {

  delay(10);
  // We start by connecting to a WiFi network
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}

void callback(char* topic, byte* payload, unsigned int length) {
  
}

void reconnect() {  
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Attempt to connect
    if (client.connect("ESP8266Client")) {
      Serial.println("connected");
      // Once connected, publish an announcement...
      client.publish("outTopic", "hello world");
      // ... and resubscribe
      //client.subscribe("margulis/relay01");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      // Wait 5 seconds before retrying
      delay(5000);
    }
  }
}
void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();
  long now = millis();
  if (now - lastMsg > 10000) {
    if(Serial.available()>0){
      request = "";
      while (Serial.available() > 0){
        request = Serial.readStringUntil('\n');
      }
      Serial.println(request); // для отладки, убрать после
      
      // разбираем строку на параметры и значения
      int firstVal = 0; 
      int secondVal = 0;
      int separator;
      String s1,s2,s3,subrequest;
      // s1 start
      for (int i = 0; i < request.length(); i++) {
        if (request.substring(i, i+1) == ":") {
          firstVal = i;
        }
        if (request.substring(i, i+1) == "&") {
          secondVal = i; 
          break;
        }
        //Serial.println(i);
      }
      s1=request.substring(firstVal+1, secondVal);
      Serial.println("s1: "+s1);
      subrequest = request.substring(secondVal+1);
      
      // s2 start
      request = subrequest;
      for (int i = 0; i < request.length(); i++) {
        if (request.substring(i, i+1) == ":") {
          firstVal = i;
        }
        if (request.substring(i, i+1) == "&") {
          secondVal = i; 
          break;
        }
      }
     
      s2=request.substring(firstVal+1, secondVal);
      subrequest = request.substring(secondVal+1);
      
      // s3 start
      request = subrequest;
      for (int i = 0; i < request.length(); i++) {
        if (request.substring(i, i+1) == ":") {
          firstVal = i;
        }
        if (request.substring(i, i+1) == "&") {
          secondVal = i; 
          break;
        }
        //Serial.println(i);
      }
      s3=request.substring(firstVal+1, secondVal);
      
      char chars1[2];
      char chars2[2];
      char chars3[2];

      s1.toCharArray(chars1, s1.length());
      s2.toCharArray(chars2, s2.length());
      s3.toCharArray(chars3, s3.length());

      //push in mqtt server
      client.publish("holl/security", chars1);
      client.publish("margulis/security", chars2);
      client.publish("hall/security", chars3);

    }

    lastMsg = now;
    
  }
}
