# rrze-campo

Darstellung von Lehrveranstaltungen und organisatorischen Daten aus Campo.

(ersetzt und deaktiviert rrze-univis, d.h. bestehende Shortcodes werden von rrze-campo ausgeführt)

- nutzt API zu Campo
- Zwischenspeicher als Transients
- verwendet PHPUnit für Tests (https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
- gibt .ics aus
- ist als Shortcode, Widget und Block verwendbar

## Download
GITHub-Repo: https://gitlab.rrze.fau.de/rrze-webteam/rrze-jobs

## Autor
RRZE-Webteam , http://www.rrze.fau.de

## Copryright
GNU General Public License (GPL) Version 3

## Verwendung

als Shortcode:

[campo view="" id="" show="" hide=""]

Parameter:

|Parameter|Plichtfeld|Werte|Default|Beispiele|
|-|-|-|-|-|
|**view**|ja|einer der Werte: mitarbeiter-alle, 
mitarbeiter-einzeln, 
mitarbeiter-orga, 
mitarbeiter-telefonbuch, 
lehrveranstaltungen-alle, 
lehrveranstaltungen-einzeln, 
publikationen|mitarbeiter|"lehrveranstaltungen" oder "mitarbeiter-telefonbuch"|
|**id**|nein|durch Komma getrennte Zahlen||"123, 987" oder "456"|
|**show**|nein|durch Komma getrennte Werte: sprungmarken, 
ics, 
telefon, 
mobile, 
fax,
url,
address,
office,
call
||"ics, tel" oder "address"|
|**hide**|nein|mit Komma getrennte Werte: sprungmarken, 
ics, 
telefon, 
mobile, 
fax,
url,
address,
office,
call||"tel, fax" oder "office"|
|**sem**|nein|Zahl oder Jahreszahl mit Semesterkürzel||"-2" oder "1" oder "2022s"|
|**show_jobs**|nein|durch Komma getrennte Berufsbezeichnungen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**hide_jobs**|nein|durch Komma getrennte Berufsbezeichnungen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**order**|nein|durch Komma getrennte Berufsbezeichnungen oder Lehrveranstaltungstypen||"UnivIS-Beauftragter" oder "Webmaster, UnivIS-Beauftragter"|
|**since**|nein|Jahreszahl||"2020" oder "2022"|
|**hstart**|nein|Zahl von 1 bis 6|2|"2" oder "4"|



Parameter von rrze-univis können verwendet werden:

|Parameter|Werte|
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




