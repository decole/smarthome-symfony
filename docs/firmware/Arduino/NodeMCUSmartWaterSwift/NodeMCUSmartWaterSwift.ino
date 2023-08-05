#include <ESP8266WiFi.h>
#include <PubSubClient.h>

// Update these with values suitable for your network.

const char* ssid = "WIFI";
const char* password = "PASSWORD";
const char* mqtt_server = "192.168.1.5";

WiFiClient espClient;
PubSubClient client(espClient);
long lastMsg = 0;
char msg[50];
int value = 0;

int Relay1 = 2;
/*int Relay2 = 3;
int Relay3 = 4;
int Relay4 = 5;
int WaterSensor = 8;
*/
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
  if(topic = "home/relay01"){
    if(String(topicValue).indexOf("on") >= 0) {
      Serial.println("r-on");
      topicValue = "";      
    }
    else if(String(topicValue).indexOf("off") >= 0) {
      Serial.println("r-off");
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
      client.publish("outTopic", "hello world");
      // ... and resubscribe
      client.subscribe("home/relay01");
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
  
  pinMode(Relay1, OUTPUT);
  /*
  pinMode(Relay2, OUTPUT);
  pinMode(Relay3, OUTPUT);
  pinMode(Relay4, OUTPUT);
  pinMode(WaterSensor, OUTPUT);
  */
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

  if (now - lastMsg > 5000) {
    digitalWrite(Relay1, !digitalRead(Relay1));
    /*
    digitalWrite(Relay2, !digitalRead(Relay2));
    digitalWrite(Relay3, !digitalRead(Relay3));
    digitalWrite(Relay4, !digitalRead(Relay4));
    Serial.println(digitalRead(WaterSensor));
    */
    lastMsg = now;
  }
}