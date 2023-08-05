#define relay 5                 // реле к выводу 5
#define analogPin A0            // пин к делителю напряжения
int val = 0;                       // переменная АЦП от 0 до 1023 в зависимости , от настройки делителя
int Vfire=200;                     // порго уровня сработки датчика (выставить экспериметальным путем) 
boolean fire=false;                // флаг пожара
unsigned long previousMillis = 0;

 
void setup()
{
  Serial.begin(9600);          //  настройка последовательного соединения
  pinMode(relay, OUTPUT);      // переключаем цифровой вывод в режим выхода
  delay(2000);
}
 
void loop()
{
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= 1000) {
  previousMillis = currentMillis;
  val = analogRead(analogPin);    // считываем напряжение с аналогового входа
  Serial.print("val = ");
  Serial.println(val);
  Serial.print("Fire flag is ");            // наблюдаем считанное значение
  Serial.println(fire);
  }
  
  if (val <= Vfire){
    fire=true;
    Serial.println("FIRE !!!");
    } 

  if (fire == true)  {
  digitalWrite(relay, HIGH);   // включаем 
  delay(1000);                  // ждем 1 секунду
  digitalWrite(relay, LOW);    // выключаем 
  delay(1000);                  // ждем 1 секунду
  }
}
