#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <RTClib.h>

// =====================
// WIFI
// =====================
const char* WIFI_SSID = "ESP32TEST";
const char* WIFI_PASSWORD = "12345678";

// =====================
// MQTT MOSQUITTO
// =====================
const char* MQTT_HOST = "10.21.153.61";
const int MQTT_PORT = 1883;
const char* MQTT_CLIENT_ID = "esp32-greenhouse-01";
const char* MQTT_SENSOR_TOPIC = "agrovision/sensors";

// =====================
// TANAMAN
// =====================
const String TANAMAN = "Stroberi";

// =====================
// PIN ESP32 DEVKIT
// =====================
const int SOIL_PIN = 34;
const int TDS_PIN = 35;
const int LDR_PIN = 32;
const int PUMP_PIN = 26;

// =====================
// KALIBRASI SENSOR
// =====================
const int SOIL_WET_RAW = 1200;
const int SOIL_DRY_RAW = 3000;

const int LDR_BRIGHT_RAW = 0;
const int LDR_DARK_RAW = 4095;

int soilMin = 45;
int soilMax = 75;

int soilPumpOnBelow = 40;
int soilPumpOffAbove = 60;

RTC_DS3231 rtc;

WiFiClient espClient;
PubSubClient mqttClient(espClient);

unsigned long lastSend = 0;
const unsigned long SEND_INTERVAL = 6000;

bool pumpStatus = false;

int clampInt(int value, int minValue, int maxValue) {
  if (value < minValue) return minValue;
  if (value > maxValue) return maxValue;
  return value;
}

int mapPercent(int raw, int rawWetOrBright, int rawDryOrDark) {
  int percent = map(raw, rawDryOrDark, rawWetOrBright, 0, 100);
  return clampInt(percent, 0, 100);
}

String getWaktuSekarang() {
  if (rtc.lostPower()) {
    return "";
  }

  DateTime now = rtc.now();

  char buffer[25];
  sprintf(
    buffer,
    "%04d-%02d-%02d %02d:%02d:%02d",
    now.year(),
    now.month(),
    now.day(),
    now.hour(),
    now.minute(),
    now.second()
  );

  return String(buffer);
}

int bacaTdsPpm(int raw) {
  float voltage = raw * (3.3 / 4095.0);

  float tds = (133.42 * voltage * voltage * voltage
              - 255.86 * voltage * voltage
              + 857.39 * voltage) * 0.5;

  if (tds < 0) {
    tds = 0;
  }

  return (int)tds;
}

void konekWiFi() {
  Serial.print("Menghubungkan WiFi");

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int retry = 0;

  while (WiFi.status() != WL_CONNECTED && retry < 40) {
    delay(500);
    Serial.print(".");
    retry++;
  }

  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.print("WiFi terhubung. IP ESP32: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("WiFi gagal terhubung.");
  }
}

void konekMQTT() {
  while (!mqttClient.connected()) {
    Serial.print("Menghubungkan MQTT ke ");
    Serial.print(MQTT_HOST);
    Serial.print(":");
    Serial.println(MQTT_PORT);

    if (mqttClient.connect(MQTT_CLIENT_ID)) {
      Serial.println("MQTT terhubung.");
    } else {
      Serial.print("MQTT gagal. State: ");
      Serial.println(mqttClient.state());
      Serial.println("Coba lagi 3 detik...");
      delay(3000);
    }
  }
}

void publishDataSensor(
  int soilPercent,
  float suhu,
  int ldrPercent,
  int tdsPpm,
  bool pumpOn,
  String waktu
) {
  if (WiFi.status() != WL_CONNECTED) {
    konekWiFi();
  }

  if (!mqttClient.connected()) {
    konekMQTT();
  }

  StaticJsonDocument<512> doc;

  doc["plant"] = TANAMAN;
  doc["soil_humidity"] = soilPercent;
  doc["soil_temperature"] = suhu;
  doc["ldr_value"] = ldrPercent;
  doc["tds_value"] = tdsPpm;
  doc["pump_status"] = pumpOn;

  if (waktu != "") {
    doc["rtc_time"] = waktu;
    doc["recorded_at"] = waktu;
  }

  char payload[512];
  size_t payloadSize = serializeJson(doc, payload);

  Serial.print("MQTT publish topic: ");
  Serial.println(MQTT_SENSOR_TOPIC);

  Serial.print("Payload: ");
  Serial.println(payload);

  bool success = mqttClient.publish(MQTT_SENSOR_TOPIC, payload, payloadSize);

  if (success) {
    Serial.println("Data MQTT berhasil dikirim.");
  } else {
    Serial.println("Data MQTT gagal dikirim.");
  }
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  pinMode(PUMP_PIN, OUTPUT);
  digitalWrite(PUMP_PIN, LOW);

  analogReadResolution(12);

  Wire.begin();

  if (!rtc.begin()) {
    Serial.println("RTC DS3231 tidak terdeteksi.");
  } else {
    if (rtc.lostPower()) {
      Serial.println("RTC kehilangan daya, set waktu dari waktu compile.");
      rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
    }
  }

  konekWiFi();

  mqttClient.setServer(MQTT_HOST, MQTT_PORT);
  mqttClient.setBufferSize(512);

  konekMQTT();
}

void loop() {
  mqttClient.loop();

  if (millis() - lastSend < SEND_INTERVAL) {
    return;
  }

  lastSend = millis();

  int soilRaw = analogRead(SOIL_PIN);
  int tdsRaw = analogRead(TDS_PIN);
  int ldrRaw = analogRead(LDR_PIN);

  int soilPercent = mapPercent(soilRaw, SOIL_WET_RAW, SOIL_DRY_RAW);
  int ldrPercent = mapPercent(ldrRaw, LDR_BRIGHT_RAW, LDR_DARK_RAW);
  int tdsPpm = bacaTdsPpm(tdsRaw);

  float suhu = 25.0;

  if (!pumpStatus && soilPercent < soilPumpOnBelow) {
    pumpStatus = true;
  }

  if (pumpStatus && soilPercent > soilPumpOffAbove) {
    pumpStatus = false;
  }

  digitalWrite(PUMP_PIN, pumpStatus ? HIGH : LOW);

  String statusTanah = soilPercent < soilMin ? "KERING" : "NORMAL";
  String waktu = getWaktuSekarang();

  Serial.print("Waktu: ");
  Serial.print(waktu);
  Serial.print(" | Tanaman: ");
  Serial.print(TANAMAN);
  Serial.print(" | Soil raw: ");
  Serial.print(soilRaw);
  Serial.print(" | Soil: ");
  Serial.print(soilPercent);
  Serial.print("% | TDS raw: ");
  Serial.print(tdsRaw);
  Serial.print(" | TDS: ");
  Serial.print(tdsPpm);
  Serial.print(" ppm | LDR raw: ");
  Serial.print(ldrRaw);
  Serial