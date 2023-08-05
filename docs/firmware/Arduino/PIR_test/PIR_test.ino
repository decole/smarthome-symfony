void setup() {
    Serial.begin(9600);
    pinMode(2, INPUT);
    pinMode(11, OUTPUT);

}

void loop(){
    int val = digitalRead(2);
    Serial.println(val);
    noTone(11);
    delay(1000);
    tone(11, 1000);
    delay(1000);
}
