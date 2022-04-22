# rrze-campo

- Liefert aufbereitete Daten zu Lehrveranstaltungen und Mitarbeitern von Campo
- WordPress Plugin mit Shortcode, Gutenberg-ready, TinyMCE compatible, Widget (Block und Classic)
- Vereint die Funktionalität aller bisheriger Plugins, die UnivIS nutzen: rrze-univis, fau-person
- Aufbau orientiert sich am MVC-Modell (vor allem Datenaufbereitung ist nur an einer Stelle und gekapselt)
- Verwendet PHPUnit (jede Funktion ist gekapselt testbar; Test wird in Settings ein/ausgeschaltet)
- Generiert im Fehlerfall Logs, die rrze-log nutzen kann
- Single site und multisite kompatibel
- Shortcodes sind abwärtskompatibel bis zur aktuellen Version von rrze-univis und fau-person (Stand 01.04.2022)
- Ist auf hohe Performance ausgelegt (KISS, durchdachtes Caching, transient und reduced to the maximum code)
- Funktioniert auch bei Störungen der API zu Campo (Cache mit optionaler Ausgabe des Timestamps)
- Deaktiviert rrze-univis und fau-person, sobald diese ablösbar sind (ab V 0.3)
- Ist als Shortcode, Widget und Block verwendbar

## Download
GITHub-Repo: https://github.com/RRZE-Webteam/rrze-campo

## Autor
RRZE-Webteam, http://www.rrze.fau.de

## Copryright
GNU General Public License (GPL) Version 3

## Verwendung

als Shortcode:

[campo view="" id="" show="" hide=""]

Parameter:

|Parameter|Plichtfeld|Werte|Default|Beispiele|
|-|-|-|-|-|
|**view**|ja|einer der Werte: mitarbeiter-alle, mitarbeiter-einzeln, mitarbeiter-orga, mitarbeiter-telefonbuch, lehrveranstaltungen-alle, lehrveranstaltungen-einzeln, publikationen|mitarbeiter|"lehrveranstaltungen" oder "mitarbeiter-telefonbuch"|
|**id**|nein|durch Komma getrennte Zahlen||"123, 987" oder "456"|
|**show**|nein|durch Komma getrennte Werte: sprungmarken, ics, telefon, mobile, fax, url, address, office, call||"ics, tel" oder "address"|
|**hide**|nein|mit Komma getrennte Werte: sprungmarken, ics, telefon, mobile, fax, url, address, office, call||"tel, fax" oder "office"|
|**sem**|nein|Zahl oder Jahreszahl mit Semesterkürzel||"-2" oder "1" oder "2022s"|
|**show_jobs**|nein|durch Komma getrennte Berufsbezeichnungen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**hide_jobs**|nein|durch Komma getrennte Berufsbezeichnungen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**order**|nein|durch Komma getrennte Berufsbezeichnungen oder Lehrveranstaltungstypen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**since**|nein|Jahreszahl||"2020" oder "2022"|
|**hstart**|nein|Zahl von 1 bis 6|2|"2" oder "4"|



Parameter von rrze-univis können verwendet werden:

|Parameter|Werte|
|-|-|
|**task**|siehe view| 
|**univisid**|siehe id| 
|**number**|siehe id| 
|**id**|siehe id| 
|**name**|siehe name| 
|**dozent**|siehe name| 
|**lv_id**|siehe id| 
|**show**|siehe show| 
|**hide**|siehe hide| 
|**sem**|siehe hide| 
|**order**|siehe hide| 
|**since**|siehe since| 
|**lv_import**|fällt weg| 
|**hstart**|siehe hstart| 




