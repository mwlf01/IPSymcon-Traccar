# Traccar für IP-Symcon

[![IP-Symcon Version](https://img.shields.io/badge/IP--Symcon-8.1+-blue.svg)](https://www.symcon.de)
[![Lizenz: EUPL-1.2](https://img.shields.io/badge/Lizenz-EUPL--1.2-blue.svg)](LICENSE)

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

- IP-Symcon 8.1 oder höher
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
- Server-Verbindungskonfiguration (Host, Port, HTTPS, TLS-Zertifikatsprüfung)
- API-Token-Authentifizierung mit Session-Cookie inkl. automatischer Sitzungserneuerung bei Ablauf
- Konfigurierbares Aktualisierungsintervall (Standard: 30 Sekunden)
- Verbindungstest

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
   - **TLS-Zertifikat prüfen**: Server-Zertifikat validieren (empfohlen; nur für selbstsignierte Setups abschalten)
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
Erzwingt das Anlegen einer neuen Sitzung (nützlich, wenn das Cookie außerhalb des regulären Pollings ungültig geworden ist). Der Splitter erneuert die Sitzung zusätzlich automatisch, sobald ein Request HTTP 401 zurückliefert.

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
- Bei selbstsignierten Zertifikaten **TLS-Zertifikat prüfen** deaktivieren
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

### Version 1.2.0
- **Lizenz**: Wechsel von MIT auf EUPL v. 1.2. Der neue Lizenztext liegt in der `LICENSE`; jede Modul-Datei enthält zusätzlich einen SPDX-Header.
- **Authentifizierung**: Session-Cookie-Modell beibehalten für volle Kompatibilität. Bei HTTP 401 erneuert der Splitter die Sitzung automatisch, mehrere `Set-Cookie`-Header werden korrekt zusammengeführt. Der Token wird nicht mehr ins Debug-Log geschrieben.
- **Geräte-Status**: Geräte-Instanzen abonnieren via `MessageSink` die Status-Änderungen des Splitters (`IM_CHANGESTATUS` und `IPS_KERNELSTARTED`). Behebt die falsche Meldung *Keine Traccar Splitter Instanz verbunden* auf Symcon 9.0+, wo `HasActiveParent()` innerhalb von `ApplyChanges` nicht zuverlässig ist, weil der Parent asynchron etabliert wird.
- **Sicherheit**: API-Token, Session-Cookies und vollständige API-Antworten werden nicht mehr ins Debug-Log geschrieben; nur HTTP-Code und Payload-Größe.
- **Sicherheit**: Neue Option **TLS-Zertifikat prüfen** (Standard an) ersetzt die zuvor hart deaktivierte Zertifikatsprüfung.
- **Filter-Präzision**: Der Receive-Data-Filter im Device-Modul ist nun begrenzt, so dass Geräte-ID `1` nicht mehr fälschlich auf `10`, `100`, `11` etc. matcht.
- **Selbstheilung**: Nach einem erfolgreichen API-Call wird der Status `Aktiv` automatisch wiederhergestellt, ohne dass der Nutzer eingreifen muss.
- **Parent-Erkennung**: Geräteinstanzen zeigen jetzt korrekt den Status `Keine übergeordnete Instanz` — gesteuert über den tatsächlichen Splitter-Status statt eines einmaligen Checks während `ApplyChanges`.
- **Geofence-Cache**: Geofence-Liste wird pro Splitter 5 Minuten lang zwischengespeichert — weniger API-Last.
- **RequestUpdate**: Der Button *Jetzt aktualisieren* delegiert nun an den Splitter — konsistente Daten (inklusive Geofence-Namen) und kein doppelter Update-Pfad mehr.
- **Konfigurator**: Formular öffnet sofort, wenn der Splitter inaktiv ist — mit Hinweis statt 30-Sekunden-Block.
- **HTTP-Timeouts**: Standard-Request-Timeout von 30 s auf 15 s reduziert, zusätzlich 5 s Connect-Timeout.
- **Batteriespannung**: Wird nur geschrieben, wenn die Tracker-Hardware sowohl `battery` als auch `batteryLevel` liefert (übliche Konvention), ersetzt die bisherige `< 50`-Heuristik.
- **Kraftstoffstand**: Liest nur noch `fuelLevel` (Prozent); das mehrdeutige `fuel`-Attribut (oft Liter) wird ignoriert, um falsche Einheiten zu vermeiden.
- **Alarm**: Wird zurückgesetzt, wenn kein Alarm mehr gemeldet wird — statt den alten Wert zu behalten.
- **Status-Normalisierung**: `Status`-Variable wird immer in Kleinbuchstaben gespeichert; doppelte Darstellungs-Optionen entfernt.

### Version 1.1.1
- Warnung behoben, wenn keine übergeordnete Instanz bei RequestUpdate verbunden ist

### Version 1.1.0
- Separate Variablen für Geofence-Namen und Geofence-IDs hinzugefügt
- Variablen für Gerätezeit und Serverzeit hinzugefügt
- Variablen für Kontakt und Deaktiviert hinzugefügt
- Verbesserte Variablenanordnung in logischen Gruppen
- Verbesserte deutsche Übersetzungen
- Code-Bereinigung und Optimierungen

### Version 1.0.0
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
- [Symcon Community](https://community.symcon.de/) – Benutzer: **mwlf**
- [Traccar Dokumentation](https://www.traccar.org/documentation/)

---

## Lizenz

Dieses Projekt steht unter der **European Union Public Licence (EUPL) v. 1.2** — siehe die [LICENSE](LICENSE)-Datei für den vollständigen Lizenztext.

Die EUPL ist eine Copyleft-Lizenz: abgeleitete Werke, die weitergegeben werden, müssen ebenfalls unter der EUPL oder einer kompatiblen Lizenz veröffentlicht werden (z. B. GPL, AGPL, MPL, LGPL — die vollständige Kompatibilitätsliste steht im Anhang der EUPL). Frühere Releases bis Version 1.1.1 bleiben weiterhin unter der zuvor genutzten MIT-Lizenz verfügbar.

Die EUPL wird in 24 offiziellen Sprachfassungen veröffentlicht, die rechtlich alle gleichwertig sind. Die Lizenz kann in anderen Sprachen auf der [offiziellen EU-Seite](https://interoperable-europe.ec.europa.eu/collection/eupl/eupl-text-eupl-12) eingesehen werden.

---

## Autor

**mwlf01**

- GitHub: [@mwlf01](https://github.com/mwlf01)
- Symcon Community: [mwlf](https://community.symcon.de/)

---

## Haftungsausschluss

Dieses Projekt ist eine inoffizielle Drittanbieter-Integration und steht in keiner Verbindung zu [Traccar](https://www.traccar.org/) oder dessen Entwicklern und wird von diesen weder unterstützt noch empfohlen.

**Traccar** ist eine Marke von Anton Tananaev. Dieses Modul verwendet die öffentlich verfügbare Traccar REST-API und enthält keinen Traccar-Quellcode.

Traccar ist lizenziert unter der [Apache License 2.0](https://github.com/traccar/traccar/blob/master/LICENSE.txt).
