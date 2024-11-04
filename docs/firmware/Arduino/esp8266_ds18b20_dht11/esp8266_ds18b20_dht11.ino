#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <OneWire.h>
#include <DHT.h>

const char* ssid = "WIFI";
const char* password = "password";
const char* mqtt_server = "192.168.1.5";

#define DHTPIN 1  //GPIO1 (Tx)
#define DHTTYPE  DHT11
#define DS18B20 2  //GPIO2

//DHT11 stuff
float temperature_buiten;
float temperature_buiten2;
DHT dht(DHTPIN, DHTTYPE, 15);

//DS18b20 stuff
OneWire  ds(DS18B20);  // on pin 2 

WiFiClient espClient;
PubSubClient client(espClient);

unsigned long lastMsg = 0;
#define MSG_BUFFER_SIZE  (50)
char msg[MSG_BUFFER_SIZE];

void setup_wifi() {
  delay(10);
  // We start by connecting to a WiFi network
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
  }

  randomSeed(micros());
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    // Create a random client ID
    String clientId = "ESP8266Client-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str())) {
      // Once connected, publish an announcement...
      client.publish("outTopic", "underflor");
    } else {
      client.state();
      // Wait 5 seconds before retrying
      delay(5000);
    }
  }
}

void setup() {
  pinMode(BUILTIN_LED, OUTPUT);
  digitalWrite(BUILTIN_LED, HIGH);
  setup_wifi();
  client.setServer(mqtt_server, 1883);
  dht.begin();
}

void loop() {

  if (!client.connected()) {
    reconnect();
  }
  client.loop();
  
  unsigned long now = millis();
  if (now - lastMsg > 15000) {
    lastMsg = now;
    
    //float humidity = dht.readHumidity();
    //float temp = dht.readTemperature();
    byte i;
    byte present = 0;
    byte type_s;
    byte data[12];
    byte addr[8];
    float celsius;

    //client.publish("underflor/dht-temperature", String(temp).c_str(), true);
    //client.publish("underflor/humidity", String(humidity).c_str(), true);
    
    if ( !ds.search(addr)) {
      ds.reset_search();
      delay(250);
      return;
    }
    
    if (OneWire::crc8(addr, 7) != addr[7]) {
      client.publish("prototip/error", "CRC is not valid!");
      return;
    }

    switch (addr[0]) {
      case 0x10:
        //Serial.println("  Chip = DS18S20");  // or old DS1820
        type_s = 1;
      break;
      case 0x28:
        //Serial.println("  Chip = DS18B20");
        type_s = 0;
      break;
      case 0x22:
        //Serial.println("  Chip = DS1822");
        type_s = 0;
        break;
      default:
        //Serial.println("Device is not a DS18x20 family device.");
        return;
    }
    
    ds.reset();
    ds.select(addr);
    ds.write(0x44, 1);        // start conversion, with parasite power on at the end
    
    delay(1000);     // maybe 750ms is enough, maybe not
    // we might do a ds.depower() here, but the reset will take care of it.
    
    present = ds.reset();
    ds.select(addr);
    ds.write(0xBE);         // Read Scratchpad
    
    //Serial.print("  Data = ");
    //Serial.print(present, HEX);
    //Serial.print(" ");
    for ( i = 0; i < 9; i++) {           // we need 9 bytes
      data[i] = ds.read();
      //Serial.print(data[i], HEX);
      //Serial.print(" ");
    }
    //Serial.print(" CRC=");
    //Serial.print(OneWire::crc8(data, 8), HEX);
    //Serial.println();
    OneWire::crc8(data, 8);
    
    int16_t raw = (data[1] << 8) | data[0];
    if (type_s) {
      raw = raw << 3; // 9 bit resolution default
      if (data[7] == 0x10) {
    // "count remain" gives full 12 bit resolution
        raw = (raw & 0xFFF0) + 12 - data[6];
      }
    } else {
      byte cfg = (data[4] & 0x60);
      // at lower res, the low bits are undefined, so let's zero them
      if (cfg == 0x00) raw = raw & ~7;  // 9 bit resolution, 93.75 ms
      else if (cfg == 0x20) raw = raw & ~3; // 10 bit res, 187.5 ms
      else if (cfg == 0x40) raw = raw & ~1; // 11 bit res, 375 ms
      //// default is 12 bit resolution, 750 ms conversion time
    }
    
    celsius = ((float)raw / 16.0) - 6.33;
        
    client.publish("underflor/temperature", String(celsius).c_str(), true);
  }
}
