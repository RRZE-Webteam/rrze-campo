# rrze-campo

- Liefert aufbereitete Daten zu Lehrveranstaltungen von Campo
- WordPress Plugin mit Shortcode, Gutenberg-ready, TinyMCE compatible, Widget (Block und Classic)
- Generiert im Fehlerfall Logs, die rrze-log nutzen kann
- Single site und multisite kompatibel
- Shortcodes sind abwärtskompatibel bis zur aktuellen Version von rrze-univis bei Lehrveranstaltungen
- Funktioniert auch bei Störungen der API zu Campo (Cache mit optionaler Ausgabe des Timestamps)

## Download
GITHub-Repo: https://github.com/RRZE-Webteam/rrze-campo

## Autor
RRZE-Webteam, http://www.rrze.fau.de

## Copryright
GNU General Public License (GPL) Version 3

## Verwendung

als Shortcode:

[campo view="" id/name/lecturerID="" show="" hide=""]

Parameter:

|Parameter|Plichtfeld|Werte|Default|Beispiele|
|-|-|-|-|-|
|**view**|ja|derzeit nur dieser Wert: lecture|
|**id**|nein|Die ID der Lehrveranstaltung: durch Komma getrennte Zahlen||"123, 987" oder "456"|
|**name**|nein|Der Name des/der DozentIn: durch Komma getrennt: Nachname, Vorname||"Mustermann, Manfred" oder "Musterfrau, Monika"|
|**lecturerID**|nein|Die ID der/des DozentIn: durch Komma getrennte Zahlen||"123, 987" oder "456"|
|**show**|nein|durch Komma getrennte Werte: sprungmarken, ics, telefon, mobile, fax, url, address, office, call||"ics, tel" oder "address"|
|**hide**|nein|mit Komma getrennte Werte: sprungmarken, ics, telefon, mobile, fax, url, address, office, call||"tel, fax" oder "office"|
|**sem**|nein|Zahl oder Jahreszahl mit Semesterkürzel||"-2" oder "1" oder "2022s"|
|**order**|nein|durch Komma getrennte Berufsbezeichnungen oder Lehrveranstaltungstypen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**hstart**|nein|Zahl von 1 bis 6|2|"2" oder "4"|
|**nodata**|nein|Eine beliebige Zeichenkette|No matching entries found.|Es wurden keine Lehrveranstaltungen gefunden.|

