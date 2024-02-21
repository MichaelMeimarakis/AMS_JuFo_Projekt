// Für eine einfachere De­mons­t­ra­ti­on und Präsentation wird keine Zeitbestimmung verwendet
// Dafür wird ein Knopf verwendet, der das erreichen spezifischer zeitlicher Momente simuliert
// Um das Programm mit Zeitbestimmung zu verwenden, muss die nächste Zeile auskommentiert werden und der Arduino richtig mit einem DS1307 RTC Modul verbunden werden

//#define USING_TIME_DET


/// Erfassung für das Programm wichtige Libraries 

#ifdef USING_TIME_DET
#include <RTClib.h>
#endif

// Struct zur Anfrage and Speicherung der Server-Daten
struct ServerData {
  ServerData() : _morgen(0), _mittag(0), _abend(0) {};
  void Anfrage() {    
    // Afrage an den Server
    // Da kein Server benutzt wird, werden die Daten als Beispiel ausgewählt
    _morgen = 00 * 60 + 37;
    _mittag = 13 * 60 + 30;
    _abend = 20 * 60 + 45;
  }
  int _morgen, _mittag, _abend;
  
};
ServerData sd;

/// Deklaration und Initialisierung von Variablen, die die Pin-Werte enthalten

// Mit den Shift-Register verbundene Pins (Datasheet der Shift-Register: https://www.ti.com/lit/ds/scls041j/scls041j.pdf) */
// D1 and D2: Die Pins, die mit dem SER-Eingang von je einem der beiden Shift-Register verbunden sind
// S: Der Pin, der mit dem SRCLK-Eingang beider Shift-Register verbunden ist
// R: Der Pin, der mit dem RCLK-Eingang beider Shift-Register verbunden ist
const unsigned int D1 = 8;
const unsigned int D2 = 9;
const unsigned int S = 10;
const unsigned int R = 11;

// Mit denm Ultraschall-Sensor verbundene Pins (Datasheet des Ultraschall-Sensors: https://cdn.sparkfun.com/datasheets/Sensors/Proximity/HCSR04.pdf)
// echo: Der Pin, der mit den Echo-Eingang des Ultraschall-Sensors verbunden ist
// trig: Der Pin, der mit den Trig-Eingang des Ultraschall-Sensors verbunden ist
const unsigned int echo = 7;
const unsigned int trig = 6;

// Für die Zeitbestimmung wichtige Pins und Variablen
#ifndef USING_TIME_DET
// Der Pin, der mit den Knopf der zur Simulierung der Zeitbestimmung benutzt wird, verbunden ist
const unsigned int timeButton = 5;
#else
// Das Objekt, das sich mit dem DS1307 RTC Modul verbindet
//RTC_DS1307 rtc;
RTC_DS1307 rtc;
#endif

// Mit dem Buzzer verbundenen Pin
const unsigned int buzzerPin = 4;

/// Andere wichtige Variablen

// Variable, die zur indentifizierung des nächsten Motors, der aktiviert werden soll, genutzt wird
unsigned short Motor = 0;


/// Setup Funktion
void setup() {
  // Einstellung des Pin-Modus aller notwendigen Pins
  pinMode(D1, OUTPUT);
  pinMode(D2, OUTPUT);
  pinMode(S, OUTPUT);
  pinMode(R, OUTPUT);

  pinMode(echo, INPUT);
  pinMode(trig, OUTPUT);

#ifndef USING_TIME_DET
  pinMode(timeButton, INPUT);
#endif

  // Initialisierung der seriellen Kommunikation
  Serial.begin(9600);
  Serial.write("Beginn\n");

#ifdef USING_TIME_DET
  // Initialisierung der Verbindung zum
  if (!rtc.begin()) {
    Serial.println("RTC konnte nicht gefunden werden.\n");
    while (1);
  }

  pinMode(buzzerPin, OUTPUT);
#endif
}


