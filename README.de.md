# Traccar für IP-Symcon

[![IP-Symcon Version](https://img.shields.io/badge/IP--Symcon-7.0+-blue.svg)](https://www.symcon.de)
[![Lizenz](https://img.shields.io/badge/Lizenz-MIT-green.svg)](LICENSE)

Eine IP-Symcon Modulbibliothek zur Integration des [Traccar](https://www.traccar.org/) GPS-Tracking-Servers über dessen REST-API.

**[English Version](README.md)**

---

## Inhaltsverzeichnis

- [Funktionen](#funktionen)
- [Voraussetzungen](#voraussetzungen)
- [Installation](#installation)
- [Modulübersicht](#modulübersicht)
  - [Traccar Splitter](#traccar-splitter)
  - [Traccar Konfigurator](#traccar-konfigurator)
  - [Traccar Gerät](#traccar-gerät)
- [Konfiguration](#konfiguration)
- [Variablen](#variablen)
- [PHP-Funktionen](#php-funktionen)
- [Lizenz](#lizenz)

---

## Funktionen

- **Vollständige Traccar API-Integration**: Verbindung zu jedem Traccar-Server (selbst gehostet oder Cloud)
- **API-Token-Authentifizierung**: Sichere Authentifizierung über Traccar API-Token
- **Geräteerkennung**: Automatische Erkennung aller Geräte von Ihrem Traccar-Server
- **Echtzeit-Positionsdaten**: Verfolgen Sie Breitengrad, Längengrad, Höhe, Geschwindigkeit, Kurs und mehr
- **Gerätestatus**: Überwachen Sie den Online/Offline-Status der verfolgten Geräte
- **Erweiterte Attribute**: Akkustand, Bewegungserkennung, Zündungsstatus, Kilometerstand, Betriebsstunden und mehr
- **Adress-Geocoding**: Anzeige der von Traccar ermittelten Adressen
- **Konfigurierbare Variablen**: Wählen Sie pro Gerät, welche Datenpunkte angezeigt werden
- **Deutsche Lokalisierung**: Vollständige deutsche Übersetzung für Benutzeroberfläche und Variablen

---

## Voraussetzungen

- IP-Symcon 7.0 oder höher
- Traccar Server (selbst gehostet oder [Traccar Abonnement](https://www.traccar.org/product/tracking-server/))
- Netzwerkzugriff auf Ihren Traccar-Server

---

## Installation

### Über den Module Store (Empfohlen)

1. IP-Symcon Konsole öffnen
2. Navigieren Sie zu **Module** > **Module Store**
3. Suchen Sie nach "Traccar"
4. Klicken Sie auf **Installieren**

### Manuelle Installation via Git

1. IP-Symcon Konsole öffnen
2. Navigieren Sie zu **Module** > **Module**
3. Klicken Sie auf **Hinzufügen** (Plus-Symbol)
4. Wählen Sie **Modul von URL hinzufügen**
5. Geben Sie ein: `https://github.com/mwlf01/IPSymcon-Traccar.git`
6. Klicken Sie auf **OK**

### Manuelle Installation (Dateikopie)

1. Klonen oder laden Sie dieses Repository herunter
2. Kopieren Sie den Ordner in Ihr IP-Symcon Modulverzeichnis:
   - Windows: `C:\ProgramData\Symcon\modules\`
   - Linux: `/var/lib/symcon/modules/`
   - Docker: Prüfen Sie Ihr Volume-Mapping
3. Laden Sie die Module in der IP-Symcon Konsole neu

---

## Modulübersicht

Diese Bibliothek enthält drei Module, die zusammenarbeiten:

### Traccar Splitter

Das **Traccar Splitter** Modul verwaltet die Verbindung zu Ihrem Traccar-Server. Es übernimmt die Authentifizierung via API-Token und stellt den API-Zugriff für alle Kindmodule bereit.

**Funktionen:**
- Server-Verbindungskonfiguration (Host, Port, HTTPS)
- API-Token-Authentifizierung mit Session-Verwaltung
- Konfigurierbares Aktualisierungsintervall (Standard: 30 Sekunden)
- Verbindungstest und Session-Aktualisierung

### Traccar Konfigurator

Das **Traccar Konfigurator** Modul erkennt automatisch alle Geräte von Ihrem Traccar-Server und ermöglicht die Erstellung von Geräteinstanzen mit einem Klick.

**Funktionen:**
- Listet alle Geräte von Traccar auf
- Zeigt Gerätename, eindeutige ID, Status, Kategorie, Modell und letzte Aktualisierung
- Ein-Klick-Erstellung von Geräteinstanzen
- Erkennt bereits konfigurierte Geräte

### Traccar Gerät

Das **Traccar Gerät** Modul repräsentiert ein einzelnes verfolgtes Gerät und zeigt dessen Positions- und Statusdaten an.

**Funktionen:**
- Echtzeit-Positionsverfolgung (Breitengrad, Längengrad, Höhe)
- Geschwindigkeits- und Kursinformationen
- Anzeige der ermittelten Adresse
- Akkustand- und Spannungsüberwachung
- Bewegungs- und Zündungserkennung
- Kilometerstand, Wegstrecke und Gesamtstrecke
- Betriebsstunden-Erfassung
- GPS-Qualität (Satelliten, HDOP, Genauigkeit)
- Konfigurierbare Variablensichtbarkeit

---

## Konfiguration

### Schritt 1: Traccar Splitter Instanz erstellen

1. Navigieren Sie zu **Objekte** > **Objekt hinzufügen** > **Instanz**
2. Suchen Sie nach "Traccar Splitter"
3. Klicken Sie auf **OK**
4. Konfigurieren Sie:
   - **Traccar Server Host**: Hostname oder IP Ihres Traccar-Servers (z.B. `demo.traccar.org`)
   - **Port**: API-Port (Standard: 443 für HTTPS)
   - **HTTPS verwenden**: Aktivieren für sichere Verbindungen (empfohlen)
   - **API Token**: Erstellen Sie einen Token in Traccar unter Einstellungen → Konto → Token
   - **Aktualisierungsintervall**: Wie oft nach Updates gefragt wird (Standard: 30 Sekunden)
5. Klicken Sie auf **Verbindung testen** zur Überprüfung
6. Klicken Sie auf **Übernehmen**

### Schritt 2: Traccar Konfigurator Instanz erstellen

1. Navigieren Sie zu **Objekte** > **Objekt hinzufügen** > **Instanz**
2. Suchen Sie nach "Traccar Configurator"
3. Klicken Sie auf **OK**
4. Der Konfigurator verbindet sich automatisch mit der Traccar Splitter Instanz
5. Eine Liste aller Geräte wird angezeigt

### Schritt 3: Geräteinstanzen hinzufügen

1. Finden Sie im Traccar Konfigurator das gewünschte Gerät
2. Klicken Sie auf den **+** Button neben dem Gerät
3. Die Geräteinstanz wird automatisch mit allen Einstellungen erstellt

### Schritt 4: Gerätevariablen konfigurieren (Optional)

1. Öffnen Sie die erstellte Traccar Gerät Instanz
2. Unter **Angezeigte Variablen** aktivieren/deaktivieren Sie die gewünschten Datenpunkte
3. Klicken Sie auf **Übernehmen**

---

## Variablen

Jede Traccar Gerät Instanz kann folgende Variablen erstellen:

| Variable | Typ | Beschreibung |
|----------|-----|--------------|
| **Status** | String | Online/Offline Status |
| **Letzte Aktualisierung** | Integer | Unix-Zeitstempel der letzten Positionsaktualisierung |
| **Breitengrad** | Float | GPS-Breitengrad in Grad |
| **Längengrad** | Float | GPS-Längengrad in Grad |
| **Höhe** | Float | Höhe in Metern |
| **Adresse** | String | Ermittelte Adresse |
| **Geschwindigkeit** | Float | Aktuelle Geschwindigkeit in km/h |
| **Kurs** | Float | Richtung/Peilung in Grad |
| **Geofence** | String | Namen der Geofences, in denen sich das Gerät befindet |
| **Geofence-IDs** | String | IDs der Geofences, in denen sich das Gerät befindet |
| **Genauigkeit** | Float | GPS-Genauigkeit in Metern |
| **Position gültig** | Boolean | Ob GPS-Fix gültig ist |
| **Satelliten** | Integer | Anzahl der GPS-Satelliten |
| **HDOP** | Float | Horizontale Positionsgenauigkeit |
| **Protokoll** | String | Tracking-Protokoll (z.B. osmand, teltonika) |
| **Gerätezeit** | Integer | Zeitstempel vom Gerät |
| **Serverzeit** | Integer | Zeitstempel vom Server |
| **Akku** | Integer | Akkustand in Prozent (für Handys) |
| **Batteriespannung** | Float | Batteriespannung in V (für GPS-Tracker) |
| **Laden** | Boolean | Ob das Gerät geladen wird |
| **Externe Spannung** | Float | Externe Versorgungsspannung in V |
| **Bewegung** | Boolean | Ob sich das Gerät bewegt |
| **Zündung** | Boolean | Fahrzeug-Zündungsstatus |
| **Alarm** | String | Alarmstatus |
| **Gesamtstrecke** | Float | Gesamtfahrstrecke in km |
| **Kilometerstand** | Float | Geräte-Kilometerstand in km |
| **Wegstrecke** | Float | Aktuelle Fahrstrecke in km |
| **Betriebsstunden** | Float | Motor-Betriebsstunden |
| **Kraftstoffstand** | Float | Kraftstoffstand in Prozent |
| **Signalstärke** | Integer | RSSI-Signalstärke in dBm |
| **Aktivität** | String | Aktivitätszustand (still, walking, etc.) |
| **Kategorie** | String | Gerätekategorie |
| **Modell** | String | Gerätemodell |
| **Telefon** | String | Zugehörige Telefonnummer |
| **Kontakt** | String | Kontaktinformationen |
| **Deaktiviert** | Boolean | Ob das Gerät in Traccar deaktiviert ist |

---

## PHP-Funktionen

### Traccar Splitter

#### TestConnection
Testet die Verbindung zum Traccar-Server.

```php
bool TRACCAR_TestConnection(int $InstanceID);
```

#### GetDevices
Ruft alle Geräte vom Traccar-Server ab.

```php
array TRACCAR_GetDevices(int $InstanceID);
```

#### GetPositions
Ruft aktuelle Positionen für alle Geräte ab.

```php
array TRACCAR_GetPositions(int $InstanceID);
```

#### GetDevicePosition
Ruft die Position für ein bestimmtes Gerät ab.

```php
array TRACCAR_GetDevicePosition(int $InstanceID, int $DeviceID);
```

#### GetGeofences
Ruft alle Geofences vom Server ab.

```php
array TRACCAR_GetGeofences(int $InstanceID);
```

#### GetServerInfo
Ruft Traccar-Serverinformationen ab.

```php
array TRACCAR_GetServerInfo(int $InstanceID);
```

#### UpdateDevices
Löst ein Update für alle Geräteinstanzen aus.

```php
void TRACCAR_UpdateDevices(int $InstanceID);
```

#### RefreshSession
Aktualisiert die API-Session (verwenden bei Verbindungstimeout).

```php
bool TRACCAR_RefreshSession(int $InstanceID);
```

### Traccar Gerät

#### RequestUpdate
Fordert ein sofortiges Positionsupdate für dieses Gerät an.

```php
void TRACCARDEV_RequestUpdate(int $InstanceID);
```

#### GetPosition
Ruft die aktuellen Positionsdaten als Array ab.

```php
array TRACCARDEV_GetPosition(int $InstanceID);
```

**Beispiel:**
```php
$position = TRACCARDEV_GetPosition(12345);
echo "Breitengrad: " . $position['latitude'];
echo "Längengrad: " . $position['longitude'];
echo "Geschwindigkeit: " . $position['speed'] . " km/h";
```

#### GetRawAttributes
Ruft alle rohen Positionsattribute als Array ab.

```php
array TRACCARDEV_GetRawAttributes(int $InstanceID);
```

---

## Fehlerbehebung

### Verbindung fehlgeschlagen
- Überprüfen Sie, ob Hostname und Port korrekt sind
- Prüfen Sie, ob HTTPS für Ihren Server erforderlich ist
- Stellen Sie sicher, dass Ihr API-Token gültig und nicht abgelaufen ist
- Klicken Sie auf "Session aktualisieren" in der Splitter-Konfiguration
- Testen Sie die API direkt: `https://ihr-server/api/server`

### Geräte werden nicht angezeigt
- Stellen Sie sicher, dass die Traccar Splitter Instanz verbunden ist (grüner Status)
- Prüfen Sie, ob Ihr Benutzer die Berechtigung hat, Geräte in Traccar anzuzeigen
- Klicken Sie auf "Geräteliste aktualisieren" im Konfigurator

### Position wird nicht aktualisiert
- Überprüfen Sie das Aktualisierungsintervall in den Traccar Splitter Einstellungen
- Stellen Sie sicher, dass das Gerät Daten an Traccar sendet
- Prüfen Sie die Variable "Letzte Aktualisierung" für die letzte bekannte Positionszeit

---

## Änderungsprotokoll

### Version 1.1.0 (17.01.2026)
- Separate Variablen für Geofence-Namen und Geofence-IDs hinzugefügt
- Variablen für Gerätezeit und Serverzeit hinzugefügt
- Variablen für Kontakt und Deaktiviert hinzugefügt
- Verbesserte Variablenanordnung in logischen Gruppen
- Verbesserte deutsche Übersetzungen
- Code-Bereinigung und Optimierungen

### Version 1.0.0 (11.01.2026)
- Erstveröffentlichung
- Traccar Splitter Modul für Serververbindung mit Session-basierter Authentifizierung
- Traccar Konfigurator für Geräteerkennung
- Traccar Gerät für Positionsverfolgung mit umfangreicher Attributunterstützung
- API-Token-Authentifizierung
- Vollständige deutsche Lokalisierung

---

## Support

Bei Problemen, Funktionswünschen oder Beiträgen besuchen Sie bitte:
- [GitHub Repository](https://github.com/mwlf01/IPSymcon-Traccar)
- [GitHub Issues](https://github.com/mwlf01/IPSymcon-Traccar/issues)
- [Traccar Dokumentation](https://www.traccar.org/documentation/)

---

## Lizenz

Dieses Projekt ist unter der MIT-Lizenz lizenziert - siehe die [LICENSE](LICENSE) Datei für Details.

---

## Autor

**mwlf01**

- GitHub: [@mwlf01](https://github.com/mwlf01)

---

## Haftungsausschluss

Dieses Projekt ist eine inoffizielle Drittanbieter-Integration und steht in keiner Verbindung zu [Traccar](https://www.traccar.org/) oder dessen Entwicklern und wird von diesen weder unterstützt noch empfohlen.

**Traccar** ist eine Marke von Anton Tananaev. Dieses Modul verwendet die öffentlich verfügbare Traccar REST-API und enthält keinen Traccar-Quellcode.

Traccar ist lizenziert unter der [Apache License 2.0](https://github.com/traccar/traccar/blob/master/LICENSE.txt).
