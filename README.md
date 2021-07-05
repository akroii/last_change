# last_update

## This is a fork of an old extension by tomkit.de taken from ER2


### Änderungsdatum oder Zeitspanne anzeigen.
Die neuen Insert-Tags funktionieren ähnlich wie last_update.
Die folgenden Zeiten können abgefragt werden:

`last_change_page` zeigt das Datum einer Seite.

`last_change_article` zeigt das Datum eines Artikels.

`last_change_ce` zeigt das Datum eines ContentElementes.

`last_change_news` zeigt das Datum eines News-Archives mit den enthaltenen News.

`last_change_events` zeigt das Datum eines Kalenders mit den enthaltenen Events.

`last_change_faqs` zeigt das Datum einer FAQ-Kategorie mit den enthaltenen FAQs.

Diese Parameter gibt es:

`all` zeigt das Datum inkl. der darin enthaltenen Abfragen bis hin zu news/events/faqs, falls vorhanden.

`date` zeigt das formatierte Datum.

`datetime` zeigt das formatierte Datum inkl. Uhrzeit.

`ago` zeigt die formatierte Zeitspanne.

### Beispiele:

```
{{last_change_page::2::all::datetime}}
{{last_change_page::2::all::ago}}
{{last_change_article::41::all::datetime}}
{{last_change_article::41::all::ago}}
{{last_change_ce::31::all::datetime}}
{{last_change_ce::31::all::ago}}
{{last_change_news::1::datetime}}
{{last_change_news::1::ago}}
{{last_change_events::2::datetime}}
{{last_change_events::2::ago}}
{{last_change_faqs::2::datetime}}
{{last_change_faqs::2::ago}}
```

Die Formatierungen können über das DCA angepasst werden:

```
$GLOBALS['TL_LANG']['last_change']['dateformat'] = 'd.m.Y';
$GLOBALS['TL_LANG']['last_change']['datetimeformat'] = 'd.m.Y - H:i';
$GLOBALS['TL_LANG']['last_change']['day'] = 'Tag';
$GLOBALS['TL_LANG']['last_change']['days'] = 'Tage';
$GLOBALS['TL_LANG']['last_change']['hour'] = 'Stunde';
$GLOBALS['TL_LANG']['last_change']['hours'] = 'Stunden';
$GLOBALS['TL_LANG']['last_change']['minute'] = 'Minute';
$GLOBALS['TL_LANG']['last_change']['minutes'] = 'Minuten';
```