/// Funktion zum rotieren der Motoren
// m: Motor der rotiert werden soll (zwischen 0 und 3)
// r: Anzahl der Rotationsabschnitte (in 45 Grad Rotationen)
void RotateBy(int m, unsigned int r) {
  // Auswahl und Speicherung des Pins der benutzt werden soll abhängig von des ausgewählten Motors
  // Dabei wird zwischen den globalen Variablen "D1" und "D2" entschieden
  unsigned int D = m >= 2 ? D2 : D1;

  // Auswahl der Programm-Abschnitts der benutzt werden soll, abhänging mit welchen Ausgängen des Shift Registers der ausgewählte Motor verbunden ist (bzw. die ersten oder letzten vier)
  if ((m % 2) == 0) {
    for (int i = 0; i < r; i++)
      for (int j = 0; j < 64; j++) {
        digitalWrite(D, HIGH);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(D, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
      }
  } else {
    for (int i = 0; i < r; i++)
      for (int j = 0; j < 64; j++) {
        digitalWrite(D, HIGH);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(D, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
        digitalWrite(S, HIGH);
        digitalWrite(S, LOW);
        delay(3);
        digitalWrite(R, HIGH);
        digitalWrite(R, LOW);
      }
  }
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
  digitalWrite(S, HIGH);
  digitalWrite(S, LOW);
}


/// Main Loop Funktion
void loop() {
  if(rtc.now().hour() * 60 + rtc.now().minute() == 0)sd.Anfrage();

  // Testen, ob ein Breakpoint erreicht wurde 
#ifndef USING_TIME_DET
  if (digitalRead(timeButton)) {
#else
  if (rtc.now().hour() * 60 + rtc.now().minute() > (Motor == 0 ? sd._morgen : Motor == 1 ? sd._mittag : sd._abend)) {
#endif
    // Motor rotieren
    RotateBy(Motor, 1);

    // Output durch die Serielle Konsole
    Serial.write("Rotating ");
    Motor == 0 ? Serial.write("0") : Motor == 1 ? Serial.write("1") : Serial.write("2");
    Serial.write("\n");

    // Motor Variable anpassen, sodass nächter zu rotierener Motor identifiziert werden kann
    Motor += (Motor >= 2) ? -2 : 1;
    
    delay(500);
    
    digitalWrite(buzzerPin, HIGH);
    delay(2000);
    digitalWrite(buzzerPin, LOW);
    
#ifdef USING_TIME_DET
    // Speichern der Zeit vor dem benutzen des Ultraschall Sensors
    DateTime now = rtc.now();
#endif
    // Loop zum Testen mit den Ultraschall Sensor
    for (long distance = []() -> long {
      // Zurücksetzung des Unltraschall Sensors
      digitalWrite(trig, LOW); delayMicroseconds(2);

      // Unltraschall-Signal schicken
      digitalWrite(trig, HIGH); delayMicroseconds(10); digitalWrite(trig, LOW);

      // Ausgabe und Speicherung der Distanz vor dem Sensor
      return pulseIn(echo, HIGH) / 29 / 2;
    }();;) 
    {
      // Testen, ob ein signifikanter Unterschied in der jetzigen Distanz vor dem Sensor im Vergleich zu am Anfang gemessen Wert vorgefunden werden kann
      if ([=]() -> bool {
        // Zurücksetzung des Unltraschall Sensors
        digitalWrite(trig, LOW); delayMicroseconds(2);

        // Unltraschall-Signal schicken
        digitalWrite(trig, HIGH); delayMicroseconds(10); digitalWrite(trig, LOW);

        // Distanz vor dem Sensor berechen
        long d = pulseIn(echo, HIGH) / 29 / 2;

        // Ausgabe
        return (abs(d - distance) > 2 && d < 100);
      }()) {
        // Wenn ein signifikanter Unterschied zwischen der jetzigen und am Anfang gemessenen Distanz vorgefunden werden kann, werden die nächten Zeilen ausgeführt
        Serial.write("Success\n");
        break;
        // Testen, ob eine angegebene Zeitspanne seit das Testen mit dem Ultraschall begonnen hat, vergangen ist
#ifndef USING_TIME_DET
      } else if (digitalRead(timeButton)) {
#else
      // Hier wird als Zeitspanne 10 Minuten benutzt
      } else if ((rtc.now() - now).minutes() >= 10) {
#endif
        // Wenn die angegebene Zeitspanne vergangen ist, werden die nächsten Zeilen ausgeführt
        Serial.print("Failure");
        digitalWrite(buzzerPin, HIGH);
        delay(2000);
        digitalWrite(buzzerPin, LOW);
        break;
      }
      delay(1000);
    }
  }
  delay(10);
}
